<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\SimBrief;

class ActiveBookings extends Widget
{
  // Set Widget Auto Refresh Time (Seconds)
  public $reloadTimeout = 90;

  public function run()
  {
    $eager_load = array('aircraft', 'flight.airline', 'flight.arr_airport', 'flight.dpt_airport', 'user');
    $active_bookings = SimBrief::with($eager_load)->whereNotNull('flight_id')->whereNotNull('aircraft_id')->whereNull('pirep_id')->orderby('created_at', 'desc')->get();

    return view('DBasic::widgets.active_bookings', [
      'active_bookings' => $active_bookings,
      'is_visible'      => isset($active_bookings) ? true : false,
    ]);
  }
}
