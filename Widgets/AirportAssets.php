<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Modules\DisposableBasic\Services\DB_AirportServices;

class AirportAssets extends Widget
{
    protected $config = ['location' => 'ZZZZ', 'type' => 'aircraft', 'count' => null];

    public function run()
    {
        $location = $this->config['location'];
        $type = $this->config['type'];
        $count = is_numeric($this->config['count']) ? $this->config['count'] : 50;
        $title = __('DBasic::widgets.airport_assets');
        $icon = 'fa-bomb';
        $units = array('weight' => setting('units.weight'), 'fuel' => setting('units.fuel'));

        $AirportSvc = app(DB_AirportServices::class);

        if ($type === 'pireps') {
            $title = __('DBasic::common.reports');
            $icon = 'fa-file-upload';
            $assets = $AirportSvc->GetPireps($location, $count);
        } elseif ($type === 'pilots') {
            $title = __('DBasic::common.pilots');
            $icon = 'fa-users';
            $assets = $AirportSvc->GetPilots($location, $count);
            $total_time = setting('pilots.count_transfer_hours') ? true : false;
        } else {
            $title = __('DBasic::common.aircraft');
            $icon = 'fa-plane';
            $assets = $AirportSvc->GetAircraft($location, $count);
        }

        return view('DBasic::widgets.airport_assets', [
            'assets'     => isset($assets) ? $assets : null,
            'count'      => is_countable($assets) ? $assets->count() : 0,
            'icon'       => $icon,
            'is_visible' => filled($assets) ? true : false,
            'location'   => $location,
            'title'      => $title,
            'total_time' => isset($total_time) ? $total_time : null,
            'type'       => $type,
            'units'      => $units,
        ]);
    }
}
