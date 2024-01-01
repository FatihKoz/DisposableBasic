<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Airline;
use App\Models\Aircraft;
use App\Models\Pirep;
use App\Models\Subfleet;
use App\Models\User;
use App\Models\Enums\ActiveState;
use App\Models\Enums\PirepState;
use App\Models\Enums\UserState;
use League\ISO3166\ISO3166;
use Modules\DisposableBasic\Services\DB_StatServices;

class DB_AirlineController extends Controller
{
    // Airlines
    public function index()
    {
        $withCount = ['aircraft', 'flights', 'pireps', 'subfleets', 'users'];
        $airlines = Airline::withCount($withCount)->where('airlines.active', ActiveState::ACTIVE)->sortable('name')->get();

        if (!$airlines) {
            flash()->error('No active airline found !');
            return redirect(route('frontend.dashboard.index'));
        }

        if ($airlines->count() === 1) {
            $airline = $airlines->first();
            return redirect(route('DBasic.airline', [$airline->icao]));
        }

        return view('DBasic::airlines.index', [
            'airlines' => $airlines,
            'country'  => new ISO3166(),
        ]);
    }

    // My Airline
    public function myairline($id)
    {
        $airline_icao = optional(Airline::select('icao')->where('id', $id)->first())->icao;

        if (filled($airline_icao)) {
            return redirect(route('DBasic.airline', [$airline_icao]));
        } else {
            flash()->error('Airline not found !');
            return redirect(route('DBasic.airlines'));
        }
    }

    // Airline Details
    public function show($icao)
    {
        $airline = Airline::with('journal')->where('icao', $icao)->first();

        if (!$airline) {
            flash()->error('Airline not found !');
            return redirect(route('DBasic.airlines'));
        }

        if ($airline) {
            // Get Pilots, ordered by join date (seniority)
            $user_where = [];
            $user_where['airline_id'] = $airline->id;

            if (setting('pilots.hide_inactive')) {
                $user_where['state'] = UserState::ACTIVE;
            }

            $eager_users = ['rank', 'current_airport', 'home_airport', 'last_pirep'];
            $users = User::withCount('awards')->with($eager_users)->where($user_where)->orderBy('created_at')->get();

            // Get Pireps, latest 50 ordered by submit date descending
            $pirep_where = [];
            $pirep_where['pireps.airline_id'] = $airline->id;
            $pirep_where[] = ['pireps.state', '!=', PirepState::IN_PROGRESS];

            $eager_pireps = ['aircraft', 'airline', 'dpt_airport', 'arr_airport', 'user'];
            $pireps = Pirep::with($eager_pireps)->where($pirep_where)->orderBy('submitted_at', 'desc')->paginate(50);

            // Get Aircraft, full fleet without restrictions
            $airline_subfleets = Subfleet::where('airline_id', $airline->id)->pluck('id')->toArray();
            $aircraft = Aircraft::with('subfleet', 'airline')->whereIn('aircraft.subfleet_id', $airline_subfleets)->sortable('registration', 'subfleet.name')->get();

            // Get Stats
            $StatSvc = app(DB_StatServices::class);
            $finance = $StatSvc->AirlineFinance($airline->journal->id);
            $stats_basic = $StatSvc->BasicStats($airline->id);
            $stats_pirep = $StatSvc->PirepStats($airline->id);

            return view('DBasic::airlines.show', [
                'aircraft'  => $aircraft,
                'airline'   => $airline,
                'country'   => new ISO3166(),
                'finance'   => $finance,
                'pireps'    => $pireps,
                'stats_b'   => $stats_basic,
                'stats_p'   => $stats_pirep,
                'units'     => DB_GetUnits(),
                'users'     => $users,
            ]);
        }
    }
}
