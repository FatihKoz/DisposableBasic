<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\PirepFiled;
use App\Models\Enums\AircraftState;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Services\DB_NotificationServices;

class Pirep_Filed
{
    // Change Aircraft State: PARKED
    public function handle(PirepFiled $event)
    {
        $pirep = $event->pirep;
        // Send Discord Notification
        if (DB_Setting('dbasic.discord_pirepmsg')) {
            $NotificationSvc = app(DB_NotificationServices::class);
            $NotificationSvc->PirepMessage($pirep, 'New flight report received');
        }

        if (DB_Setting('dbasic.acstate_control') && $pirep->aircraft) {
            $aircraft = $pirep->aircraft;
            $aircraft->state = AircraftState::PARKED;
            $aircraft->save();
            Log::debug('Disposable Basic | Pirep:' . $event->pirep->id . ' FILED, Change STATE of ' . $aircraft->registration . ' to PARKED');
        }
    }
}
