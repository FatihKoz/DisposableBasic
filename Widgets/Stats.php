<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Services\DB_StatServices;

class Stats extends Widget
{
    protected $config = ['type' => null, 'id' => null];

    public function run()
    {
        $id = is_numeric($this->config['id']) ? $this->config['id'] : null;
        $type = isset($this->config['type']) ? $this->config['type'] : 'airline';

        $StatSvc = app(DB_StatServices::class);

        if ($type === 'aircraft' && isset($id)) {
            $icon = 'fa-plane';
            $stats = $StatSvc->PirepStats(null, $id);

            $details = DB::table('aircraft')->select('registration', 'name')->where('id', $id)->first();
            $main = 'registration';
        } elseif ($type === 'airline' && isset($id)) {
            $icon = 'fa-home';
            $basic_stats = $StatSvc->BasicStats($id);
            $pirep_stats = $StatSvc->PirepStats($id);
            $stats = array_merge($basic_stats, $pirep_stats);

            $details = DB::table('airlines')->select('icao', 'name')->where('id', $id)->first();
            $main = 'icao';
        } elseif ($type === 'home') {
            $stats = $StatSvc->PirepStats();

            $footer_note = config('app.name');
        } else {
            $basic_stats = $StatSvc->BasicStats();
            $pirep_stats = $StatSvc->PirepStats();
            $stats = array_merge($basic_stats, $pirep_stats);

            $footer_note = config('app.name');
        }

        if (isset($details)) {
            $header_note = ' | ' . $details->$main;
            if ($details->$main != $details->name) {
                $footer_note = $details->name;
            }
        }

        return view('DBasic::widgets.stats', [
            'footer_note' => isset($footer_note) ? $footer_note : null,
            'header_note' => isset($header_note) ? $header_note : null,
            'icon'        => isset($icon) ? $icon : 'fa-cogs',
            'is_visible'  => (isset($stats) && count($stats) > 0) ? true : false,
            'stats'       => is_array($stats) ? $stats : null,
        ]);
    }
}
