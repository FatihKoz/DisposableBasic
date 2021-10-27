<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\News;

class DB_NewsController extends Controller
{
    // News
    public function index()
    {
        $allnews = News::with('user')->orderby('created_at', 'DESC')->paginate(20);

        return view('DBasic::news.index', [
            'allnews'  => $allnews,
        ]);
    }
}
