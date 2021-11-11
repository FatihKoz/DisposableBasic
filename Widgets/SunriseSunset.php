<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Services\DB_AirportServices;

class SunriseSunset extends Widget
{
    protected $config = ['location' => null, 'type' => null, 'card' => true];

    public function run()
    {
        $location = $this->config['location'];
        $type = ($this->config['type'] === 'civil') ? 'civil' : 'nautical';

        $icon = 'fa-bomb';
        $airport = DB::table('airports')->select('id', 'name', 'location', 'lat', 'lon')->where('id', $location)->first();

        if (!$airport) {
            $error = 'Airport not found!';

            return view('DBasic::widgets.sunrise_sunset', [
                'error' => $error,
                'icon'  => $icon,
            ]);
        }

        $AirportSvc = app(DB_AirportServices::class);
        $details = $AirportSvc->SunriseSunset($airport, $type);

        $icon = array_key_exists('daylight', $details) ? 'fa-sun' : 'fa-moon';

        return view('DBasic::widgets.sunrise_sunset', [
            'card_view'      => is_bool($this->config['card']) ? $this->config['card'] : true,
            'details'        => $details,
            'icon'           => $icon,
            'footer_note'    => array_key_exists('location', $details) ? $details['location'] : null,
            'location'       => isset($location) ? $location : null,
        ]);
    }
}
