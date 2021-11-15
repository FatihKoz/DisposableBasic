<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Illuminate\Support\Facades\DB;

// Widget Designed By MacoFallico, slightly enhanced By FatihKoz
class AirportInfo extends Widget
{
    protected $config = ['type' => 'all'];

    public function run()
    {
        $where = [];

        if ($this->config['type'] === 'hubs') {
            $where['hub'] = 1;
            $apt_route = 'DBasic.hub';
        } elseif ($this->config['type'] === 'nohubs') {
            $where['hub'] = 0;
        }

        $airports = DB::table('airports')->select('id', 'iata', 'name', 'location', 'country')->where($where)->orderBy('id')->get();

        return view('DBasic::widgets.airport_info', [
            'airports'   => $airports,
            'apt_route'  => isset($apt_route) ? $apt_route : 'frontend.airports.show',
            'config'     => $this->config,
            'is_visible' => filled($airports) ? true : false,
        ]);
    }
}
