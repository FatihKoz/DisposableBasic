<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\Subfleet;
use App\Models\User;
use App\Models\Enums\UserState;
use League\ISO3166\ISO3166;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Services\DB_AirportServices;

class DB_HubController extends Controller
{
    // Hubs
    public function index()
    {
        $hubs = Airport::where('hub', 1)->orderby('name')->get();

        if (!$hubs) {
            flash()->error('No hubs found !');
            return redirect(route('frontend.dashboard.index'));
        }

        if (setting('pilots.hide_inactive', true) === true) {
            $states = [UserState::ACTIVE];
        } else {
            $states = [UserState::ACTIVE, UserState::ON_LEAVE];
        }

        $counts = DB::table('users')->selectRaw('home_airport_id as hub, count(id) as members')->whereIn('state', $states)->groupBy('hub')->orderBy('hub', 'asc')->get();

        $pc = [];
        foreach ($counts as $count) {
            $pc[$count->hub] = $count->members;
        }

        return view('DBasic::hubs.index', [
            'country' => new ISO3166(),
            'hubs'    => $hubs,
            'pilots'  => $pc,
        ]);
    }

    // Hub
    public function show($id)
    {
        $hub = Airport::withCount('files')->with('files')->where(['id' => $id, 'hub' => 1])->first();

        if (!$hub) {
            flash()->error('Airport is not hub !');
            return redirect(route('DBasic.hubs'));
        }

        if ($hub) {
            $is_visible = [];

            // Units
            $units = [];
            $units['currency'] = setting('units.currency');
            $units['distance'] = setting('units.distance');
            $units['fuel'] = setting('units.fuel');
            $units['weight'] = setting('units.weight');

            // Aircraft
            $hub_subfleets = Subfleet::where('hub_id', $hub->id)->pluck('id')->toArray();
            $eager_aircraft = ['airline', 'subfleet'];
            $withCount_aircraft = ['simbriefs' => function ($query) { $query->whereNull('pirep_id'); }];

            $aircraft_hub = Aircraft::withCount($withCount_aircraft)->with($eager_aircraft)
                ->whereIn('subfleet_id', $hub_subfleets)
                ->orWhere('hub_id', $hub->id)
                ->orderby('icao')->orderby('registration')
                ->get();
            $aircraft_off = Aircraft::withCount($withCount_aircraft)->with($eager_aircraft)
                ->where('airport_id', $hub->id)
                ->where(function ($query) use ($hub_subfleets, $hub) {
                    return $query->whereNotIn('subfleet_id', $hub_subfleets)->orWhere('hub_id', '!=', $hub->id);
                })->orderby('icao')->orderby('registration')
                ->get();

            $is_visible['aircraft'] = ($aircraft_hub->count() > 0 || $aircraft_off->count() > 0) ? true : false;

            // Flights
            $f_where = ['active' => 1, 'visible' => 1];
            $eager_flights = ['airline', 'arr_airport', 'dpt_airport'];

            $flights = Flight::with($eager_flights)->where($f_where)
                ->where(function ($query) use ($hub) {
                    return $query->where('dpt_airport_id', $hub->id)->orWhere('arr_airport_id', $hub->id);
                })->orderby('flight_number')->get();

            $flights_dpt = $flights->where('dpt_airport_id', $hub->id);
            $flights_arr = $flights->where('arr_airport_id', $hub->id);

            $is_visible['flights'] = ($flights_dpt->count() > 0 || $flights_arr->count() > 0) ? true : false;

            // Pilots
            $hub_where = [];
            $off_where = [];
            $hub_where['home_airport_id'] = $hub->id;
            $off_where[] = ['home_airport_id', '!=', $hub->id];
            $off_where['curr_airport_id'] = $hub->id;

            if (setting('pilots.hide_inactive')) {
                $hub_where['state'] = 1;
                $off_where['state'] = 1;
            }

            $eager_users = ['airline', 'current_airport', 'home_airport', 'rank'];

            $users_hub = User::with($eager_users)->where($hub_where)->orderby('id')->get();
            $users_off = User::with($eager_users)->where($off_where)->orderby('id')->get();

            $is_visible['pilots'] = ($users_hub->count() > 0 || $users_off->count() > 0) ? true : false;

            // Pilot Reports
            $p_where = ['state' => 2, 'status' => 'ONB'];
            $eager_pireps = ['aircraft', 'airline', 'arr_airport', 'dpt_airport', 'user'];

            $pireps = Pirep::with($eager_pireps)->where('dpt_airport_id', $hub->id)->where($p_where)
                ->orWhere('arr_airport_id', $hub->id)->where($p_where)
                ->orderby('submitted_at', 'desc')->paginate(25);
            $is_visible['reports'] = ($pireps->count() > 0) ? true : false;

            // Downloads
            $is_visible['downloads'] = ($hub->files_count > 0 && Auth::check()) ? true : false;

            // Sunrise Sunset Details
            $AirportSvc = app(DB_AirportServices::class);
            $sun_details = $AirportSvc->SunriseSunset($hub);

            return view('DBasic::hubs.show', [
                'aircraft_hub' => $aircraft_hub,
                'aircraft_off' => $aircraft_off,
                'country'      => new ISO3166(),
                'flights_arr'  => $flights_arr,
                'flights_dpt'  => $flights_dpt,
                'hub'          => $hub,
                'is_visible'   => $is_visible,
                'pireps'       => $pireps,
                'sundetails'   => $sun_details,
                'units'        => $units,
                'users_hub'    => $users_hub,
                'users_off'    => $users_off,
            ]);
        }
    }
}
