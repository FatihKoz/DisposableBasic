<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Bid;
use App\Models\Flight;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Models\DB_Scenery;
use Modules\DisposableBasic\Models\Enums\DB_Simulator;
use Modules\DisposableBasic\Models\Enums\DB_WorldRegion;

class DB_SceneryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $user_regions = DB_Scenery::where('user_id', $user->id)->where('region', '!=', 0)->groupBy('region')->pluck('region')->toArray();
        $user_simulators = DB_Scenery::where('user_id', $user->id)->where('simulator', '!=', 0)->groupBy('simulator')->pluck('simulator')->toArray();
        $user_sceneries = DB_Scenery::where('user_id', $user->id)->groupBy('airport_id')->pluck('airport_id')->toArray();

        $where = [
            'active'  => true,
            'visible' => true,
        ];

        if (setting('pilots.restrict_to_company')) {
            $where['airline_id'] = $user->airline_id;
        }

        $flights = [];
        $flights['deps'] = Flight::where($where)->whereIn('dpt_airport_id', $user_sceneries)->count();
        $flights['arrs'] = Flight::where($where)->whereIn('arr_airport_id', $user_sceneries)->count();
        $flights['trip'] = Flight::where($where)->whereIn('dpt_airport_id', $user_sceneries)->whereIn('arr_airport_id', $user_sceneries)->count();

        $filter_region = filled($request->input('region')) ? true : false;
        $filter_sim = filled($request->input('sim')) ? true : false;

        $sceneries = DB_Scenery::withCount(['departures', 'arrivals'])->with(['airport'])->where('user_id', $user->id)->when($filter_region, function ($query) use ($request) {
            return $query->where('region', $request->input('region'));
        })->when($filter_sim, function ($query) use ($request) {
            return $query->where('simulator', $request->input('sim'));
        })->sortable('airport_id')->paginate(25);

        return view('DBasic::scenery.index', [
            'sceneries'  => $sceneries,
            'simulators' => DB_Simulator::select(true),
            'regions'    => DB_WorldRegion::select(true),
            'flights'    => $flights,
            'user_id'    => $user->id,
            'user_regs'  => $user_regions,
            'user_sims'  => $user_simulators,
        ]);
    }

    public function flights(Request $request)
    {
        $type = $request->input('type');

        if (!in_array($type, ['arrivals', 'departures', 'trips'])) {
            $type = 'arrivals';
        }

        $search_arrs = $type === 'arrivals' ? true : false;
        $search_deps = $type === 'departures' ? true : false;
        $search_trips = $type === 'trips' ? true : false;

        $user = Auth::user();
        $user_sceneries = DB_Scenery::where('user_id', $user->id)->groupBy('airport_id')->pluck('airport_id')->toArray();

        $where = [
            'active'  => true,
            'visible' => true,
        ];

        if (setting('pilots.restrict_to_company')) {
            $where['airline_id'] = $user->airline_id;
        }

        if (setting('pilots.only_flights_from_current')) {
            $where['dpt_airport_id'] = $user->curr_airport_id;
        }

        $filter_by_user = (setting('pireps.restrict_aircraft_to_rank', true) || setting('pireps.restrict_aircraft_to_typerating', false)) ? true : false;

        if ($filter_by_user) {
            $user_service = app(UserService::class);
            $user_subfleets = $user_service->getAllowableSubfleets($user)->pluck('id')->toArray();
            $user_flights = DB::table('flight_subfleet')->select('flight_id')->whereIn('subfleet_id', $user_subfleets)->groupBy('flight_id')->pluck('flight_id')->toArray();
            $open_flights = Flight::withCount('subfleets')->whereNull('user_id')->having('subfleets_count', 0)->pluck('id')->toArray();
            $allowed_flights = array_merge($user_flights, $open_flights);
        } else {
            $allowed_flights = [];
        }

        $myflights = Flight::with(['airline', 'dpt_airport', 'arr_airport', 'simbrief'])->where($where)->whereNull('user_id')->when($search_deps, function ($query) use ($user_sceneries) {
            return $query->whereIn('dpt_airport_id', $user_sceneries);
        })->when($search_arrs, function ($query) use ($user_sceneries) {
            return $query->whereIn('arr_airport_id', $user_sceneries);
        })->when($search_trips, function ($query) use ($user_sceneries) {
            return $query->whereIn('dpt_airport_id', $user_sceneries)->whereIn('arr_airport_id', $user_sceneries);
        })->when($filter_by_user, function ($query) use ($allowed_flights) {
            return $query->whereIn('id', $allowed_flights);
        })->sortable(['flight_number', 'route_code', 'route_leg'])->paginate(25);

        $saved_flights = [];
        $bids = Bid::where('user_id', $user->id)->get();
        foreach ($bids as $bid) {
            if (!$bid->flight) {
                $bid->delete();
                continue;
            }

            $saved_flights[$bid->flight_id] = $bid->id;
        }

        return view('DBasic::scenery.flights', [
            'flights'       => $myflights,
            'units'         => DB_GetUnits(),
            'type'          => $type,
            'user'          => $user,
            'saved'         => $saved_flights,
            'simbrief'      => !empty(setting('simbrief.api_key')),
            'simbrief_bids' => setting('simbrief.only_bids'),
            'acars_plugin'  => check_module('VMSAcars'),
        ]);
    }

    public function store(Request $request)
    {
        if (!$request->airport_id || strlen($request->airport_id) != 4) {
            flash()->error('Provide an Airport ICAO code!');

            return redirect(route('DBasic.scenery'));
        }

        DB_Scenery::updateOrCreate(
            [
                'id'          => $request->id,
            ],
            [
                'user_id'    => Auth::id(),
                'airport_id' => strtoupper($request->airport_id),
                'region'     => $request->region,
                'simulator'  => $request->simulator,
                'notes'      => $request->notes,
            ]
        );

        Log::debug('Disposable Basic | Scenery record for '.strtoupper($request->airport_id).' added by '.Auth::user()->name_private);
        flash()->success('Scenery entry saved');

        return redirect(route('DBasic.scenery'));
    }

    public function delete(Request $request)
    {
        $where = [
            'id'         => $request->id,
            'user_id'    => $request->user_id,
            'airport_id' => strtoupper($request->airport_id),
        ];

        $scenery = DB_Scenery::where($where)->first();

        if ($scenery) {
            $scenery->delete();
            Log::debug('Disposable Basic | Scenery record for '.$scenery->airport_id.' deleted by '.Auth::user()->name_private);
            flash()->warning('Scenery entry deleted!');
        } else {
            flash()->warning('Scenery not found!');
        }

        return redirect(route('DBasic.scenery'));
    }
}
