<?php

namespace Modules\DisposableBasic\Listeners;

use App\Events\PirepFiled;
use App\Models\PirepFieldValue;
use App\Models\Enums\AircraftState;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Models\DB_WhazzUpCheck;
use Modules\DisposableBasic\Services\DB_NotificationServices;

class Pirep_Filed
{
    public function handle(PirepFiled $event)
    {
        $pirep = $event->pirep;

        if (DB_Setting('dbasic.discord_pirepmsg')) {
            // Send Discord Notification
            $NotificationSvc = app(DB_NotificationServices::class);
            $NotificationSvc->PirepMessage($pirep, 'New flight report received');
        }

        if (DB_Setting('dbasic.acstate_control') && $pirep->aircraft) {
            // Change Aircraft State: PARKED
            $aircraft = $pirep->aircraft;
            $aircraft->state = AircraftState::PARKED;
            $aircraft->save();
            Log::debug('Disposable Basic | Pirep:' . $pirep->id . ' FILED, Change STATE of ' . $aircraft->registration . ' to PARKED');
        }

        if (DB_Setting('dbasic.networkcheck', false)) {
            // Pirep is Filed, calculate the percentage and write the result
            $results = DB_WhazzUpCheck::select('is_online')->where('pirep_id', $pirep->id)->get();
            $check_count = $results->count();
            if ($check_count > 0) {
                $check_online = $results->where('is_online', 1)->count();
                $check_result = round((100 * $check_online) / $check_count);
            } else {
                $check_online = 0;
                $check_result = 0;
            }

            Log::debug('Disposable Basic | Pirep:' . $pirep->id . ' FILED, C:' . $check_count . ' P:' . $check_online . ' Calculated Presence %:' . $check_result);
            PirepFieldValue::create([
                'pirep_id' => $pirep->id,
                'name'     => 'Network Presence',
                'slug'     => 'network-presence',
                'value'    => $check_result,
                'source'   => 0,
            ]);
        }
    }
}
