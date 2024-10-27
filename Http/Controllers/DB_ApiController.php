<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use App\Models\Enums\UserState;
use App\Models\Flight;
use App\Models\News;
use App\Models\Pirep;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\DisposableBasic\Services\DB_StatServices;
use Nwidart\Modules\Facades\Module;

class DB_ApiController extends Controller
{
    // News
    public function news(Request $request)
    {
        if (!$this->AuthCheck($request->header('x-service-key'))) {
            return response(['error' => ['code' => '401', 'http_code' => 'Unauthorized', 'message' => 'Check Service Key!']], 401);
        };

        $count = (is_numeric($request->header('x-news-count'))) ? $request->header('x-news-count') : 3;
        $allnews = News::with('user')->orderby('created_at', 'DESC')->take($count)->get();

        $news = [];

        foreach ($allnews as $n) {
            $news[] = [
                'id'            => $n->id,
                'subject'       => $n->subject,
                'message'       => $n->body,
                'author_name'   => optional($n->user)->name_private,
                'author_ident'  => optional($n->user)->ident,
                'author_avatar' => optional(optional($n->user)->avatar)->url,
                'created_at'    => $n->created_at->format('d.M.Y H:i'),
                'updated_at'    => $n->updated_at->format('d.M.Y H:i'),
            ];
        }

        return response()->json($news);
    }

    // Roster
    public function roster(Request $request)
    {
        if (!$this->AuthCheck($request->header('x-service-key'))) {
            return response(['error' => ['code' => '401', 'http_code' => 'Unauthorized', 'message' => 'Check Service Key!']], 401);
        };

        $where = [];

        if (setting('pilots.hide_inactive')) {
            $where['state'] = UserState::ACTIVE;
        }

        if ($request->header('x-roster-type') === 'full' && setting('pilots.hide_inactive')) {
            unset($where['state']);
            $where[] = ['state', '!=', UserState::REJECTED];
            $where[] = ['state', '!=', UserState::DELETED];
        }

        $roster = array();
        $eager_load = ['airline', 'current_airport', 'fields', 'home_airport', 'last_pirep', 'rank'];
        $users = User::withCount('awards')->with($eager_load)->where($where)->orderby('pilot_id')->get();

        foreach ($users as $user) {
            $roster[] = [
                'id'            => $user->id,
                'pilot_id'      => $user->pilot_id,
                'callsign'      => $user->callsign,
                'ident'         => $user->ident,
                'atc'           => $user->atc,
                'name'          => $user->name_private,
                'rank'          => optional($user->rank)->name,
                'rank_image'    => optional($user->rank)->image_url,
                'base_id'       => $user->home_airport_id,
                'base_name'     => optional($user->home_airport)->name,
                'base_iata'     => optional($user->home_airport)->iata,
                'base_icao'     => optional($user->home_airport)->icao,
                'location_id'   => $user->curr_airport_id,
                'location_name' => optional($user->current_airport)->name,
                'location_iata' => optional($user->current_airport)->iata,
                'location_icao' => optional($user->current_airport)->icao,
                'airline_name'  => optional($user->airline)->name,
                'airline_iata'  => optional($user->airline)->iata,
                'airline_icao'  => optional($user->airline)->icao,
                'airline_logo'  => optional($user->airline)->logo,
                'mins_flight'   => $user->flight_time,
                'mins_transfer' => $user->transfer_time,
                'mins_total'    => round($user->flight_time + $user->transfer_time),
                'time_flight'   => DB_ConvertMinutes($user->flight_time),
                'time_transfer' => DB_ConvertMinutes($user->transfer_time),
                'time_total'    => DB_ConvertMinutes(($user->flight_time + $user->transfer_time)),
                'awards'        => $user->awards_count,
                'ivao_id'       => optional($user->fields->firstWhere('name', DB_Setting('dbasic.networkcheck_fieldname_ivao', 'IVAO ID')))->value,
                'vatsim_id'     => optional($user->fields->firstWhere('name', DB_Setting('dbasic.networkcheck_fieldname_vatsim', 'VATSIM ID')))->value,
                'state'         => UserState::label($user->state),
                'state_id'      => $user->state,
                'last_report'   => optional(optional($user->last_pirep)->submitted_at)->diffForHumans(),
            ];
        }

        return response()->json($roster);
    }

