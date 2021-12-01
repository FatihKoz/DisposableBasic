<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        // Check Settings
        $status = (DB_Setting('dbasic.stable_app_control', false) === true) ? null : 'Plugin support disabled';

        // Request Debug
        Log::debug('Request HEADER :' . json_encode($request->header()));
        Log::debug('Request IP :' . json_encode($request->ip()));
        Log::debug('Request POST :' . json_encode($request->post()));
        Log::debug('Request FILE :' . json_encode($request->file()));

        /*
        if (!isset($status)) {
            // Check Contents
            $received_file = $request->file('report');
            $extension = optional($received_file)->extension();
            $status = ($extension != 'json') ? 'Report not in proper format' : null;
        }
        */

        if (!isset($status)) {
            // Check Report Fields
            $report = DB_ProcessSapReport($request->post());
            $status = (isset($report->userID) && isset($report->plugin_version) && isset($report->requirementResultsGroups)) ? null : 'Report is not valid';
        }

        if (!isset($status)) {
            // Duplicate Report Check
            $duplicate = DB_StableApproach::where('sap_analysisID', $report->analysis->id)->count();
            $status = ($duplicate > 0) ? 'Already received' : null;
        }

        if (!isset($status)) {
            // Check Results
            $requirements = is_array($report->requirementResultsGroups) ? collect($report->requirementResultsGroups) : null;
            $is_stable = (isset($requirements) && $requirements->where('type', '2')->count()) ? false : true;
        }

        if (!isset($status)) {
            // Check User
            $where = ['name' => DB_Setting('dbasic.stable_app_field', 'Stable Approach ID'), 'active' => true];
            $field_id = DB::table('user_fields')->where($where)->value('id');
            $user_id = DB::table('user_field_values')->where(['user_field_id' => $field_id, 'value' => $report->userID])->value('user_id');
            $status = isset($user_id) ? null : 'No matching user';
        }

        if (!isset($status)) {
            // Check Pirep
            $pirep_id = DB::table('pireps')->where(['user_id' => $user_id, 'state' => 0])->orderBy('created_at', 'desc')->value('id');
            $status = isset($pirep_id) ? null : 'No active pirep';
        }

        if (!isset($status)) {
            // Save the report
            DB_StableApproach::updateOrCreate([
                'sap_userID'     => $report->userID,
                'sap_analysisID' => $report->analysis->id,
            ], [
                'user_id'        => $user_id,
                'pirep_id'       => $pirep_id,
                'is_stable'      => isset($is_stable) ? $is_stable : false,
                'raw_report'     => json_encode($report),
            ]);

            $response['received'] = 'OK';
            Log::debug('Disposable Basic, Stable Approach Report RECEIVED (A: ' . $report->analysis->id . ', P: ' . $pirep_id . ', U: ' . $user_id . ')');
        } else {
            // Add reason to the response
            $response['received'] = 'rejected';
            $response['reason'] = $status;
            Log::debug('Disposable Basic, Stable Approach Report REJECTED (' . $status . ')');
        }

        return response()->json($response);
    }
}
