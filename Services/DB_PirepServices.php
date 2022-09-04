<?php

namespace Modules\DisposableBasic\Services;

use App\Models\UserField;
use App\Models\UserFieldValue;
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
        $network_name = DB_Setting('dbasic.networkcheck_server', 'IVAO');
        $user_field_name = DB_Setting('dbasic.networkcheck_fieldname', 'IVAO ID');
        $user_field_id = optional(UserField::select('id')->where('name', $user_field_name)->first())->id;

        if ($network_name === 'VATSIM') {
            $network_field = 'cid';
            $network_server = 'https://data.vatsim.net/v3/vatsim-data.json';
        } else {
            $network_field = 'userId';
            $network_server = 'https://api.ivao.aero/v2/tracker/whazzup';
        }
        $network_refresh = 150;

        // Get user's Network ID
        $user_networkid = UserFieldValue::select('value')->where(['user_field_id' => $user_field_id, 'user_id' => $pirep->user_id])->first();

        // Get WhazzUp Data (it may be already downloaded by cron or by WhazzUp Widget)
        $whazzup = DB_WhazzUp::where('network', $network_name)->orderby('updated_at', 'desc')->first();

        // Update if necessary
        if (!$whazzup || $whazzup->updated_at->diffInSeconds() > $network_refresh) {
            $OnlineSvc = app(DB_OnlineServices::class);
            $whazzup = $OnlineSvc->DownloadWhazzUp($network_name, $network_server);
            Log::debug('Disposable Basic | Downloading WhazzUp Data For Pirep Checks');
        }

        // Get all pilots and reduce the collection to a specific user
        if ($whazzup && $user_networkid) {

            $model_data = [];
            $model_data['user_id'] = $pirep->user_id;
            $model_data['pirep_id'] = $pirep->id;
            $model_data['network'] = $network_name;

            $online_pilots = collect(json_decode($whazzup->pilots));
            $online_pilots = $online_pilots->where($network_field, $user_networkid->value);

            if ($online_pilots && count($online_pilots) > 0) {
                $model_data['is_online'] = 1;
            } else {
                $model_data['is_online'] = 0;
            }

            DB_WhazzUpCheck::create($model_data);
            return;
        }
    }
}