    // Pireps
    public function pireps(Request $request)
    {
        if (!$this->AuthCheck($request->header('x-service-key'))) {
            return response(['error' => ['code' => '401', 'http_code' => 'Unauthorized', 'message' => 'Check Service Key!']], 401);
        };

        if ($request->header('x-pirep-type') != 'live') {
            $count = (is_numeric($request->header('x-pirep-count'))) ? $request->header('x-pirep-count') : 25;
            $where = ['state' => PirepState::ACCEPTED];
        } else {
            $where = [
                [
                    'state', '==', PirepState::IN_PROGRESS
                ], [
                    'status', '!=', PirepStatus::PAUSED
                ]
            ];
            $count = null;
        }

        $pireps = [];
        $eager_load = ['airline', 'aircraft', 'arr_airport', 'dpt_airport', 'field_values', 'user.fields'];
        $vms_pireps = Pirep::with($eager_load)->where($where)->when(isset($count), function ($query) use ($count) {
            return $query->take($count)->orderby('submitted_at', 'desc');
        })->when(!isset($count), function ($query) {
            return $query->orderby('updated_at', 'desc');
        })->get();

        foreach ($vms_pireps as $pirep) {
            $pireps[] = [
                'flight_number' => optional($pirep->airline)->code . ' ' . $pirep->flight_number,
                'flight_rcode'  => $pirep->route_code,
                'flight_rleg'   => $pirep->route_leg,
                'dep_id'        => $pirep->dpt_airport_id,
                'dep_iata'      => optional($pirep->dpt_airport)->iata,
                'dep_icao'      => optional($pirep->dpt_airport)->icao,
                'dep_name'      => optional($pirep->dpt_airport)->name,
                'dep_full'      => optional($pirep->dpt_airport)->full_name,
                'dep_position'  => optional($pirep->field_values->firstWhere('slug', 'departure-gate'))->value,
                'dep_runway'    => optional($pirep->field_values->firstWhere('slug', 'departure-runway'))->value,
                'arr_id'        => $pirep->arr_airport_id,
                'arr_iata'      => optional($pirep->arr_airport)->iata,
                'arr_icao'      => optional($pirep->arr_airport)->icao,
                'arr_name'      => optional($pirep->arr_airport)->name,
                'arr_full'      => optional($pirep->arr_airport)->full_name,
                'arr_position'  => optional($pirep->field_values->firstWhere('slug', 'arrival-gate'))->value,
                'arr_runway'    => optional($pirep->field_values->firstWhere('slug', 'arrival-runway'))->value,
                'aircraft'      => optional($pirep->aircraft)->ident,
                'aircraft_reg'  => optional($pirep->aircraft)->registration,
                'aircraft_iata' => optional($pirep->aircraft)->iata,
                'aircraft_icao' => optional($pirep->aircraft)->icao,
                'pilot_id'      => optional($pirep->user)->id,
                'pilot_name'    => optional($pirep->user)->name_private,
                'pilot_ident'   => optional($pirep->user)->ident,
                'pilot_atc'     => optional($pirep->user)->atc,
                'pilot_ivao'    => optional($pirep->user->fields->firstWhere('name', DB_Setting('dbasic.networkcheck_fieldname_ivao', 'IVAO ID')))->value,
                'pilot_vatsim'  => optional($pirep->user->fields->firstWhere('name', DB_Setting('dbasic.networkcheck_fieldname_vatsim', 'VATSIM ID')))->value,
                'pirep_score'   => $pirep->score,
                'pirep_lrate'   => $pirep->landing_rate,
                'pirep_time'    => $pirep->flight_time,
                'pirep_timep'   => $pirep->planned_flight_time,
                'pirep_timeu'   => "min",
                'formatted_ft'  => DB_ConvertMinutes($pirep->flight_time),
                'formatted_pft' => DB_ConvertMinutes($pirep->planned_flight_time),
                'pirep_dist'    => $pirep->distance->local(0),
                'pirep_distp'   => $pirep->planned_distance->local(0),
                'pirep_distr'   => ($pirep->distance->local(0) > 0 && $pirep->planned_distance->local(0) > 0) ? round((100 * $pirep->distance->local(0)) / $pirep->planned_distance->local(0), 0) : null,
                'pirep_distu'   => setting('units.distance'),
                'pirep_fuel'    => $pirep->fuel_used->local(0),
                'pirep_fuelu'   => setting('units.fuel'),
                'pirep_status'  => PirepStatus::label($pirep->status),
                'pirep_state'   => PirepState::label($pirep->state),
                'network_name'  => optional($pirep->field_values->firstWhere('slug', 'network-online'))->value,
                'network_ratio' => optional($pirep->field_values->firstWhere('slug', 'network-presence-check'))->value,
                'network_csign' => optional($pirep->field_values->firstWhere('slug', 'network-callsign-check'))->value,
                'submitted_at'  => (filled($pirep->submitted_at)) ? $pirep->submitted_at->format('d.m.Y H:i') : null,
                'created_at'    => (filled($pirep->created_at)) ? $pirep->created_at->format('d.m.Y H:i') : null,
                'updated_at'    => (filled($pirep->updated_at)) ? $pirep->updated_at->format('d.m.Y H:i') : null,
            ];
        }

        return response()->json($pireps);
    }

