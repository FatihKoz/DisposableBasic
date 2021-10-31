<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\DisposableBasic\Models\DB_RandomFlight;
use Modules\DisposableBasic\Services\DB_FlightServices;

class RandomFlights extends Widget
{
    protected $config = ['count' => null, 'daily' => false, 'hub' => false, 'user' => null];

    public function run()
    {
        $count = (is_numeric($this->config['count']) && $this->config['count'] > 0) ? $this->config['count'] : 1;
        $daily = $this->config['daily'];
        $today = Carbon::today();
        $user = Auth::user();

        if (!$user) {

            return view('DBasic::widgets.random_flights', [
                'is_visible' => false,
            ]);
        }

        DB_RandomFlight::where('assign_date', '!=', $today)->delete();

        $orig = filled($user->curr_airport_id) ? $user->curr_airport_id : $user->home_airport_id;

        if ($this->config['hub'] === true) {
            $orig = $user->home_airport_id;
        }

        $whereRF = [];
        $whereRF['user_id'] = $user->id;
        $whereRF['assign_date'] = $today;
        if ($daily === false) {
            $whereRF['airport_id'] = $orig;
        }

        $eager_load = array('flight.airline', 'flight.dpt_airport', 'flight.arr_airport', 'pirep', 'user');
        $rfs = DB_RandomFlight::with($eager_load)->where($whereRF)->get();

        if ($rfs->isEmpty()) {
            $FlightSvc = app(DB_FlightServices::class);
            $rfs = $FlightSvc->PickRandomFlights($user, $orig, $count, $whereRF, $eager_load);
        }

        return view('DBasic::widgets.random_flights', [
            'random_flights' => $rfs,
            'today'          => $today,
            'is_visible'     => (count($rfs) > 0) ? true : false,
        ]);
    }
}
