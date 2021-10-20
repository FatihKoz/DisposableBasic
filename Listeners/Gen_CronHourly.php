<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\CronHourly;
use App\Models\Aircraft;
use App\Models\Enums\AircraftState;
use App\Models\Enums\PirepState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Gen_CronHourly
{
  public function handle(CronHourly $event)
  {
    $this->ReleaseStuckAircraft();
  }

  public function ReleaseStuckAircraft()
  {
    $live_aircraft = DB::table('pireps')->select('aircraft_id')->where('state', PirepState::IN_PROGRESS)->orWhere('state', PirepState::PAUSED)->pluck('aircraft_id')->toArray();
    $blocked_aircraft = Aircraft::where('state', '!=', AircraftState::PARKED)->whereNotIn('id', $live_aircraft)->get();

    foreach ($blocked_aircraft as $aircraft) {
      $aircraft->state = AircraftState::PARKED;
      $aircraft->save();
      Log::debug('CRON, Disposable Basic | Aircraft: '.$aircraft->registration.' STATE changed to PARKED');
    }
  }
}
