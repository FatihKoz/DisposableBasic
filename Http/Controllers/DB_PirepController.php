<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Pirep;
use App\Models\Enums\PirepState;

class DB_PirepController extends Controller
{
    // All Pireps (except inProgress)
    public function index()
    {
        $eager_load = ['user', 'aircraft', 'airline', 'dpt_airport', 'arr_airport', 'simbrief'];
        $pireps = Pirep::withCount('comments')->with($eager_load)->where('pireps.state', '!=', PirepState::IN_PROGRESS)->sortable(['submitted_at' => 'desc'])->paginate(50);

        return view('DBasic::pireps.index', [
            'DSpecial' => DB_CheckModule('DisposableSpecial'),
            'pireps'   => $pireps,
            'units'    => DB_GetUnits(),
        ]);
    }
}
