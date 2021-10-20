<?php

namespace Modules\DisposableBasic\Services;

use App\Models\Pirep;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Enums\UserState;
use App\Support\Money;
use Illuminate\Support\Facades\DB;

class DB_StatServices
{
  // Pilot Learderboard
  public function PilotLeaderboard()
  {
    $user_states = array(UserState::ACTIVE, UserState::ON_LEAVE);
    $users_array = DB::table('users')->whereIn('state', $user_states)->pluck('id')->toArray();

    $leaderboard = Pirep::with('user.airline')->selectRaw($select_Raw)
      ->where('state', PirepState::ACCEPTED)
      ->whereIn('user_id', $users_array)
      ->orderBy('totals', 'desc')->groupBy('user_id')
      ->take(10)->get();

    return $leaderboard;
  }
  
  // Basic Statistics
  public function BasicStats($airline_id = null)
  {
    $stats = [];

    $where = [];
    if (isset($airline_id)) {
      $where['airline_id'] = $airline_id;
    }

    $subfleets_array = DB::table('subfleets')->where($where)->pluck('id')->toArray();

    if (empty($airline_id)) {
      $stats[__('DBasic::stats.airlines')] = DB::table('airlines')->where('active', 1)->count();
    }

    $stats[__('DBasic::stats.pilots')] = DB::table('users')->where($where)->count();
    $stats[__('DBasic::stats.subfleets')] = count($subfleets_array);
    $stats[__('DBasic::stats.aircraft')] = DB::table('aircraft')->whereIn('subfleet_id', $subfleets_array)->count();
    $stats[__('DBasic::stats.flights')] = DB::table('flights')->where($where)->count();

    return $stats;
  }

  // Pirep Statistics
  public function PirepStats($airline_id = null)
  {
    $stats = [];

    $unit_distance = setting('units.distance');
    $unit_fuel = setting('units.fuel');

    $where = [];
    $where['state'] = PirepState::ACCEPTED;
    if (isset($airline_id)) {
      $where['airline_id'] = $airline_id;
    }

    $stats[__('DBasic::stats.pireps_ack')] = DB::table('pireps')->where($where)->count();

    if (empty($airline_id)) {
      $stats[__('DBasic::stats.pireps_rej')] = DB::table('pireps')->where('state', PirepState::REJECTED)->count();
    } else {
      $stats[__('DBasic::stats.pireps_rej')] = DB::table('pireps')->where(['airline_id' => $airline_id, 'state' => PirepState::REJECTED])->count();
    }

    $total_time = DB::table('pireps')->where($where)->sum('flight_time');
    $average_time = DB::table('pireps')->where($where)->avg('flight_time');

    $total_dist = DB::table('pireps')->where($where)->sum('distance');
    $average_dist = DB::table('pireps')->where($where)->avg('distance');

    $total_fuel = DB::table('pireps')->where($where)->sum('fuel_used');
    $average_fuel = DB::table('pireps')->where($where)->avg('fuel_used');

    if ($unit_distance === 'km') {
      $total_dist = $total_dist * 1.852;
      $average_dist = $average_dist * 1.852;
    } elseif ($unit_distance === 'mi') {
      $total_dist = $total_dist * 1.15078;
      $average_dist = $average_dist * 1.15078;
    }

    if ($unit_fuel === 'kg') {
      $total_fuel = $total_fuel / 2.20462262185;
      $average_fuel = $average_fuel / 2.20462262185;
    }

    $stats[__('DBasic::stats.ttime')] = $total_time; // minutestotime($total_time);
    $stats[__('DBasic::stats.atime')] = $average_time; // minutestotime($average_time);

    $stats[__('DBasic::stats.tfuel')] = number_format($total_fuel).' '.$unit_fuel;
    $stats[__('DBasic::stats.afuel')] = number_format($average_fuel).' '.$unit_fuel;

    if ($total_fuel > 0 && $total_time > 0) {
      $average_fuel_hour = ($total_fuel / $total_time) * 60;
      $stats[__('DBasic::stats.hfuel')] = number_format($average_fuel_hour).' '.$unit_fuel;
    }

    $stats[__('DBasic::stats.tdist')] = number_format($total_dist).' '.$unit_distance;
    $stats[__('DBasic::stats.adist')] = number_format($average_dist).' '.$unit_distance;

    if ($total_dist > 0 && $total_time > 0) {
      $average_dist_hour = ($total_dist / $total_time) * 60;
      $stats[__('DBasic::stats.hdist')] = number_format($average_dist_hour).' '.$unit_distance;
    }

    $where['source'] = PirepSource::ACARS;

    $average_lrate = DB::table('pireps')->where($where)->avg('landing_rate');
    $average_score = DB::table('pireps')->where($where)->avg('score');

    $stats[__('DBasic::stats.alrate')] = number_format(abs($average_lrate)).' ft/min';
    $stats[__('DBasic::stats.ascore')] = number_format($average_score);

    return $stats;
  }

  // Top Airports
  public function TopAirports($type = 'dpt', $count = 5)
  {
    $top_airports = Pirep::with($type.'_airport')->selectRaw($type.'_airport_id, count('.$type.'_airport_id) as tusage')
      ->where('state', PirepState::ACCEPTED)->orderby('tusage', 'desc')->groupby($type.'_airport_id')->take($count)->get();

    return $top_airports;
  }

  // Airline Finance
  public function AirlineFinance($journal_id)
  {
    $finance = [];

    $income = DB::table('journal_transactions')->where('journal_id', $journal_id)->sum('credit');
    $expense = DB::table('journal_transactions')->where('journal_id', $journal_id)->sum('debit');
    $balance = $income - $expense;
    $color = ($balance < 0) ? ' darkred' : ' darkgreen';

    $finance[__('DBasic::common.income')] = Money::createFromAmount($income);
    $finance[__('DBasic::common.expense')] = Money::createFromAmount($expense);
    $finance[__('DBasic::common.balance')] = '<span style="color: '.$color.';"><b>'.Money::createFromAmount($balance).'</b></span>';

    return $finance;
  }
}