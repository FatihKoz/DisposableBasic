<?php

namespace Modules\DisposableBasic\Services;

use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Enums\AircraftState;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use App\Services\AirportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Models\DB_WhazzUpCheck;

class DB_CronServices
{
    // Release Stuck Aircraft ("in use" or "in air" without an active pirep)
    public function ReleaseStuckAircraft()
    {
        $live_aircraft = Pirep::where('state', PirepState::IN_PROGRESS)->orWhere('state', PirepState::PAUSED)->pluck('aircraft_id')->toArray();
        $blocked_aircraft = Aircraft::where('state', '!=', AircraftState::PARKED)->whereNotIn('id', $live_aircraft)->get();

        foreach ($blocked_aircraft as $aircraft) {
            $aircraft->state = AircraftState::PARKED;
            $aircraft->save();
            Log::info('Disposable Basic | '.$aircraft->registration.' state changed to PARKED (On Ground)');
        }
    }

    // Cleanup Network Presence Data
    public function CleanUpWhazzUpChecks()
    {
        if (DB_Setting('dbasic.networkcheck_cleanup', '48') > 0) {
            $cleanup_margin = DB_Setting('dbasic.networkcheck_cleanup', '48');
            $cleanup_time = Carbon::now()->subHours($cleanup_margin);

            DB_WhazzUpCheck::where('created_at', '<', $cleanup_time)->delete();
            Log::info('Disposable Basic | Network Presence check data cleanup completed');
        }
    }

    // Update Airports via vaCentral Lookup
    public function UpdateAirports()
    {
        Log::info('Disposable Basic | Airport data lookup and update process started...');
        $airportSVC = app(AirportService::class);
        $airports = Airport::whereNull('elevation')->orderBy('icao')->take(100)->get();

        foreach ($airports as $ap) {
            $api = $airportSVC->lookupAirport($ap->id);

            if (filled($api)) {
                $ap->icao = $api['icao'];
                $ap->iata = $api['iata'];
                $ap->name = $api['name'];
                $ap->location = $api['location'];
                $ap->region = $api['region'];
                $ap->country = $api['country'];
                $ap->timezone = $api['timezone'];
                $ap->lat = $api['lat'];
                $ap->lon = $api['lon'];
                $ap->elevation = $api['elevation'];

                $ap->save();
                Log::info('Disposable Basic | '.$ap->id.' updated via vaCentral Lookup');
            } else {
                $ap->elevation = 1;
                $ap->notes = 'Special or Custom Airport';

                $ap->save();
                Log::info('Disposable Basic | '.$ap->id.' not found via vaCentral Lookup, record updated with a note and 1 feet elevation');
            }
        }

        if ($airports->count() > 0) {
            Log::info('Disposable Basic | Airport data lookup and update process completed for '.$airports->count().' records');
        } else {
            Log::info('Disposable Basic | Airport data update was not necessary, nothing done');
        }
    }
}
