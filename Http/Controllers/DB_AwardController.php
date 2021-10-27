<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Award;

class DB_AwardController extends Controller
{
    // Awards
    public function index()
    {
        $awards = Award::orderby('id')->get()->sortby('name', SORT_NATURAL);

        return view('DBasic::awards.index', [
            'awards' => $awards
        ]);
    }
}
