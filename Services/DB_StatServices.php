<?php

namespace Modules\DisposableBasic\Services;

use App\Models\Pirep;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Enums\UserState;
use App\Support\Units\Distance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DB_StatServices
{
    // Personal Statistics (uses cache)
    public function PersonalStats($user_id, $period, $type)
    {
        $personal = [];

        $now = Carbon::now();
        $current_year = $now->copy()->format('Y');
        $b_date = null;
        $s_date = null;
        $e_date = null;

        // Cache
        $cache_key = 'pstats-' . $user_id . '-' . $type;
        if (isset($period)) {
            $cache_key .= '-' . $period;
        }

        if (is_numeric($period)) {
            $cache_until = Carbon::now()->endOfDay();
        } elseif ($period === 'lastm' || $period === 'prevm') {
            $cache_until = Carbon::now()->endOfMonth();
        } elseif ($period === 'lasty' || $period === 'prevy') {
            $cache_until = Carbon::now()->endOfYear();
        } else {
            $cache_until = Carbon::now()->addHours(6);
        }

        // Base Date
        if (is_numeric($period) || $period === 'currentm' || $period === 'currenty') {
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

        // Period Specific Start and End Dates, Period Text
        if ($period === 'currenty' || $period === 'lasty' || $period === 'prevy') { // Years
            $s_date = $b_date->startOfYear();
            $e_date = $b_date->copy()->endOfYear();
            $personal['period_text'] = $b_date->format('Y');
        } elseif ($period === 'currentm' || $period === 'lastm' || $period === 'prevm') { // Months
            $s_date = $b_date->startOfMonth();
            $e_date = $b_date->copy()->endOfMonth();
            $personal['period_text'] = __('DBasic::dates.'.$b_date->format('m'));
        } elseif (is_numeric($period)) { // Days
            $s_date = $b_date->copy()->startOfDay()->subdays($period);
            $e_date = $now->endOfDay();
            $personal['period_text'] = __('DBasic::widgets.lastndays', ['period' => $period]);
        } elseif ($period === 'q1') { // Quarter 1 JAN-FEB-MAR
            $s_date = $current_year . '-01-01 00:00:01';
            $e_date = $current_year . '-03-31 23:59:59';
            $personal['period_text'] = $current_year . '/' . strtoupper($period);
        } elseif ($period === 'q2') { // Quarter 2 APR-MAY-JUN
            $s_date = $current_year . '-04-01 00:00:01';
            $e_date = $current_year . '-06-30 23:59:59';
            $personal['period_text'] = $current_year . '/' . strtoupper($period);
        } elseif ($period === 'q3') { // Quarter 3 JUL-AUG-SEP
            $s_date = $current_year . '-07-01 00:00:01';
            $e_date = $current_year . '-09-30 23:59:59';
            $personal['period_text'] = $current_year . '/' . strtoupper($period);
        } elseif ($period === 'q4') { // Quarter 4 OCT-NOV-DEC
            $s_date = $current_year . '-10-01 00:00:01';
            $e_date = $current_year . '-12-31 23:59:59';
            $personal['period_text'] = $current_year . '/' . strtoupper($period);
        }

        $where = [];
        $where['user_id'] = $user_id;
        $where['state'] = PirepState::ACCEPTED;

        $table_name = 'pireps';
        $date_field = 'submitted_at';

        if ($type === 'avglanding') { // Average Landing Rate - Acars Only
            $where['source'] = PirepSource::ACARS;
            $where[] = ['landing_rate', '<', 0];
            $select_raw = 'avg(landing_rate)';
            $personal['stat_name'] = __('DBasic::widgets.avglanding');
        } elseif ($type === 'avgscore') { // Average Score - Acars Only
            $where['source'] = PirepSource::ACARS;
            $select_raw = 'avg(score)';
            $personal['stat_name'] = __('DBasic::widgets.avgscore');
        } elseif ($type === 'avgdistance') { // Average Distance
            $select_raw = 'avg(distance)';
            $personal['stat_name'] = __('DBasic::widgets.avgdistance');
        } elseif ($type === 'totdistance') { // Total Distance
            $select_raw = 'sum(distance)';
            $personal['stat_name'] = __('DBasic::widgets.totdistance');
        } elseif ($type === 'avgtime') { // Average Time
            $select_raw = 'avg(flight_time)';
            $personal['stat_name'] = __('DBasic::widgets.avgtime');
        } elseif ($type === 'tottime') { // Total Time
            $select_raw = 'sum(flight_time)';
            $personal['stat_name'] = __('DBasic::widgets.tottime');
        } elseif ($type === 'avgfuel') { // Average Fuel
            $select_raw = 'avg(fuel_used)';
            $personal['stat_name'] = __('DBasic::widgets.avgfuel');
        } elseif ($type === 'totfuel') { // Total Fuel
            $select_raw = 'sum(fuel_used)';
            $personal['stat_name'] = __('DBasic::widgets.totfuel');
        } elseif ($type === 'totflight') { // Total Flights
            $select_raw = 'count(id)';
            $personal['stat_name'] = __('DBasic::widgets.totflight');
        } elseif ($type === 'fdm') {
            $select_raw = '(100 * sum(if(is_stable = 1, 1, 0))) / count(user_id)';
            unset($where['state']);
            $table_name = 'disposable_sap_reports';
            $date_field = 'created_at';
            $personal['stat_name'] = __('DBasic::widgets.fdm');
        } elseif ($type === 'assignment') {
            $select_raw = '(100 * sum(if(pirep_id IS NOT NULL, 1, 0))) / count(user_id)';
            unset($where['state']);
            $table_name = 'disposable_assignments';
            $date_field = 'updated_at';
            $personal['stat_name'] = __('DBasic::widgets.assignment');
        }

        // Execute
        $result = cache()->remember($cache_key, $cache_until, function () use ($select_raw, $where, $period, $s_date, $e_date, $table_name, $date_field) {
            return DB::table($table_name)->selectRaw($select_raw . ' as uresult')
                ->where($where)
                ->when(isset($period), function ($query) use ($s_date, $e_date, $date_field) {
                    $query->whereBetween($date_field, [$s_date, $e_date]);
                })->value('uresult');
            }
        );

        $personal['raw'] = $result;
        // Format the result according to type
        if ($type === 'avglanding') {
            $personal['formatted'] = number_format($result) . ' ft/min';
        } elseif ($type === 'avgscore') {
            $personal['formatted'] = number_format($result);
        } elseif ($type === 'avgtime' || $type === 'tottime') {
            $personal['formatted'] = DB_ConvertMinutes(round($result), '%2dh %2dm');
        } elseif ($type === 'avgdistance' || $type === 'totdistance') {
            if (setting('units.distance') === 'km') {
                $personal['formatted'] = number_format($result * 1.852) . ' km';
            } else {
                $personal['formatted'] = number_format($result) . ' nm';
            }
        } elseif ($type === 'avgfuel' || $type === 'totfuel') {
            if (setting('units.fuel') === 'kg') {
                $personal['formatted'] = number_format($result / 2.20462262185) . ' kg';
            } else {
                $personal['formatted'] = number_format($result) . ' lbs';
            }
        } elseif ($type === 'fdm' || $type === 'assignment') {
            $personal['formatted'] = number_format($result).'%';
        } else {
            $personal['formatted'] = round($result);
        }

        return $personal;
    }

    // Leader Board (uses cache)
    public function LeaderBoard($source, $count, $type, $period, $hub)
    {
        $now = Carbon::now();
        $s_date = null;
        $e_date = null;
        $whereIn_array = null;

        // Cache
        $cache_key = 'lboard-' . $source . '-' . $type . '-' . $count;
        if (isset($period)) {
            $cache_key .= '-' . $period;
        } else {
            $cache_key .= '-alltime';
        }
        if (isset($hub)) {
            $cache_key .= '-' . $hub;
        }

        if ($period === 'lastm' || $period === 'prevm') {
            $cache_until = Carbon::now()->endOfMonth();
        } elseif ($period === 'lasty' || $period === 'prevy') {
            $cache_until = Carbon::now()->endOfYear();
        } else {
            $cache_until = Carbon::now()->endOfDay();
        }

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

            $user_states = [UserState::ACTIVE, UserState::ON_LEAVE];
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
        } else {
            $is_period = null;
        }

        // Type
        $where = [];
        $where['state'] = PirepState::ACCEPTED;

        if ($type === 'lrate' || $type === 'lrate_low' || $type === 'lrate_high') {
            $where['source'] = PirepSource::ACARS;
            $where[] = ['landing_rate', '<', 0];
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
        $results = cache()->remember($cache_key, $cache_until, function () use ($eager_load, $base, $select_Raw, $where, $is_period, $s_date, $e_date, $whereIn_array, $type, $count) {
            return Pirep::with($eager_load)->selectRaw($base . ', ' . $select_Raw . ' as totals')
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
            }
        );

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
                $item->totals = DB_ConvertDistance(new Distance($item->totals, 'nmi'));
            } else {
                $item->totals = number_format($item->totals);
            }

            $leader_board[] = [
                'id'           => ($source === 'pilot') ? $item->user_id : $item->$eager_load->icao,
                'icao'         => ($source === 'pilot') ? null : $item->$eager_load->icao,
                'name'         => ($source === 'pilot') ? $item->user->name : $item->$eager_load->name,
                'name_private' => ($source === 'pilot') ? $item->user->name_private : $item->$eager_load->name,
                'pilot_ident'  => ($source === 'pilot') ? $item->user->ident.' - ' : null,
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
        $unit_weight = setting('units.weight');
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
        if (setting('pireps.delete_rejected_hours') == 0 && $level > 10) {
            if (empty($airline_id)) {
                $stats[__('DBasic::widgets.pireps_rej')] = DB::table('pireps')->where('state', PirepState::REJECTED)->count();
            } else {
                $stats[__('DBasic::widgets.pireps_rej')] = DB::table('pireps')->where(['airline_id' => $airline_id, 'state' => PirepState::REJECTED])->count();
            }
        }
        */

        // Count carried PAX and CGO for fancy stats
        $paxfares = DB::table('fares')->select('id')->where('type', 0)->pluck('id')->toArray();
        $cgofares = DB::table('fares')->select('id')->where('type', 1)->pluck('id')->toArray();
        $allpireps = DB::table('pireps')->select('id')->where($where)->pluck('id')->toArray();

        $pax_amount = DB::table('pirep_fares')->whereIn('pirep_id', $allpireps)->whereIn('fare_id', $paxfares)->sum('count');
        $pax_avg = DB::table('pirep_fares')->whereIn('pirep_id', $allpireps)->whereIn('fare_id', $paxfares)->avg('count');
        $cgo_amount = DB::table('pirep_fares')->whereIn('pirep_id', $allpireps)->whereIn('fare_id', $cgofares)->sum('count');
        $cgo_avg = DB::table('pirep_fares')->whereIn('pirep_id', $allpireps)->whereIn('fare_id', $cgofares)->avg('count');

        if ($pax_amount > 0) {
            $stats[__('DBasic::widgets.pireps_pax')] = number_format($pax_amount);
            $stats[__('DBasic::widgets.avg_pax')] = number_format($pax_avg);
        }
        
        if ($cgo_amount > 0) {
            $stats[__('DBasic::widgets.pireps_cgo')] = number_format($cgo_amount) . ' ' . $unit_weight;
            $stats[__('DBasic::widgets.avg_cgo')] = number_format($cgo_avg) . ' ' . $unit_weight;
        }

        // Basic Pirep Statistics
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

    // Airline Finance (uses cache)
    public function AirlineFinance($journal_id)
    {
        $currency = setting('units.currency');
        $finance = [];

        // Cache
        $cache_key = 'journal-' . $journal_id . '-overall';
        $cache_until = Carbon::now()->endOfDay();

        $overall = cache()->remember($cache_key, $cache_until, function () use ($journal_id) {
            return DB::table('journal_transactions')->where('journal_id', $journal_id)
            ->selectRaw('sum(credit) as ov_credit, sum(debit) as ov_debit, sum(credit) - sum(debit) as ov_balance')
            ->first();
        });

        $income = $overall->ov_credit ?? 0;
        $expense = $overall->ov_debit ?? 0;
        $balance = $overall->ov_balance ?? 0;
        // $balance = $income - $expense;

        $color = ($balance < 0) ? 'darkred' : 'darkgreen';

        $finance[__('DBasic::common.income')] = money($income, $currency);
        $finance[__('DBasic::common.expense')] = money($expense, $currency);
        $finance[__('DBasic::common.balance')] = '<span style="color: ' . $color . ';"><b>' . money($balance, $currency) . '</b></span>';

        return $finance;
    }

    // Network Stats for IVAO/VATSIM (uses cache)
    public function NetworkStats($network = 'BOTH')
    {
        if ($network === 'IVAO') {
            $network_array = ['IVAO'];
        } elseif ($network === 'VATSIM') {
            $network_array = ['VATSIM'];
        } else {
            $network_array = ['IVAO', 'VATSIM'];
        }

        $nwstats = [];

        // Cache
        $cache_overall = 'ns-alltime-' . $network;
        $cache_last90 = 'ns-last90-' . $network;
        $cache_last180 = 'ns-last180-' . $network;
        $cache_until = Carbon::now()->endOfDay();

        // Periods
        $start90 = Carbon::now()->subDays(90);
        $start180 = Carbon::now()->subDays(180);

        $overall = cache()->remember($cache_overall, $cache_until, function () use ($network_array) {
            return DB::table('pirep_field_values')->selectRaw('value as network, count(value) as pireps')
            ->where('slug', 'network-online')
                ->whereIn('value', $network_array)
                ->groupBy('value')->get();
        });

        if (filled($overall) && $overall->count() > 0) {
            foreach ($overall as $ns) {
                $nwstats[$ns->network . ' (All Time)'] = $ns->pireps;
            }
        }

        $last90days = cache()->remember($cache_last90, $cache_until, function () use ($network_array, $start90) {
            return DB::table('pirep_field_values')->selectRaw('value as network, count(value) as pireps')
            ->where('slug', 'network-online')
                ->where('created_at', '>', $start90)
                ->whereIn('value', $network_array)
                ->groupBy('value')->get();
        });

        if (filled($last90days) && $last90days->count() > 0) {
            foreach ($last90days as $ns) {
                $nwstats[$ns->network . ' (Last 90 Days)'] = $ns->pireps;
            }
        }

        $last180days = cache()->remember($cache_last180, $cache_until, function () use ($network_array, $start180) {
            return DB::table('pirep_field_values')->selectRaw('value as network, count(value) as pireps')
            ->where('slug', 'network-online')
                ->where('created_at', '>', $start180)
                ->whereIn('value', $network_array)
                ->groupBy('value')->get();
        });

        if (filled($last180days) && $last180days->count() > 0) {
            foreach ($last180days as $ns) {
                $nwstats[$ns->network . ' (Last 180 Days)'] = $ns->pireps;
            }
        }

        ksort($nwstats, SORT_NATURAL);

        return $nwstats;
    }
}