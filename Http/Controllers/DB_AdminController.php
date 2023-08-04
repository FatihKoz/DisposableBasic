<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\User;
use App\Models\UserAward;
use App\Models\Enums\UserState;
use App\Services\FinanceService;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Modules\DisposableBasic\Services\DB_FleetServices;

class DB_AdminController extends Controller
{
    public function index()
    {
        // Get settings (of Disposable Basic)
        $settings = DB::table('disposable_settings')->where('key', 'LIKE', 'dbasic.%')->get();
        // $settings = $settings->groupBy('group'); // This may be used to have all settings in one card like phpVMS core

        // Manual Awards and Bonus Payments
        $awards = DB::table('awards')->select('id', 'name')->orderBy('name')->get();
        $users = DB::table('users')->select('id', 'pilot_id', 'name')->where('state', UserState::ACTIVE)->orderBy('pilot_id')->get();

        return view('DBasic::admin.index', [
            'awards'   => $awards,
            'settings' => $settings,
            'users'    => $users,
        ]);
    }

    // Manual Awarding
    public function manual_award()
    {
        $formdata = Request::post();

        $user_id = $formdata['ma_user'];
        $award_id = $formdata['ma_award'];

        if ($user_id === 'ZZZ' || $award_id === 'ZZZ') {
            flash()->error('Check form entries !');

            return back()->withInput();
        }

        $award_check = UserAward::where(['user_id' => $user_id, 'award_id' => $award_id])->count();

        if ($award_check > 0) {
            flash()->info('User already owns this award.');
        } else {
            UserAward::create(['user_id' => $user_id, 'award_id' => $award_id]);
            flash()->success('User awarded');
        }

        return back()->withInput();
    }

    // Manual Payment
    public function manual_payment()
    {
        $formdata = Request::post();

        $user_id = $formdata['mp_user'];
        $amount = $formdata['mp_amount'];

        if ($user_id === 'ZZZ' || $amount == 0 || $amount < 0) {
            flash()->error('Check form entries !');

            return back()->withInput();
        }

        $user = User::with('journal', 'airline.journal')->where('id', $user_id)->first();
        $amount = Money::createFromAmount($amount);

        if (filled($user) && $user->airline->journal->balance > $amount) {
            // Payment Details
            $financeSvc = app(FinanceService::class);
            $group = 'Bonus Payments';
            $today = Carbon::now()->format('Y-m-d');

            // Credit User
            $financeSvc->creditToJournal(
                $user->journal,
                $amount,
                $user,
                'Bonus Payment',
                $group,
                'bonus',
                $today
            );
            // Debit Airline
            $financeSvc->debitFromJournal(
                $user->airline->journal,
                $amount,
                $user,
                'Bonus Payment (' . $user->name_private . ')',
                $group,
                'bonus',
                $today
            );

            flash()->success('Wire transfer completed. Amount: ' . $amount . ' > User: ' . $user->name_private);
            Log::info('Disposable Basic | Bonus Payment of ' . $amount . ' to ' . $user->name_private . ' completed by ' . Auth::user()->name_private);
        } else {
            flash()->error('Airline balance is NOT enough to complete wire transfer !');
            Log::info('Disposable Basic | Bonus Payment Result: Rejected, Not Enough Funds');
        }

        return back()->withInput();
    }

    // Module Settings
    public function settings_update()
    {
        $formdata = Request::post();
        $section = null;

        foreach ($formdata as $id => $value) {

            if ($id === 'group') {
                $section = $value;
            }

            $setting = DB::table('disposable_settings')->where('id', $id)->first();

            if (!$setting) {
                continue;
            }

            Log::debug('Disposable Basic, ' . $setting->group . ' setting for ' . $setting->name . ' changed to ' . $value);
            DB::table('disposable_settings')->where(['id' => $setting->id])->update(['value' => $value]);
        }

        flash()->success($section . ' settings saved.');
        return back(); // return redirect(route('DBasic.admin'));
    }

    // Park Stuck Aircraft
    public function park_aircraft()
    {
        $formdata = Request::post();
        $FleetSvc = app(DB_FleetServices::class);
        $result = $FleetSvc->ParkAircraft($formdata['aircraft_reg']);

        if ($result === 0) {
            flash()->error('Nothing Done... Aircraft Not Found or was already PARKED');
        } elseif ($result === 1) {
            flash()->success('Aircraft State Changed Back to PARKED');
        } elseif ($result === 2) {
            flash()->success('Aircraft State Changed Back to PARKED and Pirep CANCELLED');
        }

        return back(); // return redirect(route('DBasic.admin'));
    }

