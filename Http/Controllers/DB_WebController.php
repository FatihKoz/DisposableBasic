<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Pirep;
use App\Models\User;
use App\Models\Enums\UserState;
use League\ISO3166\ISO3166;
use Illuminate\Http\Request;
use Modules\DisposableBasic\Services\DB_StatServices;

class DB_WebController extends Controller
{
    // Roster
    public function roster()
    {
        $where = [];

        if (setting('pilots.hide_inactive')) {
            $where['state'] = UserState::ACTIVE;
        }

        $eager_load = ['airline', 'current_airport', 'home_airport', 'last_pirep', 'rank'];
        $users = User::withCount('awards')->with($eager_load)->where($where)->orderby('pilot_id')->paginate(25);

        return view('DBasic::web.roster', [
            'users'    => $users,
            'country'  => new ISO3166(),
            'DBasic'   => true,
            'DSpecial' => DB_CheckModule('DisposableSpecial'),
        ]);
    }

    // Pireps
    public function pireps(Request $request)
    {
        $count = is_numeric($request->input('count')) ? $request->input('count') : 10;
        $where = [];

        $where['state'] = '2';
        $where['status'] = 'ONB';

        $eager_load = ['airline', 'aircraft', 'arr_airport', 'dpt_airport', 'user'];
        $pireps = Pirep::with($eager_load)->where($where)->orderby('submitted_at', 'DESC')->take($count)->get();

        return view('DBasic::web.pireps', [
            'pireps'   => $pireps,
            'units'    => DB_GetUnits(),
            'DBasic'   => true,
            'DSpecial' => DB_CheckModule('DisposableSpecial'),
        ]);
    }

    // Stats
    public function stats()
    {
        $airline_count = Airline::where('active', 1)->count();
        $multi_airline = ($airline_count && $airline_count > 1) ? true : false;

        $StatSvc = app(DB_StatServices::class);

        $stats_basic = $StatSvc->BasicStats();
        $stats_basic[__('DBasic::common.airports')] = Airport::count();
        $stats_basic[__('DBasic::common.hubs')] = Airport::where('hub', 1)->count();

        $stats_pirep = $StatSvc->PirepStats();

        return view('DBasic::web.stats', [
            'multi_airline' => $multi_airline,
            'stats_basic'   => $stats_basic,
            'stats_pirep'   => $stats_pirep,
            'DBasic'        => true,
            'DSpecial'      => DB_CheckModule('DisposableSpecial'),
        ]);
    }

    // Blank Page for Widgets etc
    public function page()
    {
        return view('DBasic::web.blank', [
            'DBasic'   => true,
            'DSpecial' => DB_CheckModule('DisposableSpecial'),
        ]);
    }
}
