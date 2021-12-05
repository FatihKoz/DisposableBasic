<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Aircraft;
use Illuminate\Support\Facades\DB;

class FleetOverview extends Widget
{
    protected $config = ['type' => 'location', 'hubs' => true];

    public function run()
    {
        $type = $this->config['type'];
        $hubs = is_bool($this->config['hubs']) ? $this->config['hubs'] : true;

        if ($type === 'icao') {
            $col_header = __('DBasic::common.icao');
            $footer_note = __('DBasic::widgets.icaotype');

            $fleet = Aircraft::selectRaw('icao, count(id) as totals')->groupBy('icao')->orderBy('icao', 'asc')->get();
        } elseif ($type === 'subfleet') {
            $col_header = __('DBasic::common.subfleet');
            $footer_note = __('DBasic::widgets.bysubfleet');

            $fleet = Aircraft::with('subfleet.airline')->selectRaw('subfleet_id, count(id) as totals')
                ->groupBy('subfleet_id')->orderBy('totals', 'desc')->orderBy('subfleet_id')->get();
        } else {
            $col_header = __('DBasic::common.location');
            $footer_note = __('DBasic::widgets.bylocation');
            $hubs_array = [];
            if ($hubs === false) {
                $footer_note = __('DBasic::widgets.nonhubloc');
                $hubs_array = DB::table('airports')->where('hub', 1)->pluck('id')->toArray();
            }

            $fleet = Aircraft::with('airport')->selectRaw('airport_id, count(id) as totals')->whereNotNull('airport_id')
                ->when($hubs === false, function ($query) use ($hubs_array) {
                    return $query->whereNotIn('airport_id', $hubs_array);
                })->groupBy('airport_id')->orderBy('totals', 'desc')->get();
        }

        return view('DBasic::widgets.fleet_overview', [
            'col_header'  => $col_header,
            'fleet'       => $fleet,
            'footer_note' => $footer_note,
            'hubs'        => $hubs,
            'is_visible'  => isset($fleet) ? true : false,
            'type'        => $type,
        ]);
    }
}
