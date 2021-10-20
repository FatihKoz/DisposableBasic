<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;

class DB_AdminController extends Controller
{
  public function index()
  {
    return view('DBasic::admin.index');
  }
}
