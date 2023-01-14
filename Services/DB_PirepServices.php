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
        $network_selection = DB_Setting('dbasic.networkcheck_server', 'IVAO');

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
        $user_ivao_id = UserFieldValue::select('value')->where(['user_field_id' => $user_field_id_ivao, 'user_id' => $pirep->user_id])->first();
        $user_vatsim_id = UserFieldValue::select('value')->where(['user_field_id' => $user_field_id_vatsim, 'user_id' => $pirep->user_id])->first();

        // Initial Check For Network Identification
        $identified_network = DB::table('pirep_field_values')->where(['pirep_id' => $pirep->id, 'slug' => 'network-online'])->value('value');

        // Stop proceeding further 'cause user has no network id's provided to VA and this is pre-identified
        if (isset($identified_network) && $identified_network === 'NONE') {
            return;
        }

        // Check user profile and try to find a matching network id and check whazzup data
        if ($network_selection === 'AUTO' && (empty($identified_network) || $identified_network === 'OFFLINE')) {
            if (isset($user_ivao_id) && empty($user_vatsim_id)) {
                Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is an IVAO member (Presence Check)');
                $network_name = 'IVAO';
            } elseif (isset($user_vatsim_id) && empty($user_ivao_id)) {
                Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is a VATSIM member (Presence Check)');
                $network_name = 'VATSIM';
            } elseif (empty($user_vatsim_id) && empty($user_ivao_id)) {
                Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' not provided any NETWORK memberships (Presence Check)');
                $network_name = 'NONE';
            } else {
                // Check both networks and try to locate the user
                Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is member of BOTH networks (Presence Check)');
                // Check IVAO
                $whazzup_ivao = $this->GetWhazzUpData('IVAO', $network_server_ivao, $network_refresh);
                $check_ivao =  $this->CheckPilotPresence($whazzup_ivao, $network_field_ivao, $user_ivao_id);
                $found_ivao = ($check_ivao && count($check_ivao) > 0) ? true : false;
                if ($found_ivao === true) {
                    $network_name = 'IVAO';
                    Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is identified on IVAO (Presence Check)');
                } else {
                    // Check VATSIM
                    $whazzup_vatsim = $this->GetWhazzUpData('VATSIM', $network_server_vatsim, $network_refresh);
                    $check_vatsim =  $this->CheckPilotPresence($whazzup_vatsim, $network_field_vatsim, $user_vatsim_id);
                    $found_vatsim = ($check_vatsim && count($check_vatsim) > 0) ? true : false;
                    if ($found_vatsim === true) {
                        $network_name = 'VATSIM';
                        Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is identified on VATSIM (Presence Check)');
                    } else {
                        // Not found on IVAO or VATSIM
                        $network_name = 'OFFLINE';
                        Log::debug('Disposable Basic | User ID:' . $pirep->user_id . ' is flying OFFLINE (Presence Check)');
                    }
                }
            }
        } elseif ($network_selection === 'AUTO' && isset($identified_network) && ($identified_network === 'IVAO' || $identified_network === 'VATSIM')) {
            // Already identified on IVAO or VATSIM
            $network_name = $identified_network;
        } else {
            // VA Only allows a certain network
            $network_name = $network_selection;
        }

        // Record network to Pirep Field Values
        $this->RecordNetwork($pirep->id, $network_name);

        if ($network_name === 'VATSIM') {
            $network_field = $network_field_vatsim;
            $network_server = $network_server_vatsim;
            $user_networkid = $user_vatsim_id;
        } elseif ($network_name === 'IVAO') {
            $network_field = $network_field_ivao;
            $network_server = $network_server_ivao;
            $user_networkid = $user_ivao_id;
        } else {
            // User OFFLINE, no need to proceed further
            return;
        }

        // Get WhazzUp Data and Update if necessary (it may be already downloaded by cron or by WhazzUp Widget)
        $whazzup = $this->GetWhazzUpData($network_name, $network_server, $network_refresh);

        // Get all pilots and reduce the collection to a specific user
        if ($whazzup && $user_networkid) {

            $model_data = [];
            $model_data['user_id'] = $pirep->user_id;
            $model_data['pirep_id'] = $pirep->id;
            $model_data['network'] = $network_name;

            $online_pilots = $this->CheckPilotPresence($whazzup, $network_field, $user_networkid);

            if ($online_pilots && count($online_pilots) > 0) {
                $model_data['callsign'] = $online_pilots->first()->callsign;
                $model_data['is_online'] = 1;
            } else {
                $model_data['callsign'] = null;
                $model_data['is_online'] = 0;
            }

            DB_WhazzUpCheck::create($model_data);
            return;
        }
    }

    // Record Selected Network to Pirep Field Values
    public function RecordNetwork($pirep_id, $network_name)
    {
        PirepFieldValue::updateOrCreate(
            ['pirep_id' => $pirep_id, 'name' => 'Network Online', 'slug' => 'network-online'],
            ['value' => $network_name, 'source' => 1]
        );
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

    public function CheckPilotPresence($whazzup, $network_field, $user_networkid)
    {
        Log::debug('Disposable Basic | Searching ' . $user_networkid->value . ' in WhazzUp data (Presence Check)');
        $online_pilots = collect(json_decode($whazzup->pilots));

        return $online_pilots->where($network_field, $user_networkid->value);
    }
}
