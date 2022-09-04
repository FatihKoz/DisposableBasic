<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\PirepUpdated;
use App\Models\Enums\AircraftState;
use App\Models\Enums\PirepStatus;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Services\DB_PirepServices;

class Pirep_Updated
{
    public function handle(PirepUpdated $event)
    {
        if (DB_Setting('dbasic.acstate_control', false) && $event->pirep->aircraft) {
            // Change Aircraft State
            $pirep = $event->pirep;
            $aircraft = $pirep->aircraft;

            if ($pirep->status === PirepStatus::BOARDING) {
                $aircraft->state = AircraftState::IN_USE;
                $aircraft->save();
                Log::debug('Disposable Basic | Pirep:' . $pirep->id . ' BOARDING started, Changed STATE of ' . $aircraft->registration . ' to IN USE');
            } elseif ($pirep->status === PirepStatus::TAKEOFF) {
                $aircraft->state = AircraftState::IN_AIR;
                $aircraft->save();
                Log::debug('Disposable Basic | Pirep:' . $pirep->id . ' TAKE OFF reported, Changed STATE of ' . $aircraft->registration . ' to IN AIR');
            } elseif ($pirep->status === PirepStatus::LANDED) {
                $aircraft->state = AircraftState::IN_USE;
                $aircraft->save();
                Log::debug('Disposable Basic | Pirep:' . $pirep->id . ' LANDING reported, Changed STATE of ' . $aircraft->registration . ' to IN USE');
            }
        }

        if (DB_Setting('dbasic.networkcheck', false)) {
            // Check Network Presence
            $pirep = $event->pirep;

            if ($pirep->status === PirepStatus::CANCELLED || $pirep->status === PirepStatus::INITIATED || $pirep->status === PirepStatus::PAUSED) {
                // Do Nothing
            } else {
                Log::debug('Disposable Basic | Pirep:' . $pirep->id . ' Status:' . $pirep->status . ' reported. Checking Network Presence');
                $DB_PirepSvc = app(DB_PirepServices::class);
                $DB_PirepSvc->CheckWhazzUp($pirep);
            }
        }
    }
}
