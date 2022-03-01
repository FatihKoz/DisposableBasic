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
        $sap_reports = DB_StableApproach::with('user', 'pirep')->where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(12);

        return view('DBasic::sap.index', [
            'sap_reports'     => $sap_reports,
            'approach_lights' => DB_XPlane_SDK('applights'),
            'runway_marking'  => DB_XPlane_SDK('markings'),
            'runway_surface'  => DB_XPlane_SDK('surface'),
        ]);
    }

    public function store(Request $request)
    {
        // Check Settings
        $status = (DB_Setting('dbasic.stable_app_control', false) === true) ? null : 'Plugin support disabled';

        // Handle Request
        $request_post = json_encode($request->post());

        if (!isset($status)) {
            // Check Report Fields
            $report = json_decode($request_post);
            $status = (isset($report->userID) && isset($report->plugin_version)) ? null : 'Report is not valid';
        }

        if (!isset($status)) {
            // Check Messages Section
            $status = (isset($report->messages) && filled($report->messages)) ? null : 'Report is not complete';
        }

        if (!isset($status)) {
            // Check Duplicate
            $duplicate = DB_StableApproach::where('sap_analysisID', $report->analysis->id)->count();
            $status = ($duplicate > 0) ? 'Already received' : null;
        }

        if (!isset($status)) {
            // Check if this is a "Stable Approach"
            // type 1 is used for informations, type 2 is for warnings/non acceptable items
            $requirements = is_array($report->messages) ? collect($report->messages) : null;
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

    public function update(Request $request)
    {
        $report = DB_StableApproach::where(['id' => $request->report_id, 'is_stable' => 0])->first();

        if ($report && $request->operation == 'update') {
            $report->is_stable = 1;
            $report->save();
            Log::debug('Stable Approach Report ' . $report->sap_analysisID . ' updated by ' . Auth::user()->name_private);
            flash()->success('Report approved as STABLE');
        } elseif ($report && $request->operation == 'delete') {
            $report->delete();
            Log::debug('Stable Approach Report ' . $report->sap_analysisID . ' deleted by ' . Auth::user()->name_private);
            flash()->success('Report Deleted !');
        } else {
            flash()->warning('Report not found or not suitable for update');
        }

        return redirect(url($request->current_page));
    }
}
