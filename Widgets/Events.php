<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Flight;
use Carbon\Carbon;

class Events extends Widget
{
    protected $config = ['type' => null];

    public function run()
    {
        $today = Carbon::today();
        $event_code = DB_Setting('dbasic.event_routecode', 'EVENT');

        if ($this->config['type'] === 'upcoming') {
            $where = [
                ['start_date', '>', $today],
                'route_code'  => $event_code,
            ];
            $widget_title = 'Upcoming Event';
        } else {
            $where = [
                'start_date'  => $today,
                'route_code'  => $event_code,
            ];
            $widget_title = "Today's Event";
        }

        $with = ['dpt_airport', 'arr_airport', 'airline'];

        $events = Flight::with($with)->where($where)->orderBy('start_date')->orderBy('dpt_time')->orderBy('flight_number')->orderBy('route_leg')->get();

        return view('DBasic::widgets.events', [
            'events'     => $events,
            'event_type' => ($this->config['type'] === 'upcoming') ? 'Upcoming' : 'Current',
            'event_text' => (count($events) > 1) ? $widget_title.'s' : $widget_title,
            'is_visible' => (count($events) > 0) ? true : false,
        ]);
    }
}