    // Database Checks
    public function health_check()
    {
        // Build Arrays from what we have (not trashed)
        $current_aircraft = DB::table('aircraft')->whereNull('deleted_at')->pluck('id')->toArray();
        $current_airlines = DB::table('airlines')->whereNull('deleted_at')->pluck('id')->toArray();
        $current_airports = DB::table('airports')->whereNull('deleted_at')->pluck('id')->toArray();
        $current_pireps = DB::table('pireps')->whereNull('deleted_at')->pluck('id')->toArray();
        $current_users = DB::table('users')->whereNull('deleted_at')->pluck('id')->toArray();

        // Acars Checks
        $acars_pirep = DB::table('acars')->whereNotIn('pirep_id', $current_pireps)->pluck('id')->toArray();

        // Airport Checks
        $airports_pilot_home = DB::table('users')->whereNull('deleted_at')->whereNotIn('home_airport_id', $current_airports)->groupBy('home_airport_id')->pluck('home_airport_id')->toArray();
        $airports_pilot_curr = DB::table('users')->whereNull('deleted_at')->whereNotIn('curr_airport_id', $current_airports)->groupBy('curr_airport_id')->pluck('curr_airport_id')->toArray();
        $airports_pirep_dep = DB::table('pireps')->whereNull('deleted_at')->whereNotIn('dpt_airport_id', $current_airports)->groupBy('dpt_airport_id')->pluck('dpt_airport_id')->toArray();
        $airports_pirep_arr = DB::table('pireps')->whereNull('deleted_at')->whereNotIn('arr_airport_id', $current_airports)->groupBy('arr_airport_id')->pluck('arr_airport_id')->toArray();
        $airports_flight_dep = DB::table('flights')->whereNull('deleted_at')->whereNotIn('dpt_airport_id', $current_airports)->groupBy('dpt_airport_id')->pluck('dpt_airport_id')->toArray();
        $airports_flight_arr = DB::table('flights')->whereNull('deleted_at')->whereNotIn('arr_airport_id', $current_airports)->groupBy('arr_airport_id')->pluck('arr_airport_id')->toArray();

        // Flight Checks
        $flight_comp = DB::table('flights')->whereNull('deleted_at')->whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
        $flight_orig = DB::table('flights')->whereNull('deleted_at')->whereNotIn('dpt_airport_id', $current_airports)->pluck('id')->toArray();
        $flight_dest = DB::table('flights')->whereNull('deleted_at')->whereNotIn('arr_airport_id', $current_airports)->pluck('id')->toArray();

        // Pirep Checks
        $pirep_user = DB::table('pireps')->whereNull('deleted_at')->whereNotIn('user_id', $current_users)->pluck('id')->toArray();
        $pirep_comp = DB::table('pireps')->whereNull('deleted_at')->whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
        $pirep_orig = DB::table('pireps')->whereNull('deleted_at')->whereNotIn('dpt_airport_id', $current_airports)->pluck('id')->toArray();
        $pirep_dest = DB::table('pireps')->whereNull('deleted_at')->whereNotIn('arr_airport_id', $current_airports)->pluck('id')->toArray();
        $pirep_acft = DB::table('pireps')->whereNull('deleted_at')->whereNotIn('aircraft_id', $current_aircraft)->pluck('id')->toArray();

        // Subfleet Checks
        $fleet_comp = DB::table('subfleets')->whereNull('deleted_at')->whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();

        // User Checks
        $users_comp = DB::table('users')->whereNull('deleted_at')->whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
        $users_field = DB::table('user_field_values')->whereNotIn('user_id', $current_users)->pluck('id')->toArray();

        // Additional Checks
        $rwy_ident_errors = DB::table('pirep_field_values')->where(function ($query) {
            $query->where('slug', 'arrival-heading-deviation')->orWhere('slug', 'landing-heading-deviation');
        })->whereBetween('value', [160, 200])->orderBy('created_at', 'desc')->pluck('pirep_id')->toArray();

        $missing_airports = array_merge($airports_pilot_home, $airports_pilot_curr, $airports_pirep_dep, $airports_pirep_arr, $airports_flight_dep, $airports_flight_arr);
        $missing_airports = array_unique($missing_airports, SORT_STRING);

        return view('DBasic::admin.health_check', [
            'acars_pirep' => $acars_pirep,
            'fleet_comp'  => $fleet_comp,
            'flight_comp' => $flight_comp,
            'flight_orig' => $flight_orig,
            'flight_dest' => $flight_dest,
            'missing_apt' => $missing_airports,
            'pirep_user'  => $pirep_user,
            'pirep_comp'  => $pirep_comp,
            'pirep_orig'  => $pirep_orig,
            'pirep_dest'  => $pirep_dest,
            'pirep_acft'  => $pirep_acft,
            'users_comp'  => $users_comp,
            'users_field' => $users_field,
            'rwy_errors'  => $rwy_ident_errors,
        ]);
    }
}
