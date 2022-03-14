<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\PirepCancelled;
use App\Models\Enums\AircraftState;
use Illuminate\Support\Facades\Log;

class Pirep_Cancelled
{
    // Change Aircraft State: PARKED (On Ground)
    public function handle(PirepCancelled $event)
    {
        if (DB_Setting('dbasic.acstate_control') && $event->pirep->aircraft) {
            $aircraft = $event->pirep->aircraft;
            $aircraft->state = AircraftState::PARKED;
            $aircraft->save();
            Log::debug('Disposable Basic | Pirep:' . $event->pirep->id . ' CANCELLED, Changed STATE of ' . $aircraft->registration . ' to PARKED');
        }
    }
}
