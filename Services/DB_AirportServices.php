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

class DB_AirportServices
{
    // Pireps
    public function GetPireps($location, $count = null)
    {
        if (!$location) {
            return null;
        }

        $user_states = array(UserState::ACTIVE, UserState::ON_LEAVE);
        $users_array = DB::table('users')->whereIn('state', $user_states)->pluck('id')->toArray();
        $airlines_array = DB::table('airlines')->where('active', 1)->pluck('id')->toArray();

        $eager_load = array('aircraft', 'airline', 'arr_airport', 'dpt_airport', 'user');
        $where = array('state' => PirepState::ACCEPTED, 'status' => PirepStatus::ARRIVED);

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

        $states_array = array(UserState::ACTIVE, UserState::ON_LEAVE);
        $airlines_array = DB::table('airlines')->where('active', 1)->pluck('id')->toArray();

        $eager_load = array('airline', 'rank', 'home_airport');
        $where = array('curr_airport_id' => $location);

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

    // Aircraft
    public function GetAircraft($location, $count = null)
    {
        if (!$location) {
            return null;
        }

        $statuses_array = array(AircraftStatus::ACTIVE, AircraftStatus::MAINTENANCE);

        $eager_load = array('subfleet.airline');
        $where = array('airport_id' => $location, 'state' => AircraftState::PARKED);

        $aircraft = Aircraft::with($eager_load)
            ->where($where)
            ->whereIn('status', $statuses_array)
            ->orderBy('landing_time', 'desc')
            ->orderBy('registration')
            ->when(is_numeric($count), function ($query) use ($count) {
                return $query->take($count);
            })->get();

        return $aircraft;
    }
}
