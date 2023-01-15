<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\PirepCancelled;
use App\Models\Enums\AircraftState;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Models\DB_WhazzUpCheck;

class Pirep_Cancelled
{
    public function handle(PirepCancelled $event)
    {
        if (DB_Setting('dbasic.acstate_control') && $event->pirep->aircraft) {
            // Park Aircraft
            $aircraft = $event->pirep->aircraft;
            $aircraft->state = AircraftState::PARKED;
            $aircraft->save();
            Log::info('Disposable Basic | Pirep:' . $event->pirep->id . ' CANCELLED, Changed STATE of ' . $aircraft->registration . ' to PARKED');
        }

        if (DB_Setting('dbasic.networkcheck', false)) {
            // Delete Crap Data
            $pirep = $event->pirep;
            Log::info('Disposable Basic | Pirep:' . $pirep->id . ' Status:' . $pirep->status . ' reported. Deleting Network Presence Check Data');
            DB_WhazzUpCheck::where('pirep_id', $pirep->id)->delete();
        }
    }
}
