<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepSource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PersonalStats extends Widget
{
    protected $config = ['disp' => null, 'user' => null, 'period' => null, 'type' => 'avglanding'];

    public function run()
    {
        $user_id = $this->config['user'] ?? Auth::id();
        $period = $this->config['period'];
        $type = $this->config['type'];

        $now = Carbon::now();
        $current_year = $now->copy()->format('Y');

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
        if ($period === 'currenty' || $period === 'lasty' || $period === 'prevy') {
            // Years
            $s_date = $b_date->startOfYear();
            $e_date = $b_date->copy()->endOfYear();
            $period_text = $b_date->format('Y');
        } elseif ($period === 'currentm' || $period === 'lastm' || $period === 'prevm') {
            // Months
            $s_date = $b_date->startOfMonth();
            $e_date = $b_date->copy()->endOfMonth();
            $period_text = $b_date->format('F');
        } elseif (is_numeric($period)) {
            // Days
            $s_date = $b_date->copy()->subdays($period);
            $e_date = $now;
            $period_text = __('DBasic::widgets.lastndays', ['period' => $period]);
        } elseif ($period === 'q1') {
            // Quarter 1 JAN-FEB-MAR
            $s_date = $current_year . '-01-01 00:00:01';
            $e_date = $current_year . '-03-31 23:59:59';
            $period_text = $current_year . '/' . strtoupper($period);
        } elseif ($period === 'q2') {
            // Quarter 2 APR-MAY-JUN
            $s_date = $current_year . '-04-01 00:00:01';
            $e_date = $current_year . '-06-30 23:59:59';
            $period_text = $current_year . '/' . strtoupper($period);
        } elseif ($period === 'q3') {
            // Quarter 3 JUL-AUG-SEP
            $s_date = $current_year . '-07-01 00:00:01';
            $e_date = $current_year . '-09-30 23:59:59';
            $period_text = $current_year . '/' . strtoupper($period);
        } elseif ($period === 'q4') {
            // Quarter 4 OCT-NOV-DEC
            $s_date = $current_year . '-10-01 00:00:01';
            $e_date = $current_year . '-12-31 23:59:59';
            $period_text = $current_year . '/' . strtoupper($period);
        } else {
            // Overall
            $s_date = '1978-07-15 00:00:01';
            $e_date = $now;
        }

        $where = [];
        $where['user_id'] = $user_id;
        $where['state'] = PirepState::ACCEPTED;

        // Average Landing Rate - Acars Only
        if ($type === 'avglanding') {
            $where['source'] = PirepSource::ACARS;
            $select_raw = 'avg(landing_rate)';
            $stat_name = __('DBasic::widgets.avglanding');
        }
        // Average Score - Acars Only
        elseif ($type === 'avgscore') {
            $where['source'] = PirepSource::ACARS;
            $select_raw = 'avg(score)';
            $stat_name = __('DBasic::widgets.avgscore');
        }
        // Average Distance
        elseif ($type === 'avgdistance') {
            $select_raw = 'avg(distance)';
            $stat_name = __('DBasic::widgets.avgdistance');
        }
        // Total Distance
        elseif ($type === 'totdistance') {
            $select_raw = 'sum(distance)';
            $stat_name = __('DBasic::widgets.totdistance');
        }
        // Average Time
        elseif ($type === 'avgtime') {
            $select_raw = 'avg(flight_time)';
            $stat_name = __('DBasic::widgets.avgtime');
        }
        // Total Time
        elseif ($type === 'tottime') {
            $select_raw = 'sum(flight_time)';
            $stat_name = __('DBasic::widgets.tottime');
        }
        // Average Fuel
        elseif ($type === 'avgfuel') {
            $select_raw = 'avg(fuel_used)';
            $stat_name = __('DBasic::widgets.avgfuel');
        }
        // Total Fuel
        elseif ($type === 'totfuel') {
            $select_raw = 'sum(fuel_used)';
            $stat_name = __('DBasic::widgets.totfuel');
        }
        // Total Flights
        elseif ($type === 'totflight') {
            $select_raw = 'count(id)';
            $stat_name = __('DBasic::widgets.totflight');
        }

        // Execute
        $result = DB::table('pireps')->selectRaw($select_raw . ' as uresult')->where($where)->whereBetween('submitted_at', [$s_date, $e_date])->value('uresult');

        // Format the result according to type
        if ($type === 'avglanding') {
            $result = number_format($result) . ' ft/min';
        } elseif ($type === 'avgscore') {
            $result = number_format($result);
        } elseif ($type === 'avgtime' || $type === 'tottime') {
            $result = round($result);
            // $result = Dispo_TimeConvert($result);
        } elseif ($type === 'avgdistance' || $type === 'totdistance') {
            if (setting('units.distance') === 'km') {
                $result = number_format($result * 1.852) . ' km';
            } else {
                $result = number_format($result) . ' nm';
            }
        } elseif ($type === 'avgfuel' || $type === 'totfuel') {
            if (setting('units.fuel') === 'kg') {
                $result = number_format($result / 2.20462262185) . ' kg';
            } else {
                $result = number_format($result) . ' lbs';
            }
        }

        return view('DBasic::widgets.personal_stats', [
            'pstat'   => isset($result) ? $result : __('DBasic::widgets.noreports'),
            'sname'   => isset($stat_name) ? $stat_name : null,
            'speriod' => isset($period_text) ? '(' . $period_text . ')' : null,
            'config'  => $this->config,
        ]);
    }
}
