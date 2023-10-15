<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Illuminate\Support\Facades\Auth;

// Widget Designed By MacoFallico, slightly enhanced By FatihKoz
class AirportInfo extends Widget
{
    protected $config = ['type' => 'all'];

    public function run()
    {
        if ($this->config['type'] === 'hubs') {
            $hubs = true;
            $apt_route = 'DBasic.hub';
        } else {
            $hubs = false;
        }

        return view('DBasic::widgets.airport_info', [
            'apt_route'  => isset($apt_route) ? $apt_route : 'frontend.airports.show',
            'config'     => $this->config,
            'hubs_only'  => ($hubs === true) ? 'hubs_only': null,
            'is_visible' => Auth::check(),
        ]);
    }
}
