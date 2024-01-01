<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Enums\UserState;
use App\Models\User;
use League\ISO3166\ISO3166;

class DB_RosterController extends Controller
{
    // All Users
    public function index()
    {
        $eager_load = ['airline', 'current_airport', 'home_airport', 'last_pirep', 'rank'];
        $users = User::withCount('awards')->with($eager_load)->where('state', '<>', UserState::DELETED)->sortable(['pilot_id'])->paginate(50);

        return view('DBasic::roster.index', [
            'users'   => $users,
            'country' => new ISO3166(),
        ]);
    }
}
