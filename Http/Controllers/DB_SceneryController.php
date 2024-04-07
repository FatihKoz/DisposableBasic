<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user_sceneries = DB_Scenery::groupBy('airport_id')->pluck('airport_id')->toArray();

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

        $sceneries = DB_Scenery::withCount(['departures', 'arrivals'])->with(['airport'])->when($filter_region, function ($query) use ($request) {
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
            'user_sims'  => $user_simulators
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

        Log::debug('Disposable Basic | Scenery record for ' . strtoupper($request->airport_id) . ' added by ' . Auth::user()->name_private);
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
            Log::debug('Disposable Basic | Scenery record for ' . $scenery->airport_id . ' deleted by ' . Auth::user()->name_private);
            flash()->warning('Scenery entry deleted!');
        } else {
            flash()->warning('Scenery not found!');
        }

        return redirect(route('DBasic.scenery'));
    }
}