    // Events
    public function events(Request $request)
    {
        if (!$this->AuthCheck($request->header('x-service-key'))) {
            return response(['error' => ['code' => '401', 'http_code' => 'Unauthorized', 'message' => 'Check Service Key!']], 401);
        };

        $today = Carbon::today();
        $event_code = DS_Setting('dbasic.event_routecode', 'EVENT');

        $where = [
            ['start_date', '>=', $today],
            'route_code' => $event_code,
        ];

        $with = ['dpt_airport', 'arr_airport', 'airline'];
        $vms_events = Flight::with($with)->where($where)->orderBy('flight_number')->orderBy('route_leg')->orderBy('dpt_time')->get();
        $current = collect();
        $upcoming = collect();

        foreach ($vms_events as $event) {
            // Prepare the event flight array
            $flight = [
                'flight_number' => optional($event->airline)->code . ' ' . $event->flight_number,
                'flight_rcode'  => $event->route_code,
                'flight_rleg'   => $event->route_leg,
                'departure'     => $event->dpt_airport_id,
                'dep_iata'      => optional($event->dpt_airport)->iata,
                'dep_icao'      => optional($event->dpt_airport)->icao,
                'dep_name'      => optional($event->dpt_airport)->name,
                'dep_full'      => optional($event->dpt_airport)->full_name,
                'arrival'       => $event->arr_airport_id,
                'arr_iata'      => optional($event->arr_airport)->iata,
                'arr_icao'      => optional($event->arr_airport)->icao,
                'arr_name'      => optional($event->arr_airport)->name,
                'arr_full'      => optional($event->arr_airport)->full_name,
                'date'          => filled($event->start_date) ? $event->start_date->format('d.M.Y') : null,
                'time'          => filled($event->dpt_time) ? $event->dpt_time : null,
                'time_diff'     => (filled($event->start_date) && filled($event->dpt_time)) ? Carbon::CreateFromFormat('Y.m.d H:i', ($event->start_date->format('Y.m.d') . ' ' . $event->dpt_time), 'UTC')->diffForHumans() : null,
            ];

            // Append either current or upcoming collections
            if ($event->start_date == $today) {
                $current->push($flight);
            } else {
                $upcoming->push($flight);
            }
        }

        return response()->json([
            'current'  => $current,
            'upcoming' => $upcoming,
        ]);
    }

    // Stats
    public function stats(Request $request)
    {
        if (!$this->AuthCheck($request->header('x-service-key'))) {
            return response(['error' => ['code' => '401', 'http_code' => 'Unauthorized', 'message' => 'Check Service Key!']], 401);
        };

        $StatSvc = app(DB_StatServices::class);

        $basic = $StatSvc->ApiBasicStats();
        $pirep = $StatSvc->ApiPirepStats();

        $stats_network = $StatSvc->NetworkStats();

        $network = [];

        if ($stats_network) {
            $network['network_ivao_ttl'] = isset($stats_network['IVAO (All Time)']) ? $stats_network['IVAO (All Time)'] : 0;
            $network['network_ivao_l90'] = isset($stats_network['IVAO (Last 90 Days)']) ? $stats_network['IVAO (Last 90 Days)'] : 0;
            $network['network_ivao_l180'] = isset($stats_network['IVAO (Last 180 Days)']) ? $stats_network['IVAO (Last 180 Days)'] : 0;
            $network['network_vatsim_ttl'] = isset($stats_network['VATSIM (All Time)']) ? $stats_network['VATSIM (All Time)'] : 0;
            $network['network_vatsim_l90'] = isset($stats_network['VATSIM (Last 90 Days)']) ? $stats_network['VATSIM (Last 90 Days)'] : 0;
            $network['network_vatsim_l180'] = isset($stats_network['VATSIM (Last 180 Days)']) ? $stats_network['VATSIM (Last 180 Days)'] : 0;
        }

        return response()->json([
            'basic'   => $basic,
            'pireps'  => $pirep,
            'network' => $network,
        ]);
    }

    // Module Check
    public function modules(Request $request)
    {
        $DBM = Module::find('DisposableBasic');
        $DBE = isset($DBM) ? $DBM->isEnabled() : false;

        $DSM = Module::find('DisposableSpecial');
        $DSE = isset($DSM) ? $DSM->isEnabled() : false;

        return response()->json([
            'App Name'           => config('app.name'),
            'App URL'            => config('app.url'),
            'Disposable Basic'   => 'Installed: ' . isset($DBM) . ' | Enabled: ' . $DBE,
            'Disposable Special' => 'Installed: ' . isset($DSM) . ' | Enabled: ' . $DSE,
        ]);
    }

    // Simple Auth Check
    public function AuthCheck($service_key = null)
    {
        return ($service_key === null || $service_key !== DB_Setting('dbasic.srvkey')) ? false : true;
    }
}
