<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NextRank extends Widget
{
    protected $config = ['display' => null];

    public function run()
    {
        $user = User::with('rank')->find(Auth::id());
        $curr_time = setting('pilots.count_transfer_hours', false) ? round(($user->flight_time + $user->transfer_time) / 60, 0) : round($user->flight_time / 60, 0);
        $curr_rank = $user->rank;
        $next_rank = Rank::where('hours', '>', $curr_time)->where('auto_promote', 1)->orderby('hours')->first();

        return view('DBasic::widgets.next_rank', [
            'config'    => $this->config,
            'curr_time' => $curr_time,
            'curr_rank' => $curr_rank,
            'next_rank' => $next_rank,
            'ratio'     => round((100 * $curr_time) / $next_rank->hours, 2),
            'missing'   => $next_rank->hours - $curr_time,
            'notice'    => ($curr_rank->hours > $next_rank->hours) ? true : false,
            'last'      => (blank($next_rank)) ? true : false,
        ]);
    }
}
