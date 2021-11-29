<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Modules\DisposableBasic\Models\DB_StableApproach;

class StableApproach extends Widget
{
    protected $config = ['pirep' => null, 'button' => false];

    public function run()
    {
        $pirep = $this->config['pirep'];

        if ($pirep) {
            $where = ['user_id' => $pirep->user_id, 'pirep_id' => $pirep->id];
            $sap_report = DB_StableApproach::where($where)->first();
        }

        return view('DBasic::widgets.stable_approach', [
            'is_stable'  => (isset($sap_report) && $sap_report->is_stable == 1) ? true : false,
            'is_visible' => isset($sap_report) ? true : false,
            'report'     => isset($sap_report) ? $sap_report : null,
            'stable'     => isset($sap_report) ? json_decode($sap_report->raw_report) : null,
            'use_button' => is_bool($this->config['button']) ? $this->config['button'] : false,
        ]);
    }
}
