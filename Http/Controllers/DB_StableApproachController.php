<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Models\DB_StableApproach;

class DB_StableApproachController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $reports = DB_StableApproach::with('user', 'pirep')->where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(12);

        return view('DBasic::sap.index', [
            'reports' => $reports,
        ]);
    }

    public function store(Request $request)
    {
        $status = (DB_Setting('dbasic.stable_app_control', false) === true) ? null : 'Plugin Support Disabled';

        if (!isset($status)) {
            $received_file = $request->file('report');
            $extension = $received_file->extension();
    
            $status = ($extension != 'json') ? 'Report not in proper format ! Process aborted' : null;
        }

        if (!isset($status)) {
            $report = DB_ReadSapReport($received_file->path());

            $status = (isset($report->userID) && isset($report->plugin_version)) ? null : 'Report is not valid ! Process aborted';

            if (!isset($status)) {
                $where = ['name' => DB_Setting('dbasic.stable_app_field', 'Stable Approach ID'), 'active' => true];
                $field_id = DB::table('user_fields')->where($where)->value('id');
                $user_id = DB::table('user_field_values')->where(['user_field_id' => $field_id, 'value' => $report->userID])->value('user_id');

                $status = isset($user_id) ? null : 'No matching user found ! Process aborted';
            }

            if (!isset($status)) {
                $pirep_id = DB::table('pireps')->where(['user_id' => $user_id, 'state' => 0])->orderBy('created_at', 'desc')->value('id');

                $status = isset($pirep_id) ? null : 'No active pirep found ! Process aborted';
            }
        }

        $new_report = isset($status) ? null : DB_StableApproach::create([
            'sap_userID'     => $report->userID,
            'sap_analysisID' => $report->analysis->id,
            'user_id'        => $user_id,
            'pirep_id'       => $pirep_id,
            'raw_report'     => json_encode($report),
        ]);

        return view('DBasic::sap.received', [
            'status' => isset($status) ? $status : 'OK',
            'report' => $new_report,
        ]);
    }
}
