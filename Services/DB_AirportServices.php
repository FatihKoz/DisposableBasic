<?php

namespace Modules\DisposableBasic\Services;

use App\Models\Aircraft;
use App\Models\Pirep;
use App\Models\User;
use App\Models\Enums\AircraftState;
use App\Models\Enums\AircraftStatus;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use App\Models\Enums\UserState;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DB_AirportServices
{
    // Pireps
    public function GetPireps($location, $count = null)
    {
        if (!$location) {
            return null;
        }

        $user_states = [UserState::ACTIVE, UserState::ON_LEAVE];
        $users_array = DB::table('users')->whereIn('state', $user_states)->pluck('id')->toArray();
        $airlines_array = DB::table('airlines')->where('active', 1)->pluck('id')->toArray();

        $eager_load = ['aircraft', 'airline', 'arr_airport', 'dpt_airport', 'user'];
        $where = ['state' => PirepState::ACCEPTED, 'status' => PirepStatus::ARRIVED];

        $pireps = Pirep::with($eager_load)
            ->where(function ($query) use ($location) {
                $query->where('dpt_airport_id', $location)->orWhere('arr_airport_id', $location);
            })
            ->where($where)
            ->whereIn('airline_id', $airlines_array)
            ->whereIn('user_id', $users_array)
            ->orderBy('submitted_at', 'desc')
            ->when(is_numeric($count), function ($query) use ($count) {
                return $query->take($count);
            })->get();

        return $pireps;
    }

    // Pilots
    public function GetPilots($location, $count = null)
    {
        if (!$location) {
            return null;
        }

        $states_array = [UserState::ACTIVE, UserState::ON_LEAVE];
        $airlines_array = DB::table('airlines')->where('active', 1)->pluck('id')->toArray();

        $eager_load = ['airline', 'rank', 'home_airport'];
        $where = ['curr_airport_id' => $location];

        $pilots = User::with($eager_load)
            ->where($where)
            ->whereIn('airline_id', $airlines_array)
            ->whereIn('state', $states_array)
            ->orderBy('updated_at', 'desc')
            ->when(is_numeric($count), function ($query) use ($count) {
                return $query->take($count);
            })->get();

        return $pilots;
    }

    // Aircraft parked at a location
    public function GetAircraft($location, $count = null)
    {
        if (!$location) {
            return null;
        }

        $statuses_array = [AircraftStatus::ACTIVE, AircraftStatus::MAINTENANCE];

        $withCount = ['simbriefs' => function ($query) { $query->whereNull('pirep_id'); }];
        $eager_load = ['airline', 'subfleet'];
        $where = ['airport_id' => $location, 'state' => AircraftState::PARKED];

        $aircraft = Aircraft::withCount($withCount)->with($eager_load)
            ->where($where)
            ->whereIn('status', $statuses_array)
            ->orderBy('landing_time', 'desc')
            ->orderBy('registration')
            ->when(is_numeric($count), function ($query) use ($count) {
                return $query->take($count);
            })->get();

        return $aircraft;
    }

    // Sunrise Sunset Time Details
    public function SunriseSunset($airport, $type = 'nautical')
    {
        $result = [];

        if (!$airport) {
            return $result;
        }

        $result['location'] = filled($airport->location) ? $airport->name . ' / ' . $airport->location : $airport->name;

        $current_time = time();
        $details = date_sun_info($current_time, $airport->lat, $airport->lon);

        if ($details) {
            foreach ($details as $key => $value) {
                if ($key === $type . '_twilight_begin' && $value > 1) {
                    $result['twilight_begin'] = Carbon::parse($value)->format('H:i') . ' UTC';
                }
                if ($key === $type . '_twilight_end' && $value > 1) {
                    $result['twilight_end'] = Carbon::parse($value)->format('H:i') . ' UTC';
                }
                if ($key === 'sunrise' && $value > 1) {
                    $result['sunrise'] = Carbon::parse($value)->format('H:i') . ' UTC';
                }
                if ($key === 'sunset' && $value > 1) {
                    $result['sunset'] = Carbon::parse($value)->format('H:i') . ' UTC';
                }
            }
        }

        if (array_key_exists('civil_twilight_begin', $details) && array_key_exists('civil_twilight_end', $details) && $current_time > $details['civil_twilight_begin'] && $current_time < $details['civil_twilight_end']) {
            $result['daylight'] = true;
        }

        return $result;
    }
}
