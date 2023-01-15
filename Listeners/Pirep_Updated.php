<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\PirepUpdated;
use App\Models\Enums\AircraftState;
use App\Models\Enums\PirepStatus;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Models\DB_WhazzUpCheck;
use Modules\DisposableBasic\Services\DB_PirepServices;
use Carbon\Carbon;

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
                Log::info('Disposable Basic | Pirep:' . $pirep->id . ' BOARDING started, Changed STATE of ' . $aircraft->registration . ' to IN USE');
            } elseif ($pirep->status === PirepStatus::TAKEOFF) {
                $aircraft->state = AircraftState::IN_AIR;
                $aircraft->save();
                Log::info('Disposable Basic | Pirep:' . $pirep->id . ' TAKE OFF reported, Changed STATE of ' . $aircraft->registration . ' to IN AIR');
            } elseif ($pirep->status === PirepStatus::LANDED) {
                $aircraft->state = AircraftState::IN_USE;
                $aircraft->save();
                Log::info('Disposable Basic | Pirep:' . $pirep->id . ' LANDING reported, Changed STATE of ' . $aircraft->registration . ' to IN USE');
            }
        }

        if (DB_Setting('dbasic.networkcheck', false)) {
            $pirep = $event->pirep;

            if ($pirep->status === PirepStatus::ENROUTE || $pirep->status === PirepStatus::APPROACH || $pirep->status === PirepStatus::APPROACH_ICAO) {
                // Get last check
                $enroute_diff = DB_Setting('dbasic.networkcheck_enroute_margin', 300);
                $last_check = DB_WhazzUpCheck::where('pirep_id', $pirep->id)->orderBy('created_at', 'DESC')->first();
                // Compare the time difference and check Network presence
                if (empty($last_check) || isset($last_check) && ($last_check->created_at->diffInSeconds(Carbon::now()) >= $enroute_diff)) {
                    Log::info('Disposable Basic | Pirep:' . $pirep->id . ' updated. Checking Network Presence');
                    $DB_PirepSvc = app(DB_PirepServices::class);
                    $DB_PirepSvc->CheckWhazzUp($pirep);
                }
            }
        }
    }
}
