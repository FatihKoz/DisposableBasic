<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Bid;
use App\Models\SimBrief;

class ActiveBookings extends Widget
{
    public $reloadTimeout = 70;

    protected $config = ['source' => 'simbrief'];

    public function run()
    {
        $source = ($this->config['source'] === 'bids') ? 'bids' : 'simbrief';

        $eager_load = array('flight.airline', 'flight.arr_airport', 'flight.dpt_airport', 'user');

        if ($source === 'bids') {
            $active_bookings = Bid::with($eager_load)->orderby('created_at', 'desc')->get();
            $title = __('DBasic::widgets.active_bids');
        } else {
            $eager_load[] = 'aircraft';
            $active_bookings = SimBrief::with($eager_load)->whereNotNull(['flight_id', 'aircraft_id'])->whereNull('pirep_id')->orderby('created_at', 'desc')->get();
            $title = __('DBasic::widgets.active_sbrf');
        }

        return view('DBasic::widgets.active_bookings', [
            'active_bookings' => $active_bookings,
            'bids'            => ($source === 'bids') ? true : false,
            'expire'          => ($source === 'simbrief') ? setting('simbrief.expire_hours') : setting('bids.expire_time'),
            'is_visible'      => filled($active_bookings) ? true : false,
            'title'           => $title,
        ]);
    }
}
