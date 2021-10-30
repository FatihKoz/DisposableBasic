<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Models\DB_Runway;

class DB_RunwayController extends Controller
{
    public function index(Request $request)
    {
        if ($request->input('runway_delete')) {
            $runway = DB_Runway::where('id', $request->input('runway_delete'))->first();

            if (!$runway) {
                flash()->error('Runway not found !');
            } else {
                $runway->delete();
                flash()->success('Runway deleted.');
            }

            return redirect(route('DBasic.runway').'?airport='.$request->input('airport'));
        }

        if ($request->input('airport')) {
            $selected_a = $request->input('airport');
            $runways = DB_Runway::where('airport_id', $selected_a)->orderBy('runway_ident')->get();

            if (blank($runways)) {
                flash()->info('Selected airport has no runways defined or last runway was deleted.');
                return redirect(route('DBasic.runway'));
            }
        }
        
        $airports = DB::table('airports')->select('id', 'name')->orderBy('id')->get();
        $runways_array = DB::table('disposable_runways')->whereNotNull('airport_id')->groupBy('airport_id')->pluck('airport_id')->toArray();

        $airports_with = $airports->whereIn('id', $runways_array);
        $airports_without = $airports->whereNotIn('id', $runways_array);

        return view('DBasic::admin.runway', [
            'airports_r' => $airports_with,
            'airports_n' => $airports_without,
            'runways'    => isset($runways) ? $runways : null,
            'selected_a' => isset($selected_a) ? $selected_a : null,
        ]);
    }

    public function store(Request $request)
    {
        if (!$request->airport_id) {
            $error = 'Airport ICAO code is required !';
        }

        if (!$request->runway_ident || !$request->lat || !$request->lon || !$request->heading || !$request->lenght) {
            $error = 'Runway details are required';
        }

        if (isset($error)) {
            flash()->error($error);
            return redirect(route('DBasic.runway'));
        }

        DB_Runway::updateOrCreate(
            [
                'id' => $request->id,
            ],[
                'airport_id'   => $request->airport_id,
                'runway_ident' => $request->runway_ident,
                'lat'          => $request->lat,
                'lon'          => $request->lon,
                'heading'      => $request->heading,
                'lenght'       => $request->lenght,
                'ils_freq'     => $request->ils_freq,
                'loc_course'   => $request->loc_course,
                'airac'        => $request->airac,
            ]
        );

        flash()->success('Runway saved');
        return redirect(route('DBasic.runway').'?airport='.$request->airport_id);
    }
}
