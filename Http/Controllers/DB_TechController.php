<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Models\DB_Tech;

class DB_TechController extends Controller
{
    public function index(Request $request)
    {
        if ($request->input('tech_delete')) {
            $tech = DB_Tech::where('id', $request->input('tech_delete'))->first();

            if (!$tech) {
                flash()->error('Record not found !');
            } else {
                $tech->delete();
                flash()->warning('Record deleted !');
            }

            return redirect(route('DBasic.tech'));
        }

        if ($request->input('tech_edit')) {
            $tech = DB_Tech::where('id', $request->input('tech_edit'))->first();

            if (!$tech) {
                flash()->error('Record not found !');
                return redirect(route('DBasic.tech'));
            }
        }

        //Get Aircraft ICAO List
        $tech_details = DB_Tech::orderby('icao')->get();
        $defined_icao_types = $tech_details->pluck('icao')->all();
        $icao_types = DB::table('aircraft')->whereNotNull('icao')->whereNotIn('icao', $defined_icao_types)->groupby('icao')->orderby('icao')->pluck('icao')->toArray();

        return view('DBasic::admin.tech', [
            'icao_types'   => $icao_types,
            'tech'         => isset($tech) ? $tech : null,
            'tech_details' => $tech_details,
            'units'        => DB_GetUnits(),
        ]);
    }

    public function store(Request $request)
    {

        if (!$request->icao) {
            flash()->error('ICAO Type code is required !');
            return redirect(route('DBasic.tech'));
        }

        DB_Tech::updateOrCreate(
            [
                'icao'         => $request->icao,
            ],[
                'f0_name'      => $request->f0_name,
                'f0_speed'     => $request->f0_speed,
                'f1_name'      => $request->f1_name,
                'f1_speed'     => $request->f1_speed,
                'f2_name'      => $request->f2_name,
                'f2_speed'     => $request->f2_speed,
                'f3_name'      => $request->f3_name,
                'f3_speed'     => $request->f3_speed,
                'f4_name'      => $request->f4_name,
                'f4_speed'     => $request->f4_speed,
                'f5_name'      => $request->f5_name,
                'f5_speed'     => $request->f5_speed,
                'f6_name'      => $request->f6_name,
                'f6_speed'     => $request->f6_speed,
                'f7_name'      => $request->f7_name,
                'f7_speed'     => $request->f7_speed,
                'f8_name'      => $request->f8_name,
                'f8_speed'     => $request->f8_speed,
                'f9_name'      => $request->f9_name,
                'f9_speed'     => $request->f9_speed,
                'f10_name'     => $request->f10_name,
                'f10_speed'    => $request->f10_speed,
                'gear_extend'  => $request->gear_extend,
                'gear_retract' => $request->gear_retract,
                'gear_maxtire' => $request->gear_maxtire,
                'max_pitch'    => $request->max_pitch,
                'max_roll'     => $request->max_roll,
                'max_cycle_a'  => $request->max_cycle_a,
                'max_time_a'   => $request->max_time_a,
                'duration_a'   => $request->duration_a,
                'max_cycle_b'  => $request->max_cycle_b,
                'max_time_b'   => $request->max_time_b,
                'duration_b'   => $request->duration_b,
                'max_cycle_c'  => $request->max_cycle_c,
                'max_time_c'   => $request->max_time_c,
                'duration_c'   => $request->duration_c,
                'avg_fuel'     => $request->avg_fuel,
                'mzfw'         => $request->mzfw,
                'mrw'          => $request->mrw,
                'mtow'         => $request->mtow,
                'mlaw'         => $request->mlaw,
                'active'       => $request->active,
            ]
        );

        flash()->success('Technical details saved');
        return redirect(route('DBasic.tech'));
    }
}
