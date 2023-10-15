<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Enums\ActiveState;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Services\DB_StatServices;

class DB_StatisticController extends Controller
{
    // Stats
    public function index()
    {
        $airline_count = DB::table('airlines')->whereNull('deleted_at')->where('active', ActiveState::ACTIVE)->count();
        $multi_airline = ($airline_count && $airline_count > 1) ? true : false;

        $StatSvc = app(DB_StatServices::class);

        $stats_basic = $StatSvc->BasicStats();
        $stats_basic[__('DBasic::common.airports')] = DB::table('airports')->whereNull('deleted_at')->count();
        $stats_basic[__('DBasic::common.hubs')] = DB::table('airports')->whereNull('deleted_at')->where('hub', 1)->count();

        $stats_pirep = $StatSvc->PirepStats();
        $stats_ivao = $StatSvc->NetworkStats('IVAO');
        $stats_vatsim = $StatSvc->NetworkStats('VATSIM');

        return view('DBasic::stats.index', [
            'multi_airline' => $multi_airline,
            'stats_basic'   => $stats_basic,
            'stats_ivao'    => $stats_ivao,
            'stats_vatsim'  => $stats_vatsim,
            'stats_pirep'   => $stats_pirep,
        ]);
    }
}
