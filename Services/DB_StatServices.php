<?php

namespace Modules\DisposableBasic\Services;

use App\Models\Pirep;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Enums\UserState;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DB_StatServices
{
    // Leader Board
    public function LeaderBoard($source, $count, $type, $period, $hub)
    {
        $now = Carbon::now();
        $s_date = null;
        $e_date = null;
        $whereIn_array = null;

        if ($source === 'airline') { // Airline
            $base = 'airline_id';
            $eager_load = 'airline';
            $whereIn_array = DB::table('airlines')->where('active', 1)->pluck('id')->toArray();
        } elseif ($source === 'arr') { // Arrival Airport
            $type = 'flights';
            $base = 'arr_airport_id';
            $eager_load = 'arr_airport';
        } elseif ($source === 'dep') { // Departure Airport
            $type = 'flights';
            $base = 'dpt_airport_id';
            $eager_load = 'dpt_airport';
        } else { // Pilot
            $base = 'user_id';
            $eager_load = 'user';
            $user_where = [];
            if (isset($hub)) {
                $user_where['home_airport_id'] = $hub;
            }

            $user_states = array(UserState::ACTIVE, UserState::ON_LEAVE);
            $whereIn_array = DB::table('users')->where($user_where)->whereIn('state', $user_states)->pluck('id')->toArray();
        }

        // Period
        if ($period === 'currentm' || $period === 'currenty') {
            $b_date = $now;
        } elseif ($period === 'lastm') {
            $b_date = $now->subMonthNoOverflow();
        } elseif ($period === 'prevm') {
            $b_date = $now->subMonthsNoOverflow(2);
        } elseif ($period === 'lasty') {
            $b_date = $now->subYearNoOverflow();
        } elseif ($period === 'prevy') {
            $b_date = $now->subYearsNoOverflow(2);
        }

        if ($period === 'currenty' || $period === 'lasty' || $period === 'prevy') {
            $s_date = $b_date->startOfYear();
            $e_date = $b_date->copy()->endOfYear();
            $is_period = true;
        } elseif ($period === 'currentm' || $period === 'lastm' || $period === 'prevm') {
            $s_date = $b_date->startOfMonth();
            $e_date = $b_date->copy()->endOfMonth();
            $is_period = true;
        }

        // Type
        $where = [];
        $where['state'] = PirepState::ACCEPTED;

        if ($type === 'lrate' || $type === 'lrate_low' || $type === 'lrate_high') {
            $where['source'] = PirepSource::ACARS;
            $where[] = ['landing_rate', '!=', 0];
        } elseif ($type === 'score') {
            $where['source'] = PirepSource::ACARS;
        }

        if ($type === 'distance') {
            $select_Raw = 'sum(distance)';
        } elseif ($type === 'time') {
            $select_Raw = 'sum(flight_time)';
        } elseif ($type === 'lrate') {
            $select_Raw = 'avg(landing_rate)';
        } elseif ($type === 'lrate_low') {
            $select_Raw = 'max(landing_rate)';
        } elseif ($type === 'lrate_high') {
            $select_Raw = 'min(landing_rate)';
        } elseif ($type === 'score') {
            $select_Raw = 'avg(score)';
        } else {
            $select_Raw = 'count(id)';
        }

        // Main Query
        $results = Pirep::with($eager_load)->selectRaw($base . ', ' . $select_Raw . ' as totals')
            ->where($where)
            ->when(isset($is_period), function ($query) use ($s_date, $e_date) {
                $query->whereBetween('created_at', [$s_date, $e_date]);
            })
            ->when(is_array($whereIn_array), function ($query) use ($base, $whereIn_array) {
                $query->whereIn($base, $whereIn_array);
            })
            ->when(($type != 'lrate_high'), function ($query) {
                $query->orderBy('totals', 'desc');
            }, function ($query) {
                $query->orderBy('totals', 'asc');
            })
            ->groupBy($base)->take($count)->get();

        // Route
        if ($source === 'airline') {
            $route = 'DBasic.airline';
        } elseif ($source === 'dep' | $source === 'arr') {
            $route = 'frontend.airports.show';
        } else {
            $route = 'frontend.profile.show';
        }

        // Leader Board
        $leader_board = [];
        foreach ($results as $item) {

            if ($type === 'time') {
                $item->totals = DB_ConvertMinutes($item->totals, '%2dh %2dm');
            } elseif ($type === 'lrate' || $type === 'lrate_low' || $type === 'lrate_high') {
                $item->totals = number_format($item->totals) . ' ft/min';
            } elseif ($type === 'distance') {
                $item->totals = DB_ConvertDistance($item->totals);
            } else {
                $item->totals = number_format($item->totals);
            }

            $leader_board[] = [
                'id'           => ($source === 'pilot') ? $item->user_id : $item->$eager_load->icao,
                'icao'         => ($source === 'pilot') ? null : $item->$eager_load->icao,
                'name'         => ($source === 'pilot') ? $item->user->name : $item->$eager_load->name,
                'name_private' => ($source === 'pilot') ? $item->user->name_private : $item->$eager_load->name,
                'route'        => $route,
                'totals'       => $item->totals,
            ];
        }

        return $leader_board;
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
            $stats[__('DBasic::common.airlines')] = DB::table('airlines')->where('active', 1)->count();
        }

        $stats[__('DBasic::common.pilots')] = DB::table('users')->where($where)->count();
        $stats[__('DBasic::common.subfleets')] = count($subfleets_array);
        $stats[__('DBasic::common.aircraft')] = DB::table('aircraft')->whereIn('subfleet_id', $subfleets_array)->count();
        $stats[__('DBasic::common.flights')] = DB::table('flights')->where($where)->count();

        return $stats;
    }

    // Pirep Statistics
    public function PirepStats($airline_id = null, $aircraft_id = null)
    {
        $stats = [];
        $level = 100;
        $unit_distance = setting('units.distance');
        $unit_fuel = setting('units.fuel');

        $where = [];
        $where['state'] = PirepState::ACCEPTED;
        if (isset($airline_id)) {
            $where['airline_id'] = $airline_id;
        } elseif (isset($aircraft_id)) {
            $where['aircraft_id'] = $aircraft_id;
            $level = 10;
        }

        $stats[__('DBasic::widgets.pireps_ack')] = DB::table('pireps')->where($where)->count();

        // Return null if pirep count is zero, no need to work for the rest
        if ($stats[__('DBasic::widgets.pireps_ack')] === 0) {
            return null;
        }

        /* Rejected Pirep counts, dashed out on purpose
        if (empty($airline_id)) {
            $stats[__('DBasic::widgets.pireps_rej')] = DB::table('pireps')->where('state', PirepState::REJECTED)->count();
        } else {
            $stats[__('DBasic::widgets.pireps_rej')] = DB::table('pireps')->where(['airline_id' => $airline_id, 'state' => PirepState::REJECTED])->count();
        }
        */

        $total_time = DB::table('pireps')->where($where)->sum('flight_time');
        $total_dist = DB::table('pireps')->where($where)->sum('distance');
        $total_fuel = DB::table('pireps')->where($where)->sum('fuel_used');

        if ($level > 10) {
            $average_time = DB::table('pireps')->where($where)->avg('flight_time');
            $average_dist = DB::table('pireps')->where($where)->avg('distance');
            $average_fuel = DB::table('pireps')->where($where)->avg('fuel_used');
        }

        if ($unit_distance === 'km') {
            $total_dist = $total_dist * 1.852;
            if ($level > 10) {
                $average_dist = $average_dist * 1.852;
            }
        } elseif ($unit_distance === 'mi') {
            $total_dist = $total_dist * 1.15078;
            if ($level > 10) {
                $average_dist = $average_dist * 1.15078;
            }
        }

        if ($unit_fuel === 'kg') {
            $total_fuel = $total_fuel / 2.20462262185;
            if ($level > 10) {
                $average_fuel = $average_fuel / 2.20462262185;
            }
        }

        $stats[__('DBasic::widgets.ttime')] = DB_ConvertMinutes($total_time, '%2dh %2dm');
        if ($level > 10) {
            $stats[__('DBasic::widgets.atime')] = DB_ConvertMinutes($average_time, '%2dh %2dm');
        }

        $stats[__('DBasic::widgets.tfuel')] = number_format($total_fuel) . ' ' . $unit_fuel;
        if ($level > 10) {
            $stats[__('DBasic::widgets.afuel')] = number_format($average_fuel) . ' ' . $unit_fuel;
        }

        if ($total_fuel > 0 && $total_time > 0) {
            $average_fuel_hour = ($total_fuel / $total_time) * 60;
            $stats[__('DBasic::widgets.hfuel')] = number_format($average_fuel_hour) . ' ' . $unit_fuel;
        }

        $stats[__('DBasic::widgets.tdist')] = number_format($total_dist) . ' ' . $unit_distance;
        if ($level > 10) {
            $stats[__('DBasic::widgets.adist')] = number_format($average_dist) . ' ' . $unit_distance;
        }

        if ($total_dist > 0 && $total_time > 0 && $level > 10) {
            $average_dist_hour = ($total_dist / $total_time) * 60;
            $stats[__('DBasic::widgets.hdist')] = number_format($average_dist_hour) . ' ' . $unit_distance;
        }

        $where['source'] = PirepSource::ACARS;

        $average_lrate = DB::table('pireps')->where($where)->avg('landing_rate');
        $stats[__('DBasic::widgets.alrate')] = number_format(abs($average_lrate)) . ' ft/min';

        if ($level > 10) {
            $average_score = DB::table('pireps')->where($where)->avg('score');
            $stats[__('DBasic::widgets.ascore')] = number_format($average_score);
        }

        return $stats;
    }

    // Airline Finance
    public function AirlineFinance($journal_id)
    {
        $finance = [];

        $income = DB::table('journal_transactions')->where('journal_id', $journal_id)->sum('credit');
        $expense = DB::table('journal_transactions')->where('journal_id', $journal_id)->sum('debit');
        $balance = $income - $expense;
        $color = ($balance < 0) ? 'darkred' : 'darkgreen';

        $finance[__('DBasic::common.income')] = Money::createFromAmount($income);
        $finance[__('DBasic::common.expense')] = Money::createFromAmount($expense);
        $finance[__('DBasic::common.balance')] = '<span style="color: ' . $color . ';"><b>' . Money::createFromAmount($balance) . '</b></span>';

        return $finance;
    }
}
