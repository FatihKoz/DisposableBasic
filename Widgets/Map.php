<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\Subfleet;
use App\Models\User;
use App\Models\Enums\AircraftState;
use App\Models\Enums\AircraftStatus;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Map extends Widget
{
    protected $config = ['source' => 0, 'visible' => true, 'limit' => null, 'airline' => null, 'location' => null, 'company' => null, 'popups' => null];

    public function run()
    {
        $mapcenter = setting('acars.center_coords');
        $detailed_popups = is_bool($this->config['popups']) ? $this->config['popups'] : null;

        if (setting('pilots.only_flights_from_current')) {
            $limit_location = true;
        } else {
            $limit_location = is_bool($this->config['location']) ? $this->config['location'] : false;
        }

        if (setting('pilots.restrict_to_company')) {
            $limit_company = true;
        } else {
            $limit_company = is_bool($this->config['company']) ? $this->config['company'] : false;
        }

        // Get The Flights/Pireps With Applied Limit
        $take_limit = is_numeric($this->config['limit']) ? $this->config['limit'] : null;

        // Get User Location with Failsafe
        $user = User::with('current_airport:id,name,lat,lon', 'home_airport:id,name,lat,lon')->find(Auth::id());
        if ($user && $user->current_airport) {
            $user_a = $user->current_airport->id;
            $user_loc = $user->current_airport->lat . ',' . $user->current_airport->lon;
        } elseif ($user && $user->home_airport) {
            $user_a = $user->home_airport->id;
            $user_loc = $user->home_airport->lat . ',' . $user->home_airport->lon;
        } else {
            $user_a = 'ZZZZ';
            $user_loc = $mapcenter;
        }

        // Define Map Type
        if ($this->config['source'] === 0) {
            $type = 'generic';
        } elseif (is_numeric($this->config['source']) && $this->config['source'] != 0) {
            $airline_id = $this->config['source'];
            $type = 'airline';
            $detailed_popups = is_bool($this->config['popups']) ? $this->config['popups'] : false;
        } elseif ($this->config['source'] === 'user') {
            $type = 'user';
        } elseif ($this->config['source'] === 'fleet') {
            $type = 'fleet';
        } elseif ($this->config['source'] === 'aerodromes') {
            $type = 'aerodromes';
        } elseif ($this->config['source'] === 'assignment') {
            $type = 'assignment';
        } else {
            $airport_id = $this->config['source'];
            $type = 'airport';
        }

        // Build User's Flown CityPairs for Flight Maps Only
        if (isset($user) && $type != 'fleet' && $type != 'assignment' && $type != 'aerodromes') {
            $user_pireps = DB::table('pireps')->select('arr_airport_id', 'dpt_airport_id')->where(['user_id' => $user->id, 'state' => 2])->get();
            $user_citypairs = collect();
            foreach ($user_pireps as $up) {
                $user_citypairs->push($up->dpt_airport_id . $up->arr_airport_id);
            }
            $user_citypairs = $user_citypairs->unique();
        }

        $where = [];
        $where['active'] = 1;

        $orwhere = [];
        $orwhere['active'] = 1;

        // Filter flights to selected airline
        if ($type === 'airline') {
            $where['airline_id'] = $airline_id;
        }

        // Filter Flights To User's Current Location
        if ($type === 'generic' && $limit_location) {
            $where['dpt_airport_id'] = $user_a;
            $mapcenter = $user_loc;
        }

        // Filter Flights to User's Company
        if ($type === 'generic' && $limit_company || $type === 'airport' && $limit_company) {
            $where['airline_id'] = $user->airline_id;
            $orwhere['airline_id'] = $user->airline_id;
        }

        // Filter Visible Flights
        if ($this->config['visible'] && $type != 'user' && $type != 'fleet') {
            $where['visible'] = 1;
            $orwhere['visible'] = 1;
        }

        $eager_load = ['airline:id,name,icao,iata', 'arr_airport:id,name,lat,lon,hub', 'dpt_airport:id,name,lat,lon,hub'];

        // User Pireps Map
        if ($type === 'user') {
            $mapflights = Pirep::with($eager_load)
                ->select('id', 'airline_id', 'flight_number', 'dpt_airport_id', 'arr_airport_id')
                ->where(['user_id' => $user->id, 'state' => 2])
                ->orderby('submitted_at', 'desc')
                ->when(is_numeric($take_limit), function ($query) use ($take_limit) {
                    return $query->take($take_limit);
                })->get();
        }

        // Generic and Airline Flights Map
        elseif ($type === 'generic' || $type === 'airline') {
            $mapflights = Flight::with($eager_load)
                ->select('id', 'dpt_airport_id', 'arr_airport_id', 'airline_id', 'flight_number')
                ->where($where)
                ->orderby('flight_number')
                ->when(is_numeric($take_limit), function ($query) use ($take_limit) {
                    return $query->take($take_limit);
                })->get();
        }

        // Monthly Assignment Flights Map
        elseif ($type === 'assignment') {
            $latest_assignments = null;
            // Get Current User's latest assignments and build the flights array
            // Needs Disposable Special Module
            if (DB_CheckModule('DisposableSpecial')) {
                $now = Carbon::now();
                $asg_where = [];
                $asg_where['assignment_year'] = $now->year;
                $asg_where['assignment_month'] = $now->month;
                $asg_where['user_id'] = $user->id;
                $latest_assignments = \Modules\DisposableSpecial\Models\DS_Assignment::where($asg_where)->pluck('flight_id')->toArray();
            }

            $mapflights = Flight::with($eager_load)
                ->select('id', 'dpt_airport_id', 'arr_airport_id', 'airline_id', 'flight_number')
                ->where($where)
                ->whereIn('id', $latest_assignments)
                ->orderby('flight_number')
                ->when(is_numeric($take_limit), function ($query) use ($take_limit) {
                    return $query->take($take_limit);
                })->get();
        }

        // Airport Flights Map
        elseif ($type === 'airport') {
            $where['dpt_airport_id'] = $airport_id;
            $mapflights = Flight::with($eager_load)
                ->select('id', 'dpt_airport_id', 'arr_airport_id', 'airline_id', 'flight_number')
                ->where($where)
                ->orWhere('arr_airport_id', $airport_id)
                ->where($orwhere)
                ->when(is_numeric($take_limit), function ($query) use ($take_limit) {
                    return $query->take($take_limit);
                })->get();
        }

        // Aerodromes - Airports Map
        elseif ($type === 'aerodromes') {
            $airports = DB::table('airports')->select('id', 'hub', 'iata', 'icao', 'lat', 'lon', 'name')->orderBy('id')->get();
        }

        // Fleet Locations Map
        elseif ($type === 'fleet') {
            $awhere = [];
            $awhere['state'] = AircraftState::PARKED;
            $awhere['status'] = AircraftStatus::ACTIVE;

            $sfwhere = [];
            if (is_numeric($this->config['airline'])) {
                $sfwhere['airline_id'] = $this->config['airline'];
            }

            $subfleets = Subfleet::where($sfwhere)->pluck('id')->toArray();

            // Get User's Allowed Aircraft
            if (setting('pireps.restrict_aircraft_to_rank', true) || setting('pireps.restrict_aircraft_to_typerating', false)) {
                $userSvc = app(UserService::class);
                $restricted_to = $userSvc->getAllowableSubfleets($user);
                $user_subfleets = $restricted_to->pluck('id')->toArray();
                $subfleets = array_intersect($subfleets, $user_subfleets);
            }

            $aircraft = DB::table('aircraft')->select('id', 'airport_id', 'subfleet_id', 'registration', 'icao')
                ->where($awhere)
                ->whereIn('subfleet_id', $subfleets)
                ->orderby('registration')
                ->get();

            // Build Unique Locations
            $aircraft_locations = $aircraft->pluck('airport_id')->toArray();
            $aircraft_locations = array_unique($aircraft_locations, SORT_STRING);
            $airports = DB::table('airports')->select('id', 'hub', 'iata', 'icao', 'lat', 'lon', 'name')->whereIn('id', $aircraft_locations)->get();
        }

        // Build Unique City Pairs From Flights/Pireps
        if ($type != 'fleet' && $type != 'aerodromes') {
            $citypairs = [];
            $airports_pack = collect();
            foreach ($mapflights as $mf) {
                $airports_pack->push($mf->dpt_airport);
                $airports_pack->push($mf->arr_airport);
                $reverse = $mf->arr_airport_id . $mf->dpt_airport_id;
                if (DB_InArray_MD($reverse, $citypairs)) {
                    continue;
                }

                $citypairs[] = array(
                    'name' => $mf->dpt_airport_id . $mf->arr_airport_id,
                    'dloc' => $mf->dpt_airport->lat . "," . $mf->dpt_airport->lon,
                    'aloc' => $mf->arr_airport->lat . "," . $mf->arr_airport->lon
                );
            }
            $citypairs = DB_ArrayUnique_MD($citypairs, 'name');
            $airports = $airports_pack->unique('id');
        }

        // Set Map Center to Selected Airport
        if ($type === 'airport') {
            foreach ($airports->where('id', $airport_id) as $center) {
                $mapcenter = $center->lat . "," . $center->lon;
            }
        }

        // Auto disable popups to increase performance and reduce php timeout errors
        if ($type != 'fleet' && $type != 'aerodromes' && is_countable($mapflights) && count($mapflights) >= 1000) {
            $detailed_popups = false;
        }

        // Map Icons
        $mapIcons = [];

        $RedUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png';
        $GreenUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png';
        $BlueUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png';
        $YellowUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png';

        $shadowUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png';
        $iconSize = [12, 20];
        $shadowSize = [20, 20];

        $mapIcons['RedIcon'] = json_encode(['iconUrl' => $RedUrl, 'shadowUrl' => $shadowUrl, 'iconSize' => $iconSize, 'shadowSize' => $shadowSize]);
        $mapIcons['GreenIcon'] = json_encode(['iconUrl' => $GreenUrl, 'shadowUrl' => $shadowUrl, 'iconSize' => $iconSize, 'shadowSize' => $shadowSize]);
        $mapIcons['BlueIcon'] = json_encode(['iconUrl' => $BlueUrl, 'shadowUrl' => $shadowUrl, 'iconSize' => $iconSize, 'shadowSize' => $shadowSize]);
        $mapIcons['YellowIcon'] = json_encode(['iconUrl' => $YellowUrl, 'shadowUrl' => $shadowUrl, 'iconSize' => $iconSize, 'shadowSize' => $shadowSize]);

        // Routes For PopUps
        $hroute = 'DBasic.hub';
        $aroute = 'DBasic.aircraft';

        // Map Hubs Array
        $mapHubs = [];

        foreach ($airports->where('hub', 1) as $hub) {
            $hpop = '<a href="' . route($hroute, [$hub->id]) . '" target="_blank">' . $hub->id . ' ' . str_replace("'", "`", $hub->name) . '</a>';
            if (isset($aircraft) && isset($aroute) && $aircraft->where('airport_id', $hub->id)->count() > 0 && $aircraft->where('airport_id', $hub->id)->count() < 6) {
                $hpop = $hpop . '<hr>';
                foreach ($aircraft->where('airport_id', $hub->id) as $ac) {
                    $hpop = $hpop . '<a href="' . route($aroute, [$ac->registration]) . '" target="_blank">' . $ac->registration . ' (' . $ac->icao . ') </a><br>';
                }
            } elseif (isset($aircraft)) {
                $hpop = $hpop . '<hr>Parked Aircraft: ' . $aircraft->where('airport_id', $hub->id)->count() . '<br>';
            }
            $mapHubs[] = [
                'id'  => $hub->id,
                'loc' => $hub->lat . ', ' . $hub->lon,
                'pop' => $hpop,
            ];
        }

        // Map Airport Array
        $mapAirports = [];

        foreach ($airports->where('hub', 0) as $airport) {
            $apop = '<a href="' . route('frontend.airports.show', [$airport->id]) . '" target="_blank">' . $airport->id . ' ' . str_replace("'", "`", $airport->name) . '</a>';
            if (isset($aircraft) && isset($aroute) && $aircraft->where('airport_id', $airport->id)->count() > 0 && $aircraft->where('airport_id', $airport->id)->count() < 6) {
                $apop = $apop . '<hr>';
                foreach ($aircraft->where('airport_id', $airport->id) as $ac) {
                    $apop = $apop . '<a href="' . route($aroute, [$ac->registration]) . '" target="_blank">' . $ac->registration . ' (' . $ac->icao . ') </a><br>';
                }
            } elseif (isset($aircraft)) {
                $apop = $apop . '<hr>Parked Aircraft: ' . $aircraft->where('airport_id', $airport->id)->count();
            }
            $mapAirports[] = [
                'id'  => $airport->id,
                'loc' => $airport->lat . ', ' . $airport->lon,
                'pop' => $apop,
            ];
        }

        // Map CityPairs Array
        $mapCityPairs = [];

        if (isset($citypairs)) {
            foreach ($citypairs as $citypair) {
                if ($detailed_popups === false) {
                    $popuptext = substr($citypair['name'], 0, 4) . ' - ' . substr($citypair['name'], 4, 4);
                } else {
                    $popuptext = '';
                    foreach ($mapflights->where('dpt_airport_id', substr($citypair['name'], 0, 4))->where('arr_airport_id', substr($citypair['name'], 4, 4)) as $mf) {
                        if ($type === 'user') {
                            $popuptext = $popuptext . '<a href="/pireps/';
                        } else {
                            $popuptext = $popuptext . '<a href="/flights/';
                        }
                        $popuptext = $popuptext . $mf->id . '" target="_blank">';
                        $popuptext = $popuptext . $mf->airline->code . $mf->flight_number . ' ' . $mf->dpt_airport_id . '-' . $mf->arr_airport_id . '</a><br>';
                    }

                    foreach ($mapflights->where('dpt_airport_id', substr($citypair['name'], 4, 4))->where('arr_airport_id', substr($citypair['name'], 0, 4)) as $mf) {
                        if ($type === 'user') {
                            $popuptext = $popuptext . '<a href="/pireps/';
                        } else {
                            $popuptext = $popuptext . '<a href="/flights/';
                        }
                        $popuptext = $popuptext . $mf->id . '" target="_blank">';
                        $popuptext = $popuptext . $mf->airline->code . $mf->flight_number . ' ' . $mf->dpt_airport_id . '-' . $mf->arr_airport_id . '</a><br>';
                    }
                }

                if (isset($user_citypairs) && $user_citypairs->contains($citypair['name'])) {
                    $cp_color = 'darkgreen';
                } elseif (isset($user_citypairs) && $user_citypairs->contains(substr($citypair['name'], 4, 4) . substr($citypair['name'], 0, 4))) {
                    $cp_color = 'lightgreen';
                } else {
                    $cp_color = 'crimson';
                }

                $mapCityPairs[] = [
                    'name' => $citypair['name'],
                    'geod' => '[' . $citypair['dloc'] . '], [' . $citypair['aloc'] . ']',
                    'geoc' => $cp_color,
                    'pop'  => $popuptext,
                ];
            }
        }

        return view('DBasic::widgets.map', [
            'mapcenter'    => $mapcenter,
            'mapsource'    => $type,
            'aircraft'     => isset($aircraft) ? count($aircraft) : null,
            'flights'      => isset($mapflights) ? count($mapflights) : null,
            'mapIcons'     => $mapIcons,
            'mapHubs'      => $mapHubs,
            'mapAirports'  => $mapAirports,
            'mapCityPairs' => $mapCityPairs,
        ]);
    }

    public function placeholder()
    {
        $loading_style = '<div class="alert alert-warning mb-1 p-0 px-2 small fw-bold"><div class="spinner-border spinner-border-sm text-dark me-2" role="status"></div>Loading Map data...</div>';
        return $loading_style;
    }
}
