<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;

class DB_PageController extends Controller
{
    // Live WX
    public function livewx()
    {
        $coordinates = setting('acars.center_coords');

        $divider = strpos($coordinates, ',');
        $lat = substr($coordinates, 0, $divider);
        $lon = substr($coordinates, ($divider + 1));

        return view('DBasic::pages.livewx', [
            'lat' => $lat,
            'lon' => $lon,
        ]);
    }
}
