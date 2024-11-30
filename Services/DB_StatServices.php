<?php

namespace Modules\DisposableBasic\Services;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\JournalTransaction;
use App\Models\Pirep;
use App\Models\PirepFare;
use App\Models\PirepFieldValue;
use App\Models\Subfleet;
use App\Models\User;
use App\Models\Enums\AircraftStatus;
use App\Models\Enums\FareType;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Enums\UserState;
use App\Support\Units\Distance;
use App\Support\Units\Fuel;
use App\Support\Units\Mass;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DB_StatServices
{
    // Personal Statistics (uses cache)
    public function PersonalStats($user_id, $period, $type)
    {
        $personal = [];

        $now = Carbon::now()->locale(app()->getLocale());
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
            $personal['period_text'] = $b_date->isoFormat('MMMM'); // __('DBasic::dates.' . $b_date->format('m'));
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
        $where['deleted_at'] = null;

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
            unset($where['deleted_at']);
            $table_name = 'disposable_sap_reports';
            $date_field = 'created_at';
            $personal['stat_name'] = __('DBasic::widgets.fdm');
        } elseif ($type === 'assignment') {
            $select_raw = '(100 * sum(if(pirep_id IS NOT NULL, 1, 0))) / count(user_id)';
            unset($where['state']);
            unset($where['deleted_at']);
            $table_name = 'disposable_assignments';
            $date_field = 'updated_at';
            $personal['stat_name'] = __('DBasic::widgets.assignment');
        }

        // Execute
        $result = cache()->remember(
            $cache_key,
            $cache_until,
            function () use ($select_raw, $where, $period, $s_date, $e_date, $table_name, $date_field) {
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
            $personal['formatted'] = number_format($result) . '%';
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
            $whereIn_array = Airline::where('active', 1)->pluck('id')->toArray();
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
            $whereIn_array = User::where($user_where)->whereIn('state', $user_states)->pluck('id')->toArray();
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
        $results = cache()->remember(
            $cache_key,
            $cache_until,
            function () use ($eager_load, $base, $select_Raw, $where, $is_period, $s_date, $e_date, $whereIn_array, $type, $count) {
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
                'pilot_ident'  => ($source === 'pilot') ? $item->user->ident . ' - ' : null,
                'route'        => $route,
                'totals'       => $item->totals,
            ];
        }

        return $leader_board;
    }

    // Basic Statistics
    public function BasicStats($airline_id = null)
    {
        $stats = new Collection;

        $where = [];
        if (isset($airline_id)) {
            $where['airline_id'] = $airline_id;
        }

        $subfleets_array = Subfleet::where($where)->pluck('id')->toArray();

        $stats->put('airlines_desc', empty($airline_id) ? __('DBasic::common.airlines') : null);
        $stats->put('airlines_value', empty($airline_id) ? (int) Airline::where('active', 1)->count() : null);

        $stats->put('pilots_desc', __('DBasic::common.pilots'));
        $stats->put('pilots_value', (int) User::where($where)->count());

        $stats->put('subfleets_desc', __('DBasic::common.subfleets'));
        $stats->put('subfleets_value', count($subfleets_array));

        $stats->put('aircraft_desc', __('DBasic::common.aircraft'));
        $stats->put('aircraft_value', (int) Aircraft::whereIn('subfleet_id', $subfleets_array)->count());

        $stats->put('flights_desc', __('DBasic::common.flights'));
        $stats->put('flights_value', (int) Flight::where($where)->count());

        $stats->put('airports_desc', __('DBasic::common.airports'));
        $stats->put('airports_value', (int) Airport::count());

        $stats->put('hubs_desc', __('DBasic::common.hubs'));
        $stats->put('hubs_value', (int) Airport::where('hub', 1)->count());

        return $stats->toArray();
    }

    // Pirep Statistics
    public function PirepStats($airline_id = null, $aircraft_id = null)
    {
        $stats = new Collection;
        $unit_weight = setting('units.weight');

        $where = [];
        $where['state'] = PirepState::ACCEPTED;
        if (isset($airline_id)) {
            $where['airline_id'] = $airline_id;
        } elseif (isset($aircraft_id)) {
            $where['aircraft_id'] = $aircraft_id;
        }

        $stats->put('pireps_desc', __('DBasic::widgets.pireps_ack'));
        $stats->put('pireps_value', (int) Pirep::where($where)->count());

        // Return empty array if pirep count is zero, no need to work for the rest
        if ($stats['pireps_value'] === 0) {
            return array();
        }

        // Count carried PAX and CGO for fancy stats, skip not accepted pireps
        $skipped_pireps = Pirep::where('state', '!=', PirepState::ACCEPTED)->when(isset($airline_id), function ($query) use ($airline_id) {
            $query->where('airline_id', $airline_id);
        })->when(isset($aircraft_id), function ($query) use ($aircraft_id) {
            $query->where('aircraft_id', $aircraft_id);
        })->pluck('id')->toArray();

        if (count($skipped_pireps) < 65500 && !isset($aircraft_id)) {
            $pax_amount = PirepFare::where('type', FareType::PASSENGER)->whereNotIn('pirep_id', $skipped_pireps)->sum('count');
            $pax_avg = PirepFare::where('type', FareType::PASSENGER)->whereNotIn('pirep_id', $skipped_pireps)->avg('count');
            $cgo_amount = PirepFare::where('type', FareType::CARGO)->whereNotIn('pirep_id', $skipped_pireps)->sum('count');
            $cgo_avg = PirepFare::where('type', FareType::CARGO)->whereNotIn('pirep_id', $skipped_pireps)->avg('count');
        } else {
            $pax_amount = 0;
            $cgo_amount = 0;
            $pax_avg = 0;
            $cgo_avg = 0;
        }

        $stats->put('pax_desc', __('DBasic::widgets.pireps_pax'));
        $stats->put('pax_value', (int) $pax_amount);
        $stats->put('pax_avg_desc', __('DBasic::widgets.avg_pax'));
        $stats->put('pax_avg_value', (float) $pax_avg);

        $stats->put('cgo_desc', __('DBasic::widgets.pireps_cgo'));
        $stats->put('cgo_value', new Mass($cgo_amount, $unit_weight));
        $stats->put('cgo_avg_desc', __('DBasic::widgets.avg_cgo'));
        $stats->put('cgo_avg_value', new Mass($cgo_avg, $unit_weight));

        // Basic Pirep Statistics (Totals)
        $total_time = Pirep::where($where)->sum('flight_time');
        $total_dist = Pirep::where($where)->sum('distance');
        $total_fuel = Pirep::where($where)->sum('fuel_used');

        // Basic Pirep Statistics (Averages)
        $average_time = Pirep::where($where)->avg('flight_time');
        $average_dist = Pirep::where($where)->avg('distance');
        $average_fuel = Pirep::where($where)->avg('fuel_used');

        // Flight Time
        $stats->put('time_desc', __('DBasic::widgets.ttime'));
        $stats->put('time_value', (int) $total_time);
        $stats->put('time_avg_desc', __('DBasic::widgets.atime'));
        $stats->put('time_avg_value', (float) $average_time);

        // Fuel Usage
        $stats->put('fuel_desc', __('DBasic::widgets.tfuel'));
        $stats->put('fuel_value', new Fuel($total_fuel, config('phpvms.internal_units.fuel')));
        $stats->put('fuel_avg_desc', __('DBasic::widgets.afuel'));
        $stats->put('fuel_avg_value', new Fuel($average_fuel, config('phpvms.internal_units.fuel')));
        $stats->put('fuel_perhour_desc', __('DBasic::widgets.hfuel'));
        $perhourfuel = ($total_fuel > 0 && $total_time > 0) ? ($total_fuel / $total_time) * 60 : 0;
        $stats->put('fuel_perhour_value', new Fuel($perhourfuel, config('phpvms.internal_units.fuel')));

        // Distance Flown
        $stats->put('dist_desc', __('DBasic::widgets.tdist'));
        $stats->put('dist_value', new Distance($total_dist, config('phpvms.internal_units.distance')));
        $stats->put('dist_avg_desc', __('DBasic::widgets.adist'));
        $stats->put('dist_avg_value', new Distance($average_dist, config('phpvms.internal_units.distance')));
        $stats->put('dist_perhour_desc', __('DBasic::widgets.hdist'));
        $perhourdist = ($total_dist > 0 && $total_time > 0) ? ($total_dist / $total_time) * 60 : 0;
        $stats->put('dist_perhour_value', new Distance($perhourdist, config('phpvms.internal_units.distance')));

        $where['source'] = PirepSource::ACARS;

        // Landing Rate
        $average_lrate = Pirep::where($where)->avg('landing_rate');
        $stats->put('lrate_avg_desc', __('DBasic::widgets.alrate'));
        $stats->put('lrate_avg_value', abs($average_lrate));

        // Pirep Score
        $average_score = Pirep::where($where)->avg('score');
        $stats->put('score_avg_desc', __('DBasic::widgets.ascore'));
        $stats->put('score_avg_value', (float) $average_score);

        return $stats->toArray();
    }

    // Airline Finance (uses cache)
    public function AirlineFinance($journal_id)
    {
        $finance = new Collection;

        // Cache
        $cache_key = 'journal-' . $journal_id . '-overall';
        $cache_until = Carbon::now()->endOfDay();

        $overall = cache()->remember($cache_key, $cache_until, function () use ($journal_id) {
            return JournalTransaction::where('journal_id', $journal_id)
            ->selectRaw('sum(credit) as ov_credit, sum(debit) as ov_debit, sum(credit) - sum(debit) as ov_balance')
            ->first();
        });

        $income = $overall->ov_credit ?? 0;
        $expense = $overall->ov_debit ?? 0;
        $balance = $overall->ov_balance ?? 0;

        $finance->put('income_desc', __('DBasic::common.income'));
        $finance->put('income_value', (float) $income);
        $finance->put('expense_desc', __('DBasic::common.expense'));
        $finance->put('expense_value', (float) $expense);
        $finance->put('balance_desc', __('DBasic::common.balance'));
        $finance->put('balance_value', (float) $balance);

        return $finance->toArray();
    }

    // Network Stats for IVAO/VATSIM (uses cache)
    public function NetworkStats($network = 'BOTH')
    {
        // $pireps = Pirep::where('state', PirepState::ACCEPTED)->pluck('id')->toArray();
        $pireps = Pirep::onlyTrashed()->pluck('id')->toArray();

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

        $overall = cache()->remember($cache_overall, $cache_until, function () use ($network_array, $pireps) {
            return PirepFieldValue::selectRaw('value as network, count(value) as pireps')
                ->where('slug', 'network-online')
                ->whereIn('value', $network_array)
                ->whereNotIn('pirep_id', $pireps)
                ->groupBy('value')->get();
        });

        if (filled($overall) && $overall->count() > 0) {
            foreach ($overall as $ns) {
                $nwstats[$ns->network . ' (All Time)'] = $ns->pireps;
            }
        }

        $last90days = cache()->remember($cache_last90, $cache_until, function () use ($network_array, $pireps, $start90) {
            return PirepFieldValue::selectRaw('value as network, count(value) as pireps')
                ->where('slug', 'network-online')
                ->where('created_at', '>', $start90)
                ->whereIn('value', $network_array)
                ->whereNotIn('pirep_id', $pireps)
                ->groupBy('value')->get();
        });

        if (filled($last90days) && $last90days->count() > 0) {
            foreach ($last90days as $ns) {
                $nwstats[$ns->network . ' (Last 90 Days)'] = $ns->pireps;
            }
        }

        $last180days = cache()->remember($cache_last180, $cache_until, function () use ($network_array, $pireps, $start180) {
            return PirepFieldValue::selectRaw('value as network, count(value) as pireps')
                ->where('slug', 'network-online')
                ->where('created_at', '>', $start180)
                ->whereIn('value', $network_array)
                ->whereNotIn('pirep_id', $pireps)
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

    // Api Basic Statistics
    // No formatting or text on values only convert to local units per settings
    public function ApiBasicStats()
    {
        $user_states = [UserState::PENDING, UserState::ACTIVE, UserState::ON_LEAVE];
        $aircraft_status = [AircraftStatus::ACTIVE, AircraftStatus::MAINTENANCE, AircraftStatus::STORED];
        $stats = [];

        $stats['basic_airlines'] = Airline::where('active', 1)->count();
        $stats['basic_users'] = User::whereIn('state', $user_states)->count();
        $stats['basic_subfleets'] = Subfleet::count();
        $stats['basic_aircraft'] = Aircraft::whereIn('status', $aircraft_status)->count();
        $stats['basic_flights'] = Flight::where('active', 1)->count();
        $stats['basic_airports'] = Airport::count();
        $stats['basic_hubs'] = Airport::where('hub', 1)->count();

        return $stats;
    }

    // Api Pirep Statistics for API usage
    // No formatting or text on values only convert to local units per settings
    public function ApiPirepStats()
    {
        $stats = [];
        $unit_distance = setting('units.distance');
        $unit_weight = setting('units.weight');
        $unit_fuel = setting('units.fuel');

        $where = [];
        $where['state'] = PirepState::ACCEPTED;

        // Pirep Count
        $stats['pireps_count'] = Pirep::where($where)->count();

        if ($stats['pireps_count'] === 0) {
            return [];
        }

        // Pirep carried PAX and CGO
        $allpireps = Pirep::where('state', '!=', PirepState::ACCEPTED)->pluck('id')->toArray();

        if (count($allpireps) < 65500) {
            $pax_amount = PirepFare::where('type', FareType::PASSENGER)->whereNotIn('pirep_id', $allpireps)->sum('count');
            $pax_avg = PirepFare::where('type', FareType::PASSENGER)->whereNotIn('pirep_id', $allpireps)->avg('count');
            $cgo_amount = PirepFare::where('type', FareType::CARGO)->whereNotIn('pirep_id', $allpireps)->sum('count');
            $cgo_avg = PirepFare::where('type', FareType::CARGO)->whereNotIn('pirep_id', $allpireps)->avg('count');
        } else {
            $pax_amount = 0;
            $cgo_amount = 0;
        }

        if ($pax_amount > 0) {
            $stats['pireps_pax_ttl'] = round($pax_amount, 0);
            $stats['pireps_pax_avg'] = round($pax_avg, 0);
        }

        if ($cgo_amount > 0) {
            $stats['pireps_cgo_ttl'] = round($cgo_amount, 0);
            $stats['pireps_cgo_avg'] = round($cgo_avg, 0);
            $stats['pireps_cgo_unt'] = $unit_weight;
        }

        // Pirep Times
        $stats['pireps_time_ttl'] = round(Pirep::where($where)->sum('flight_time'), 0);
        $stats['pireps_time_avg'] = round(Pirep::where($where)->avg('flight_time'), 0);
        $stats['pireps_time_unt'] = "min";

        // Pirep Distance
        $total_dist = Pirep::where($where)->sum('distance');
        $average_dist = Pirep::where($where)->avg('distance');

        if ($unit_distance === 'km') {
            $total_dist = $total_dist * 1.852;
            $average_dist = $average_dist * 1.852;
        } elseif ($unit_distance === 'mi') {
            $total_dist = $total_dist * 1.15078;
            $average_dist = $average_dist * 1.15078;
        }

        $stats['pireps_dist_ttl'] = round($total_dist, 0);
        $stats['pireps_dist_avg'] = round($average_dist, 0);
        $stats['pireps_dist_unt'] = $unit_distance;

        // Pirep Fuel
        $total_fuel = Pirep::where($where)->sum('fuel_used');
        $average_fuel = Pirep::where($where)->avg('fuel_used');

        if ($unit_fuel === 'kg') {
            $total_fuel = $total_fuel / 2.20462262185;
            $average_fuel = $average_fuel / 2.20462262185;
        }

        $stats['pireps_fuel_ttl'] = round($total_fuel, 0);
        $stats['pireps_fuel_avg'] = round($average_fuel, 0);
        $stats['pireps_fuel_unt'] = $unit_fuel;

        return $stats;
    }
}
