<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Pirep;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use Illuminate\Support\Facades\Auth;

class UserPireps extends Widget
{
    protected $config = ['user' => null, 'limit' => null];

    public function run()
    {
        $limit = is_numeric($this->config['limit']) ? $this->config['limit'] : 25;
        $user_id = filled($this->config['user']) && is_numeric($this->config['user']) ? $this->config['user'] : Auth::id();

        $eager_load = ['aircraft', 'airline'];

        $where = [];
        $where['user_id'] = $user_id;
        $where['state'] = PirepState::ACCEPTED;
        $where['status'] = PirepStatus::ARRIVED;

        $user_pireps = Pirep::with($eager_load)->where($where)->orderBy('submitted_at', 'DESC')->take($limit)->get();

        return view('DBasic::widgets.user_pireps', [
            'is_visible'   => ($user_pireps->count() > 0) ? true : false,
            'limit'        => $limit,
            'pireps'       => $user_pireps,
            'units'        => DB_GetUnits(),
        ]);
    }
}
