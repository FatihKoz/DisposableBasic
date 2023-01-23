<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Aircraft;
use App\Models\User;
use App\Models\Enums\FuelType;
use App\Models\Enums\PirepState;
use App\Services\AirportService;
use App\Services\FinanceService;
use App\Support\Math;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DB_WidgetController extends Controller
{
    // Aircraft Transfer
    public function transferac(Request $request)
    {
        $user = User::with('journal')->find(Auth::id());
        $user_location = filled($user->curr_airport_id) ? $user->curr_airport_id : $user->home_airport_id;

        $current_page = $request->croute;
        $price = $request->price;
        $selected_ac = $request->ac_selection;
        $interim_price = ($request->interim_price == '1') ? true : false;

        // Aircraft NOT selected (abort)
        if (!$selected_ac) {
            flash()->error(__('DBasic::widgets.ta_err_ac'));

            return redirect(url($current_page));
        }

        $aircraft = Aircraft::with('subfleet')->where('id', $selected_ac)->first();

        // Transfer is free (Move asset, complete)
        if ($price === 'free') {
            $transfer_cost = 'FREE';
            $aircraft->airport_id = $user_location;
            $aircraft->save();
            flash()->success(__('DBasic::widgets.ta_ok_free', ['registration' => $aircraft->registration]));
            Log::info('Disposable Basic | Free Aircraft Transfer > ' . $aircraft->registration . ' moved to ' . $user_location . ' by ' . $user->name_private);

            return redirect(url($current_page));
        }

        // Transfer price is auto (Calculate cost, continue)
        if ($price === 'auto') {
            $ac_location = filled($aircraft->airport_id) ? $aircraft->airport_id : $aircraft->subfleet->hub_id;
            $base_airport = DB::table('airports')->where('id', $ac_location)->first();
            $dest_airport = DB::table('airports')->where('id', $user_location)->first();

            // Distance
            $AirportSvc = app(AirportService::class);
            $transfer_distance = $AirportSvc->calculateDistance($base_airport->id, $dest_airport->id);

            // Fuel Price
            if ($aircraft->subfleet->fuel_type === FuelType::LOW_LEAD) {
                $fuel_price = ($base_airport->fuel_100ll_cost > 0) ? $base_airport->fuel_100ll_cost : setting('airports.default_100ll_fuel_cost');
            } elseif ($aircraft->subfleet->fuel_type === FuelType::MOGAS) {
                $fuel_price = ($base_airport->fuel_mogas_cost > 0) ? $base_airport->fuel_mogas_cost : setting('airports.default_mogas_fuel_cost');
            } else {
                $fuel_price = ($base_airport->fuel_jeta_cost > 0) ? $base_airport->fuel_jeta_cost : setting('airports.default_jet_a_fuel_cost');
            }

            // Ground Handling Prices (with multiplier)
            $orig_gh = filled($base_airport->ground_handling_cost) ? $base_airport->ground_handling_cost : setting('airports.default_ground_handling_cost');
            $dest_gh = filled($dest_airport->ground_handling_cost) ? $dest_airport->ground_handling_cost : setting('airports.default_ground_handling_cost');
            $gh_multiplier = filled($aircraft->subfleet->ground_handling_multiplier) ? $aircraft->subfleet->ground_handling_multiplier . '%' : '100%';
            $gh_cost = Math::applyAmountOrPercent(round($orig_gh + $dest_gh, 2), $gh_multiplier);

            // Fuel Burn (per nm, with subfleet averages)
            $subfleet_members = DB::table('aircraft')->where('subfleet_id', $aircraft->subfleet_id)->pluck('id')->toArray();
            $total_fuelused = DB::table('pireps')->where('state', PirepState::ACCEPTED)->whereIn('aircraft_id', $subfleet_members)->sum('fuel_used');
            $total_distance = DB::table('pireps')->where('state', PirepState::ACCEPTED)->whereIn('aircraft_id', $subfleet_members)->sum('distance');
            if ($total_fuelused > 0 && $total_distance > 0) {
                $avrg_fuelburn = round($total_fuelused / $total_distance, 3);
            } else {
                $avrg_fuelburn = rand(5, 30);
            }

            // Fuel Cost
            $aprx_fuelburn = round($avrg_fuelburn * (string)$transfer_distance, 3);
            $fuel_cost = round($aprx_fuelburn * $fuel_price, 3);

            // Transfer Cost
            $transfer_cost = Money::createFromAmount(round($gh_cost + $fuel_cost, 2));
            Log::debug('Disposable Basic | Aircraft Transfer > Transfer Distance: ' . $transfer_distance);
            Log::debug('Disposable Basic | Aircraft Transfer > Fuel Price: ' . $fuel_price);
            Log::debug('Disposable Basic | Aircraft Transfer > Fuel Burn: ' . $avrg_fuelburn);
            Log::debug('Disposable Basic | Aircraft Transfer > Fuel Used: ' . $aprx_fuelburn);
            Log::debug('Disposable Basic | Aircraft Transfer > Fuel Cost: ' . $fuel_cost);
            Log::debug('Disposable Basic | Aircraft Transfer > GH Cost: ' . $gh_cost);
            Log::debug('Disposable Basic | Aircraft Transfer > Calculated Cost: ' . $transfer_cost);
        }

        // Transfer price is fixed (Define cost, continue)
        if (is_numeric($price)) {
            $transfer_cost = Money::createFromAmount($price);
            Log::debug('Disposable Basic | Aircraft Transfer > Fixed Cost: ' . $transfer_cost);
        }

        if ($interim_price === true) {
            flash()->info('Aprx. Transfer Cost: ' . $transfer_cost . ' | ' . $aircraft->registration);

            return redirect(url($current_page));
        }

        // Check User Balance (abort or continue)
        if ($transfer_cost > $user->journal->balance) {
            flash()->error(__('DBasic::widgets.ta_err_funds', ['price' => $transfer_cost]));

            return redirect(url($current_page));
        }

        // Balance OK (Debit from User, Credit to aircraft owner Airline)
        $financeSvc = app(FinanceService::class);

        $financeSvc->debitFromJournal(
            $user->journal,
            $transfer_cost,
            $user,
            'Aircraft Transfer ' . $aircraft->registration,
            'Aircraft Transfer',
            'actransfer',
            Carbon::now()->format('Y-m-d')
        );

        $financeSvc->creditToJournal(
            $aircraft->subfleet->airline->journal,
            $transfer_cost,
            $user,
            'Aircraft Transfer ' . $aircraft->registration . ' (' . $user->name_private . ')',
            'Aircraft Transfer',
            'actransfer',
            Carbon::now()->format('Y-m-d')
        );

        // Flash Message
        if ($price === 'auto') {
            flash()->success(__('DBasic::widgets.ta_ok_auto', ['registration' => $aircraft->registration, 'price' => $transfer_cost, 'distance' => $transfer_distance]));
        } else {
            flash()->success(__('DBasic::widgets.ta_ok_fixed', ['registration' => $aircraft->registration, 'price' => $transfer_cost]));
        }

        // Move Asset (complete)
        $aircraft->airport_id = $user_location;
        $aircraft->save();
        Log::info('Disposable Basic | Aircraft Transfer > ' . $aircraft->registration . ' moved to ' . $user_location . ' by ' . $user->name_private . '. Price: ' . $transfer_cost);

        return redirect(url($current_page));
    }

    // JumpSeat Travel
    public function jumpseat(Request $request)
    {
        $user = User::with('journal')->find(Auth::id());
        $user_location = isset($user->curr_airport_id) ? $user->curr_airport_id : $user->home_airport_id;

        $price = $request->price;
        $base_price = $request->basep;
        $current_page = $request->croute;
        $new_location = $request->newloc;
        $interim_price = ($request->interim_price == '1') ? true : false;

        // Destination Check (abort)
        if (!$new_location || $new_location == $user_location) {
            flash()->error(__('DBasic::widgets.js_err_dest'));

            return redirect(url($current_page));
        }

        // Transfer is free (Move asset, complete)
        if ($price === 'free') {
            $transfer_cost = 'FREE';
            $user->curr_airport_id = $new_location;
            $user->save();
            flash()->success(__('DBasic::widgets.js_ok_free', ['location' => $new_location]));

            return redirect(url($current_page));
        }

        // Transfer price is auto (Calculate cost, continue)
        if ($price === 'auto') {
            $AirportSvc = app(AirportService::class);
            $transfer_distance = $AirportSvc->calculateDistance($user_location, $new_location);
            $transfer_cost = Money::createFromAmount(round($base_price * (string)$transfer_distance, 2));
        }

        // Transfer price is fixed (Define Cost, continue)
        if (is_numeric($price)) {
            $transfer_cost = Money::createFromAmount($price);
        }

        if ($interim_price === true) {
            flash()->info('Aprx. Ticket Price: '. $transfer_cost . ' | ' . $new_location);

            return redirect(url($current_page));
        }

        // Check User Balance (abort or continue)
        if ($transfer_cost > $user->journal->balance) {
            flash()->error(__('DBasic::widgets.js_err_funds', ['price' => $transfer_cost]));

            return redirect(url($current_page));
        }

        // Balance OK (Debit from User, Credit to user Airline)
        $financeSvc = app(FinanceService::class);

        $financeSvc->debitFromJournal(
            $user->journal,
            $transfer_cost,
            $user,
            'JumpSeat Travel ' . $new_location,
            'JumpSeat Travel',
            'jumpseat',
            Carbon::now()->format('Y-m-d')
        );

        $financeSvc->creditToJournal(
            $user->airline->journal,
            $transfer_cost,
            $user,
            'JumpSeat Travel (' . $user->name_private . ')',
            'JumpSeat Travel',
            'jumpseat',
            Carbon::now()->format('Y-m-d')
        );

        // Flash Message
        if ($price === 'auto') {
            flash()->success(__('DBasic::widgets.js_ok_auto', ['location' => $new_location, 'price' => $transfer_cost, 'distance' => $transfer_distance]));
        } else {
            flash()->success(__('DBasic::widgets.js_ok_fixed', ['location' => $new_location, 'price' => $transfer_cost]));
        }

        // Move Asset (complete)
        $user->curr_airport_id = $new_location;
        $user->save();

        return redirect(url($current_page));
    }
}
