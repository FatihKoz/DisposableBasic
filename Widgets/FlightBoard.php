<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Pirep;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;

class FlightBoard extends Widget
{
    public $reloadTimeout = 62;

    public function run()
    {
        $eager_load = ['aircraft', 'airline', 'arr_airport', 'dpt_airport', 'position', 'user'];
        $flights = Pirep::with($eager_load)->where(['state' => PirepState::IN_PROGRESS, ['status', '!=', PirepStatus::INITIATED]])->orderby('updated_at', 'desc')->get();

        return view('DBasic::widgets.flight_board', [
            'flights'    => $flights,
            'is_visible' => filled($flights) ? true : false,
        ]);
    }
}
