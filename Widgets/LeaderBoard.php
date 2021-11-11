<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Carbon\Carbon;
use Modules\DisposableBasic\Services\DB_StatServices;

class LeaderBoard extends Widget
{
    protected $config = ['source' => 'pilot', 'count' => 3, 'type' => 'flights', 'period' => null, 'hub' => null];

    public function run()
    {
        $now = Carbon::now();
        $source = $this->config['source'];
        $count = is_numeric($this->config['count']) ? $this->config['count'] : 3;
        $type = $this->config['type'];
        $period = $this->config['period'];
        $hub = $this->config['hub'];

        // Title and icon
        if ($source === 'airline') {
            $title = ($count === 1) ? __('DBasic::widgets.best_airline') : __('DBasic::widgets.top_airlines');
            $icon = ($count === 1) ? 'fa-trophy' : 'fa-list';
        } elseif ($source === 'arr') {
            $title = ($count === 1) ? __('DBasic::widgets.best_airport') : __('DBasic::widgets.top_airports');
            $footer_note = __('DBasic::common.arrival');
            $icon = 'fa-plane-arrival';
        } elseif ($source === 'dep') {
            $title = ($count === 1) ? __('DBasic::widgets.best_airport') : __('DBasic::widgets.top_airports');
            $footer_note = __('DBasic::common.departure');
            $icon = 'fa-plane-departure';
        } else {
            $title = ($count === 1) ? __('DBasic::widgets.best_pilot') : __('DBasic::widgets.top_pilots');
            $footer_note = isset($hub) ? $hub . ' (' . __('DBasic::common.hub') . ')' : null;
            $icon = ($count === 1) ? 'fa-crown' : 'fa-list-ol';
        }

        // Period text (visible at Card Header)
        if ($period === 'currentm') {
            $period_text = $now->startOfMonth()->format('F');
        } elseif ($period === 'lastm') {
            $period_text = $now->subMonthNoOverflow()->startOfMonth()->format('F');
        } elseif ($period === 'prevm') {
            $period_text = $now->subMonthsNoOverflow(2)->startOfMonth()->format('F');
        } elseif ($period === 'currenty') {
            $period_text = $now->startOfYear()->format('Y');
        } elseif ($period === 'lasty') {
            $period_text = $now->subYearNoOverflow()->startOfYear()->format('Y');
        } elseif ($period === 'prevy') {
            $period_text = $now->subYearsNoOverflow(2)->startOfYear()->format('Y');
        }

        if (isset($period_text)) {
            $title = $title . ' | ' . $period_text;
        }

        // Type text (visible at Card Footer)
        if ($type === 'distance') {
            $type_text = __('DBasic::common.distance');
        } elseif ($type === 'time') {
            $type_text = __('DBasic::common.btime');
        } elseif ($type === 'lrate') {
            $type_text = __('DBasic::common.lrate');
        } elseif ($type === 'lrate_low') {
            $type_text = __('DBasic::widgets.lrate_low');
        } elseif ($type === 'lrate_high') {
            $type_text = __('DBasic::widgets.lrate_hgh');
        } elseif ($type === 'score') {
            $type_text = __('DBasic::common.score');
        } else {
            $type_text = __('DBasic::common.flights');
        }

        $StatSvc = app(DB_StatServices::class);
        $leader_board = $StatSvc->LeaderBoard($source, $count, $type, $period, $hub);

        return view('DBasic::widgets.leader_board', [
            'column_title' => ($type === 'lrate' || $type === 'score') ? __('DBasic::common.avg') : __('DBasic::common.record'),
            'count'        => $count,
            'footer_type'  => $type_text,
            'footer_note'  => isset($footer_note) ? $footer_note : null,
            'header_icon'  => $icon,
            'header_title' => $title,
            'leader_board' => $leader_board,
        ]);
    }

    public function placeholder()
    {
        $loading_style = '<div class="alert alert-info mb-2 p-1 px-2 small fw-bold"><div class="spinner-border spinner-border-sm text-dark me-2" role="status"></div>Loading Leader Board data...</div>';
        return $loading_style;
    }
}
