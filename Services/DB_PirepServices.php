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
        $network_refresh = 90;

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

        // Create main Model data
        $model_update = true;
        $model_data = [];
        $model_data['user_id'] = $pirep->user_id;
        $model_data['pirep_id'] = $pirep->id;
        $model_data['network'] = $network_selection;
        $model_data['callsign'] = null;
        $model_data['is_online'] = 0;

        // Check both networks and try to find user
        if ($network_selection === 'NONE') {
            // Do Nothing
            // Log::debug('Disposable Basic | Checking NOTHING (Presence Check)');
            $model_update = false;
        } elseif ($network_selection === 'IVAO') {
            // Check IVAO
            // Log::debug('Disposable Basic | Checking only ' . $network_selection . ' (Presence Check)');
            $network_check = $this->CheckNetwork($network_selection, $network_server_ivao, $network_refresh, $network_field_ivao, $user_ivao_id);
            $model_data['callsign'] = $network_check['callsign'];
            $model_data['is_online'] = $network_check['is_online'];
        } elseif ($network_selection === 'VATSIM') {
            // Check VATSIM
            // Log::debug('Disposable Basic | Checking only ' . $network_selection . ' (Presence Check)');
            $network_check = $this->CheckNetwork($network_selection, $network_server_vatsim, $network_refresh, $network_field_vatsim, $user_vatsim_id);
            $model_data['callsign'] = $network_check['callsign'];
            $model_data['is_online'] = $network_check['is_online'];
        } elseif ($network_selection === 'AUTO') {
            // Check BOTH
            Log::debug('Disposable Basic | Checking BOTH networks (Presence Check)');
            // Check IVAO
            $ivao_check = $this->CheckNetwork('IVAO', $network_server_ivao, $network_refresh, $network_field_ivao, $user_ivao_id);
            if ($ivao_check['is_online'] === 1) {
                Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is identified on IVAO (Presence Check)');
                $network_selection = $ivao_check['network'];
                $model_data['network'] = $ivao_check['network'];
                $model_data['callsign'] = $ivao_check['callsign'];
                $model_data['is_online'] = $ivao_check['is_online'];
            }
            // Check VATSIM
            $vatsim_check = $this->CheckNetwork('VATSIM', $network_server_vatsim, $network_refresh, $network_field_vatsim, $user_vatsim_id);
            if ($vatsim_check['is_online'] === 1) {
                Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is identified on VATSIM (Presence Check)');
                $network_selection = $vatsim_check['network'];
                $model_data['network'] = $vatsim_check['network'];
                $model_data['callsign'] = $vatsim_check['callsign'];
                $model_data['is_online'] = $vatsim_check['is_online'];
            }
            // User Flying Offline
            if ($ivao_check['is_online'] === 0 && $vatsim_check['is_online'] === 0) {
                Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is flying OFFLINE (Presence Check)');
                $model_data['network'] = 'OFFLINE';
            }
        }

        // Record network to Pirep Field Values
        $this->RecordNetwork($pirep->id, $network_selection);

        // Record network presence check data
        if ($model_update === true) {
            DB_WhazzUpCheck::create($model_data);
        }
    }

    // Check Network Data for user presence
    // Return the result as an array (network name, callsign being used and check result)
    public function CheckNetwork($network_name, $network_server, $network_refresh, $network_field, $user_networkid)
    {
        //Get WhazzUp Data from DB
        $whazzup = DB_WhazzUp::where('network', $network_name)->orderby('updated_at', 'desc')->first();

        // Update If Necessary
        if (!$whazzup || $whazzup->updated_at->diffInSeconds() > $network_refresh) {
            Log::debug('Disposable Basic | Downloading ' . $network_name . ' WhazzUp data (Presence Check)');
            $OnlineSvc = app(DB_OnlineServices::class);
            $whazzup = $OnlineSvc->DownloadWhazzUp($network_name, $network_server);
        }

        // Search Pilot in Network Feed
        Log::debug('Disposable Basic | Searching ' . $user_networkid . ' in ' . $network_name . ' WhazzUp data (Presence Check)');
        $online_pilots = collect(json_decode($whazzup->pilots));
        $online_pilots = $online_pilots->where($network_field, $user_networkid);

        $result = [];
        $result['network'] = $network_name;
        $result['callsign'] = null;
        $result['is_online'] = 0;

        if ($online_pilots && count($online_pilots) > 0) {
            $result['callsign'] = $online_pilots->first()->callsign;
            $result['is_online'] = 1;
        }

        // Return array
        return $result;
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
