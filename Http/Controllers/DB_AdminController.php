<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class DB_AdminController extends Controller
{
    public function index()
    {
        // Get settings (of Disposable Basic)
        $settings = DB::table('disposable_settings')->where('key', 'LIKE', 'dbasic.%')->get();
        // $settings = $settings->groupBy('group'); // This may be used to have all settings in one card

        return view('DBasic::admin.index', [
            'settings' => $settings,
        ]);
    }

    // Update module settings
    public function settings_update()
    {
        $formdata = Request::post();
        $section = null;

        foreach ($formdata as $id => $value) {

            if ($id === 'group') {
                $section = $value;
            }

            $setting = DB::table('disposable_settings')->where('id', $id)->first();

            if (!$setting) {
                continue;
            }

            Log::debug('Disposable Basic, ' . $setting->group . ' setting for ' . $setting->name . ' changed to ' . $value);
            DB::table('disposable_settings')->where(['id' => $setting->id])->update(['value' => $value]);
        }

        flash()->success($section . ' settings saved.');
        return redirect(route('DBasic.admin'));
    }
}
