<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Airline;
use App\Models\Pirep;
use App\Models\User;
use App\Models\UserField;
use App\Models\UserFieldValue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\DisposableBasic\Models\DB_WhazzUp;
use Modules\DisposableBasic\Services\DB_OnlineServices;
use Theme;

class WhazzUp extends Widget
{
    public $reloadTimeout = 60;

    protected $config = ['network' => null, 'field_name' => null, 'refresh' => 180];

    public function run()
    {
        $network_selection = ($this->config['network'] === 'IVAO' || $this->config['network'] === 'VATSIM') ? $this->config['network'] : 'IVAO';
        $refresh_interval = (is_numeric($this->config['refresh']) && $this->config['refresh'] > 15) ? $this->config['refresh'] : 180;

        if (empty($this->config['field_name'])) {
            $field_name = Theme::getSetting('gen_'.strtolower($network_selection).'_field');
        }

        if (empty($field_name)) {
            $field_name = isset($this->config['field_name']) ? $this->config['field_name'] : 'IVAO';
        }

        if ($network_selection === 'VATSIM') {
            $user_field = 'cid';
            $server_address = 'https://data.vatsim.net/v3/vatsim-data.json';
        } else {
            $user_field = 'userId';
            $server_address = 'https://api.ivao.aero/v2/tracker/whazzup';
        }

        $whazzup = DB_WhazzUp::where('network', $network_selection)->orderby('updated_at', 'desc')->first();

        if (!$whazzup || $whazzup->updated_at->diffInSeconds() > $refresh_interval) {
            $OnlineSvc = app(DB_OnlineServices::class);
            $whazzup = $OnlineSvc->DownloadWhazzUp($network_selection, $server_address);
        }

        if ($whazzup) {
            $online_pilots = collect(json_decode($whazzup->pilots));
            $online_pilots = $online_pilots->whereIn($user_field, $this->NetworkUsersArray($field_name));
            $dltime = isset($whazzup->updated_at) ? $whazzup->updated_at : null;

            $pilots = [];
            foreach ($online_pilots as $online_pilot) {
                $user = $this->FindUser($online_pilot->$user_field);
                $pirep = $this->FindActivePirep(filled($user) ? $user->id : null);
                $airline_icao = substr($online_pilot->callsign, 0, 3);
                $flightplan = ($network_selection === 'VATSIM') ? $online_pilot->flight_plan : $online_pilot->flightPlan;

                if ($flightplan && $network_selection === 'VATSIM') {
                    $fp = $flightplan->aircraft_short . ' | ' . $flightplan->departure . ' > ' . $flightplan->arrival;
                } elseif ($flightplan) {
                    $fp = $flightplan->aircraftId . ' | ' . $flightplan->departureId . ' > ' . $flightplan->arrivalId;
                } else {
                    $fp = 'No ATC Flight Plan!';
                }

                $airline = in_array($airline_icao, $this->AirlinesArray());

                // Skip online users not flying for the VA
                // Airline check false and no live pireps
                if (!$airline && !$pirep) { continue; }

                $pilots[] = [
                    'user_id'      => isset($user) ? $user->id : null,
                    'name'         => isset($user) ? $user->name : null,
                    'name_private' => isset($user) ? $user->name_private : null,
                    'network_id'   => ($network_selection === 'VATSIM') ? $online_pilot->cid : $online_pilot->userId,
                    'callsign'     => $online_pilot->callsign,
                    'server_name'  => ($network_selection === 'VATSIM') ? $online_pilot->server : $online_pilot->serverId,
                    'online_time'  => ($network_selection === 'VATSIM') ? Carbon::parse($online_pilot->logon_time)->diffInMinutes() : ceil($online_pilot->time / 60),
                    'pirep'        => $pirep,
                    'airline'      => $airline,
                    'flightplan'   => $fp,
                ];
            }
        }

        $viewer = User::withCount('roles')->find(Auth::id());
        $checks = (isset($viewer) && $viewer->roles_count > 0) ? true : false;

        $pilots = collect($pilots);

        return view('DBasic::widgets.whazzup', [
            'pilots'  => isset($pilots) ? $pilots : null,
            'error'   => isset($error) ? $error : null,
            'network' => $network_selection,
            'checks'  => $checks,
            'dltime'  => isset($dltime) ? $dltime : null,
        ]);
    }

    public function placeholder()
    {
        $loading_style = '<div class="alert alert-info mb-2 p-1 px-2 small fw-bold"><div class="spinner-border spinner-border-sm text-dark me-2" role="status"></div>Loading Network WhazzUp data...</div>';
        return $loading_style;
    }

    public function AirlinesArray()
    {
        return Airline::where('active', 1)->pluck('icao')->toArray();;
    }

    public function NetworkUsersArray($field_name = null)
    {
        $inactive_users = User::where('state', '!=', 1)->pluck('id')->toArray();
        $user_field_id = optional(UserField::select('id')->where('name', $field_name)->first())->id;
        $network_users = UserFieldValue::where('user_field_id', $user_field_id)->whereNotIn('user_id', $inactive_users)->whereNotNull('value')->pluck('value')->toArray();

        return filled($network_users) ? $network_users : null;
    }

    public function FindUser($network_id = null)
    {
        return optional(UserFieldValue::with('user')->select('user_id')->where('value', $network_id)->first())->user;
    }

    public function FindActivePirep($user_id = null)
    {
        return Pirep::with('aircraft', 'airline')->where(['user_id' => $user_id, 'state' => 0])->orderby('updated_at', 'desc')->first();
    }
}
