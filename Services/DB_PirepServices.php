<?php

namespace Modules\DisposableBasic\Services;

use App\Models\PirepFieldValue;
use App\Models\UserField;
use App\Models\UserFieldValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Models\DB_WhazzUp;
use Modules\DisposableBasic\Models\DB_WhazzUpCheck;
use Modules\DisposableBasic\Services\DB_OnlineServices;

class DB_PirepServices
{
    public function CheckWhazzUp($pirep = null)
    {
        if (!$pirep) {
            return;
        }

        // Definitions
        $network_selection = DB_Setting('dbasic.networkcheck_server', 'AUTO');

        $user_field_name_ivao = DB_Setting('dbasic.networkcheck_fieldname_ivao', 'IVAO ID');
        $user_field_name_vatsim = DB_Setting('dbasic.networkcheck_fieldname_vatsim', 'VATSIM ID');

        // Generic Network Settings
        $network_field_vatsim = 'cid';
        $network_server_vatsim = 'https://data.vatsim.net/v3/vatsim-data.json';
        $network_field_ivao = 'userId';
        $network_server_ivao = 'https://api.ivao.aero/v2/tracker/whazzup';
        $network_refresh = 150;

        // Get Custom User Field ID's
        $user_field_id_ivao = optional(UserField::select('id')->where('name', $user_field_name_ivao)->first())->id;
        $user_field_id_vatsim = optional(UserField::select('id')->where('name', $user_field_name_vatsim)->first())->id;

        // Get User Network ID's
        $user_ivao_id = optional(UserFieldValue::select('value')->where(['user_field_id' => $user_field_id_ivao, 'user_id' => $pirep->user_id])->first())->value;
        $user_vatsim_id = optional(UserFieldValue::select('value')->where(['user_field_id' => $user_field_id_vatsim, 'user_id' => $pirep->user_id])->first())->value;

        // Initial Check For Network Identification
        $identified_network = DB::table('pirep_field_values')->where(['pirep_id' => $pirep->id, 'slug' => 'network-online'])->value('value');

        // User is already identified on IVAO or VATSIM
        if ($network_selection === 'AUTO' && ($identified_network === 'IVAO' || $identified_network === 'VATSIM')) {
            Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' already identified on ' . $identified_network . ' (Presence Check)');
            $network_selection = $identified_network;
        }

        // Check user's network preference for AUTO
        if ($network_selection === 'AUTO' && empty($user_ivao_id) && empty($user_vatsim_id)) {
            Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' not provided ANY NETWORK memberships (Presence Check)');
            $network_selection = 'NONE';
        } elseif ($network_selection === 'AUTO' && isset($user_ivao_id) && isset($user_vatsim_id)) {
            Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is member of BOTH networks (Presence Check)');
            $network_selection = 'AUTO';
        } elseif ($network_selection === 'AUTO' && isset($user_ivao_id) && empty($user_vatsim_id)) {
            Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is ONLY an IVAO member (Presence Check)');
            $network_selection = 'IVAO';
        } elseif ($network_selection === 'AUTO' && empty($user_ivao_id) && isset($user_vatsim_id)) {
            Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is ONLY a VATSIM member (Presence Check)');
            $network_selection = 'VATSIM';
        }

        // Check both networks and try to find user
        if ($network_selection === 'AUTO') {
            // Check IVAO
            $whazzup_ivao = $this->GetWhazzUpData('IVAO', $network_server_ivao, $network_refresh);
            $check_ivao =  $this->CheckPilotPresence($whazzup_ivao, $network_field_ivao, $user_ivao_id, 'IVAO');
            if ($check_ivao && count($check_ivao) > 0) {
                Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is identified on IVAO (Presence Check)');
                $network_selection = 'IVAO';
            }
            // Check VATSIM
            $whazzup_vatsim = $this->GetWhazzUpData('VATSIM', $network_server_vatsim, $network_refresh);
            $check_vatsim =  $this->CheckPilotPresence($whazzup_vatsim, $network_field_vatsim, $user_vatsim_id, 'VATSIM');
            if ($check_vatsim && count($check_vatsim) > 0) {
                Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is identified on VATSIM (Presence Check)');
                $network_selection = 'VATSIM';
            }
        } elseif ($network_selection === 'NONE') {
            Log::debug('Disposable Basic | Checking nothing (Presence Check)');
        } else {
            // VA only allows a certain network
            Log::debug('Disposable Basic | Checking only ' . $network_selection . ' (Presence Check)');
        }

        // Record network to Pirep Field Values
        $this->RecordNetwork($pirep->id, $network_selection);

        if ($network_selection === 'VATSIM') {
            $network_field = $network_field_vatsim;
            $network_server = $network_server_vatsim;
            $user_networkid = $user_vatsim_id;
            $network_download = true;
        } elseif ($network_selection === 'IVAO') {
            $network_field = $network_field_ivao;
            $network_server = $network_server_ivao;
            $user_networkid = $user_ivao_id;
            $network_download = true;
        } else {
            $network_download = false;
        }

        // Create main Model data
        $model_data = [];
        $model_data['user_id'] = $pirep->user_id;
        $model_data['pirep_id'] = $pirep->id;
        $model_data['network'] = $network_selection;
        $model_data['callsign'] = null;
        $model_data['is_online'] = 0;

        // Proceed on checks for online networks
        if ($network_download === true) {
            // Get WhazzUp data
            $whazzup = $this->GetWhazzUpData($network_selection, $network_server, $network_refresh);
            // Check the user and update model data array if necesary
            if ($whazzup && $user_networkid) {

                $online_pilots = $this->CheckPilotPresence($whazzup, $network_field, $user_networkid, $network_selection);

                if ($online_pilots && count($online_pilots) > 0) {
                    $model_data['callsign'] = $online_pilots->first()->callsign;
                    $model_data['is_online'] = 1;
                }
            }
        }

        // Save check result
        DB_WhazzUpCheck::create($model_data);
    }

    // Get WhazzUp Data and Update if necessary
    public function GetWhazzUpData($network_name, $network_server, $network_refresh)
    {
        $whazzup = DB_WhazzUp::where('network', $network_name)->orderby('updated_at', 'desc')->first();

        if (!$whazzup || $whazzup->updated_at->diffInSeconds() > $network_refresh) {
            Log::debug('Disposable Basic | Downloading ' . $network_name . ' WhazzUp data (Presence Check)');
            $OnlineSvc = app(DB_OnlineServices::class);
            $whazzup = $OnlineSvc->DownloadWhazzUp($network_name, $network_server);
        }

        return $whazzup;
    }

    // Search Pilot In Downloaded Network Data
    public function CheckPilotPresence($whazzup, $network_field, $user_networkid, $network_name)
    {
        Log::debug('Disposable Basic | Searching ' . $user_networkid . ' in ' . $network_name . ' WhazzUp data (Presence Check)');
        $online_pilots = collect(json_decode($whazzup->pilots));

        return $online_pilots->where($network_field, $user_networkid);
    }

    // Record Selected Network to Pirep Field Values
    public function RecordNetwork($pirep_id, $network_name)
    {
        PirepFieldValue::updateOrCreate(
            ['pirep_id' => $pirep_id, 'name' => 'Network Online', 'slug' => 'network-online'],
            ['value' => $network_name, 'source' => 1]
        );
    }
}
