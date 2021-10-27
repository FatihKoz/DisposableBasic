<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Rank;

class DB_RankController extends Controller
{
    // Ranks
    public function index()
    {
        $ranks = Rank::with('subfleets.airline')->orderby('hours')->get();

        return view('DBasic::ranks.index', [
            'currency' => setting('units.currency'),
            'ranks'    => $ranks,
        ]);
    }
}
