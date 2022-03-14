<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\PirepUpdated;
use App\Models\Enums\AircraftState;
use App\Models\Enums\PirepStatus;
use Illuminate\Support\Facades\Log;

class Pirep_Updated
{
    // Change Aircraft State: IN_AIR or IN_USE
    public function handle(PirepUpdated $event)
    {
        if (DB_Setting('dbasic.acstate_control') && $event->pirep->aircraft) {
            $pirep = $event->pirep;
            $aircraft = $pirep->aircraft;

            if ($pirep->status === PirepStatus::BOARDING) {
                $aircraft->state = AircraftState::IN_USE;
                $aircraft->save();
                Log::debug('Disposable Basic | Pirep:' . $event->pirep->id . ' BOARDING started, Changed STATE of ' . $aircraft->registration . ' to IN USE');
            } elseif ($pirep->status === PirepStatus::TAKEOFF) {
                $aircraft->state = AircraftState::IN_AIR;
                $aircraft->save();
                Log::debug('Disposable Basic | Pirep:' . $event->pirep->id . ' TAKE OFF reported, Changed STATE of ' . $aircraft->registration . ' to IN AIR');
            } elseif ($pirep->status === PirepStatus::LANDED) {
                $aircraft->state = AircraftState::IN_USE;
                $aircraft->save();
                Log::debug('Disposable Basic | Pirep:' . $event->pirep->id . ' LANDING reported, Changed STATE of ' . $aircraft->registration . ' to IN USE');
            }
        }
    }
}
