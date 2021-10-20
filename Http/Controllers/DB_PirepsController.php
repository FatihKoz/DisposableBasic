<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Pirep;
use App\Models\Enums\PirepState;

class DB_PirepsController extends Controller
{
  // Pireps (except inProgress)
  public function index()
  {
    $pireps = Pirep::with('user', 'aircraft', 'airline', 'dpt_airport', 'arr_airport')->where('state', '!=', PirepState::IN_PROGRESS)->orderby('submitted_at', 'desc')->paginate(50);

    return view('DBasic::pireps.index',[
      'pireps' => $pireps,
    ]);
  }
}
