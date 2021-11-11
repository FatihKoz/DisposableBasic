<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\User;
use League\ISO3166\ISO3166;

class DB_RosterController extends Controller
{
    // All Users
    public function index()
    {
        $eager_load = ['airline', 'current_airport', 'home_airport', 'last_pirep', 'rank'];
        $users = User::withCount('awards')->with($eager_load)->orderby('id')->paginate(30);

        return view('DBasic::roster.index', [
            'users'   => $users,
            'country' => new ISO3166(),
        ]);
    }
}
