<?php

namespace Modules\DisposableBasic\Services;

use App\Events\PirepCancelled;
use App\Models\Aircraft;
use App\Models\Pirep;
use App\Models\Enums\AircraftState;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Models\DB_Tech;

class DB_FleetServices
{
    // Check folders for an image, Aircraft + Subfleet
    public function AircraftImage($aircraft)
    {
        $image = null;
        $image_url = strtolower('image/aircraft/' . $aircraft->registration . '.jpg');

        if (is_file($image_url)) {
            $image['url'] = $image_url;
            $image['title'] = $aircraft->registration;

            if ($aircraft->registration != $aircraft->name) {
                $image['title'] = $aircraft->registration . ' "' . $aircraft->name . '"';
            }
        } elseif ($aircraft->subfleet) {
            $image = $this->SubfleetImage($aircraft->subfleet);
        }

        return is_array($image) ? $image : null;
    }

    // Check folders for an image, Subfleet only
    public function SubfleetImage($subfleet)
    {
        $image = null;
        $image_url = strtolower('image/subfleet/' . $subfleet->type . '.jpg');

        if (is_file($image_url)) {
            $image['url'] = $image_url;
            $image['title'] = $subfleet->name;
        }

        return is_array($image) ? $image : null;
    }

    // Fix state of a stuck aircraft (and cancel it's pirep if any)
    public function ParkAircraft($reg)
    {
        $result = 0;
        $aircraft = Aircraft::where('registration', $reg)->where('state', '!=', AircraftState::PARKED)->first();

        if ($aircraft) {
            $pirep = Pirep::where(['aircraft_id' => $aircraft->id, 'state' => PirepState::IN_PROGRESS])->orderby('updated_at', 'desc')->first();

            if ($pirep) {
                $pirep->state = PirepState::CANCELLED;
                $pirep->status = PirepStatus::CANCELLED;
                $pirep->notes = 'Cancelled by Admin';
                $pirep->save();
                $result = 1;
                event(new PirepCancelled($pirep));
                Log::info('Disposable Basic, Pirep ID:' . $pirep->id . ' CANCELLED');
            }

            $aircraft->state = AircraftState::PARKED;
            $aircraft->save();
            $result = $result + 1;
            Log::info('Disposable Basic, Aircraft REG:' . $aircraft->registration . ' PARKED');
        }

        return $result;
    }

    // Provide avg fuel burn per minute (for fuel calculations etc)
    public function AverageFuelBurn($aircraft_id)
    {
        $results = [];
        $aircraft_icao = DB::table('aircraft')->where('id', $aircraft_id)->value('icao');
        $result = DB_Tech::where('icao', $aircraft_icao)->value('avg_fuel');

        if ($result > 0) {
            $results['source'] = 'ICAO Type Avg (Manufacturer)';
            $results['avg_pounds'] = round($result / 60, 2);
            $results['avg_metric'] = round(($result / 60) / 2.20462262185, 2);

            return $results;
        }

        $aircraft_array = DB::table('aircraft')->where('icao', $aircraft_icao)->pluck('id')->toArray();

        $where = [];
        $where['state'] = PirepState::ACCEPTED;
        $where['aircraft_id'] = $aircraft_id;

        $results['source'] = 'Aircraft Avg (Pireps)';

        $count_while = 1;
        while ($count_while <= 3) {

            if ($count_while === 2) {
                // Remove the aircraft_id from where array and try getting icao based pirep average
                unset($where['aircraft_id']);
                $results['source'] = 'ICAO Type Avg (Pireps)';
            }

            $result = DB::table('pireps')
                ->selectRaw('sum(fuel_used) as total_fuel, sum(flight_time) as total_time')
                ->where($where)
                ->when($count_while === 2, function ($query) use ($aircraft_array) {
                    return $query->whereIn('aircraft_id', $aircraft_array);
                })
                ->first();

            if (filled($result) && $result->total_fuel > 0 && $result->total_time > 0) {
                break;
            }
            $count_while++;
        }

        if (filled($result) && $result->total_fuel > 0 && $result->total_time > 0) {
            $results['avg_pounds'] = round($result->total_fuel / $result->total_time, 2);
            $results['avg_metric'] = round($results['avg_pounds'] / 2.20462262185, 2);
        }

        return $results;
    }
}
