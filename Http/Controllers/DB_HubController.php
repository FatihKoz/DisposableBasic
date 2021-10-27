<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\Subfleet;
use App\Models\User;
use League\ISO3166\ISO3166;
use Illuminate\Support\Facades\Auth;

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

        return view('DBasic::hubs.index', [
            'country' => new ISO3166(),
            'hubs'    => $hubs,
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
            $aircraft_hub = Aircraft::with('subfleet.airline')->whereIn('subfleet_id', $hub_subfleets)
                ->orderby('icao')->orderby('registration')->get();
            $aircraft_off = Aircraft::with('subfleet.airline')->whereNotIn('subfleet_id', $hub_subfleets)
                ->where('airport_id', $hub->id)->orderby('icao')->orderby('registration')->get();
            $is_visible['aircraft'] = ($aircraft_hub->count() > 0 || $aircraft_off->count() > 0) ? true : false;

            // Flights
            $d_where = array('dpt_airport_id' => $hub->id, 'active' => 1, 'visible' => 1);
            $a_where = array('arr_airport_id' => $hub->id, 'active' => 1, 'visible' => 1);
            $flights_dpt = Flight::with('arr_airport', 'airline')->where($d_where)->orderby('arr_airport_id')->get();
            $flights_arr = Flight::with('dpt_airport', 'airline')->where($a_where)->orderby('dpt_airport_id')->get();
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

            $users_hub = User::with('airline', 'rank')->where($hub_where)->orderby('id')->get();
            $users_off = User::with('airline', 'rank')->where($off_where)->orderby('id')->get();
            $is_visible['pilots'] = ($users_hub->count() > 0 || $users_off->count() > 0) ? true : false;

            // Reports
            $p_where = array('state' => 2, 'status' => 'ONB');
            $eager_pireps = array('user', 'aircraft', 'airline', 'dpt_airport', 'arr_airport');
            $pireps = Pirep::with($eager_pireps)->where('dpt_airport_id', $hub->id)->where($p_where)
                ->orWhere('arr_airport_id', $hub->id)->where($p_where)
                ->orderby('submitted_at', 'desc')->paginate(25);
            $is_visible['reports'] = ($pireps->count() > 0) ? true : false;

            // Downloads
            $is_visible['downloads'] = ($hub->files_count > 0 && Auth::check()) ? true : false;

            return view('DBasic::hubs.show', [
                'country'      => new ISO3166(),
                'hub'          => $hub,
                'aircraft_hub' => $aircraft_hub,
                'aircraft_off' => $aircraft_off,
                'flights_arr'  => $flights_arr,
                'flights_dpt'  => $flights_dpt,
                'pireps'       => $pireps,
                'units'        => $units,
                'users_hub'    => $users_hub,
                'users_off'    => $users_off,
                'is_visible'   => $is_visible,
            ]);
        }
    }
}
