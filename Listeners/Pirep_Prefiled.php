<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\PirepPrefiled;
use App\Models\Enums\AircraftState;
use Illuminate\Support\Facades\Log;

class Pirep_Prefiled
{
    // Change Aircraft State : IN_USE
    // Only for MANUAL Pireps! ACARS Pireps will use PirepUpdated Event
    public function handle(PirepPrefiled $event)
    {
        if (DB_Setting('dbasic.acstate_control') && $event->pirep->aircraft && $event->pirep->source === 0) {
            $aircraft = $event->pirep->aircraft;
            $aircraft->state = AircraftState::IN_USE;
            $aircraft->save();
            Log::info('Disposable Basic | Pirep:' . $event->pirep->id . ' PRE-FILED, Changed STATE of ' . $aircraft->registration . ' to IN USE');
        }
    }
}
