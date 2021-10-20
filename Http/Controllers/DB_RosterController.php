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
    $users = User::with('airline', 'rank')->orderby('id')->get();

    return view('DBasic::roster.index', [
      'users'   => $users,
      'country' => new ISO3166(),
    ]);
  }
}
