<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Services\DB_StatServices;

class DB_StatsController extends Controller
{
  // Stats
  public function index()
  {
    $StatSvc = app(DB_StatServices::class);

    $stats_basic = $StatSvc->BasicStats();
    $stats_basic[__('DBasic::stats.airports')] = DB::table('airports')->count();
    $stats_basic[__('DBasic::stats.hubs')] = DB::table('airports')->where('hub', 1)->count();

    $stats_pirep = $StatSvc->PirepStats();

    $arrivals = $StatSvc->TopAirports('arr', 5);
    $departures = $StatSvc->TopAirports('dpt', 5);

    return view('DBasic::stats.index', [
      'arrivals'    => $arrivals,
      'departures'  => $departures,
      'stats_basic' => $stats_basic,
      'stats_pirep' => $stats_pirep,
    ]);
  }
}
