<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NextRank extends Widget
{
    protected $config = ['user' => null, 'card' => true];

    public function run()
    {
        $userid = (filled($this->config['user']) && is_numeric($this->config['user'])) ? $this->config['user'] : Auth::id();
        $user = User::with(['rank', 'location', 'journal'])->find($userid);
        $curr_time = setting('pilots.count_transfer_hours', false) ? round(($user->flight_time + $user->transfer_time) / 60, 0) : round($user->flight_time / 60, 0);
        $curr_rank = $user->rank;
        $next_rank = Rank::where('hours', '>', $curr_time)->where('auto_promote', 1)->orderby('hours')->first();

        return view('DBasic::widgets.next_rank', [
            'card'      => is_bool($this->config['card']) ? $this->config['card'] : true,
            'curr_rank' => $curr_rank,
            'curr_time' => $curr_time,
            'last'      => blank($next_rank) ? true : false,
            'missing'   => filled($next_rank) ? $next_rank->hours - $curr_time : null,
            'next_rank' => $next_rank,
            'notice'    => filled($next_rank) ? (($curr_rank->hours > $next_rank->hours) ? true : false) : false,
            'ratio'     => filled($next_rank) ? round((100 * $curr_time) / $next_rank->hours, 0) : 100,
            'user'      => $user,
        ]);
    }
}
