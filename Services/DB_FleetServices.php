<?php

namespace Modules\DisposableBasic\Services;

use App\Events\PirepCancelled;
use App\Models\Aircraft;
use App\Models\Pirep;
use App\Models\Enums\AircraftState;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class DB_FleetServices
{
    // Fix Aircraft State
    public function FixAircraftState($reg)
    {
        $result = 0;
        $aircraft = Aircraft::where('registration', $reg)->where('state', '!=', AircraftState::PARKED)->first();

        if ($aircraft) {
            $pirep = Pirep::where(['aircraft_id' => $aircraft->id, 'state' => PirepState::IN_PROGRESS])->orderby('updated_at', 'desc')->first();

            if ($pirep) {
                $pirep->state = PirepState::CANCELLED;
                $pirep->status = PirepStatus::CANCELLED;
                $pirep->notes = 'Cancelled By Admin';
                $pirep->save();
                $result = 1;
                event(new PirepCancelled($pirep));
                Log::info('Disposable Basic, Pirep ID:' . $pirep->id . ' CANCELLED to fix aircraft state');
            }
            $aircraft->state = AircraftState::PARKED;
            $aircraft->save();
            $result = $result + 1;
            Log::info('Disposable Basic, Aircraft REG:' . $aircraft->registration . ' PARKED by Admin');
        }

        if ($result === 0) {
            Flash::error('Nothing Done... Aircraft Not Found or was already PARKED');
        } elseif ($result === 1) {
            Flash::success('Aircraft State Changed Back to PARKED');
        } elseif ($result === 2) {
            Flash::success('Aircraft State Changed Back to PARKED and Pirep CANCELLED');
        }
    }
}
