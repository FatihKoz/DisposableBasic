<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Modules\DisposableBasic\Models\DB_Session;
use Carbon\Carbon;

class ActiveUsers extends Widget
{
    public $reloadTimeout = 75;

    protected $config = ['margin' => null];

    public function run()
    {
        $margin = is_numeric($this->config['margin']) ? $this->config['margin'] : 5;
        $inactive_time = Carbon::now()->addMinutes($margin);

        $active_users = DB_Session::with('user')->select('user_id', 'last_activity')
            ->whereNotNull('user_id')->where('last_activity', '>', $inactive_time)
            ->orderby('user_id')->get();

        return view('DBasic::widgets.active_users', [
            'active_users' => $active_users,
            'is_visible'   => isset($active_users) ? true : false,
        ]);
    }
}
