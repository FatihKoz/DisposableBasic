<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Services\DB_StatServices;

class DB_StatisticController extends Controller
{
    // Stats
    public function index()
    {
        $airline_count = DB::table('airlines')->where('active', 1)->count();
        $multi_airline = ($airline_count && $airline_count > 1) ? true : false;

        $StatSvc = app(DB_StatServices::class);
     
        $stats_basic = $StatSvc->BasicStats();
        $stats_basic[__('DBasic::common.airports')] = DB::table('airports')->count();
        $stats_basic[__('DBasic::common.hubs')] = DB::table('airports')->where('hub', 1)->count();

        $stats_pirep = $StatSvc->PirepStats();

        return view('DBasic::stats.index', [
            'multi_airline' => $multi_airline, 
            'stats_basic'   => $stats_basic,
            'stats_pirep'   => $stats_pirep,
        ]);
    }
}
