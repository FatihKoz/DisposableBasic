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

        if (isset($sap_report)) {
            $stable = json_decode($sap_report->raw_report);
            $requirements = is_array($stable->requirementResultsGroups) ? collect($stable->requirementResultsGroups) : null;
        }

        return view('DBasic::widgets.stable_approach', [
            'is_stable'  => (isset($requirements) && $requirements->where('type', '2')->count()) ? false : true,
            'is_visible' => isset($sap_report) ? true : false,
            'report'     => isset($sap_report) ? $sap_report : null,
            'stable'     => isset($stable) ? $stable : null,
            'use_button' => is_bool($this->config['button']) ? $this->config['button'] : false,
        ]);
    }
}
