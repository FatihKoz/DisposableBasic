<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\PirepStatusChange;
use App\Models\Enums\PirepStatus;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Services\DB_PirepServices;

class Pirep_StatusChange
{

    public function handle(PirepStatusChange $event)
    {
        if (DB_Setting('dbasic.networkcheck', false)) {
            $pirep = $event->pirep;

            if ($pirep->status === PirepStatus::CANCELLED || $pirep->status === PirepStatus::PAUSED) {
                // Do Nothing
            } else {
                // Check Network Presence
                Log::info('Disposable Basic | Pirep:' . $pirep->id . ' Status:' . $pirep->status . ' reported. Checking Network Presence');
                $DB_PirepSvc = app(DB_PirepServices::class);
                $DB_PirepSvc->CheckWhazzUp($pirep);
            }
        }
    }
}
