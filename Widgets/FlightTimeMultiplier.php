<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;

class FlightTimeMultiplier extends Widget
{
    public function run()
    {
        return view('DBasic::widgets.flight_time_multiplier');
    }
}
