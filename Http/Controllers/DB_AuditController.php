<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use App\Models\PirepFieldValue;
use App\Models\User;
use App\Models\UserField;
use App\Models\UserFieldValue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\CharsetConverter;
use League\Csv\Writer;

class DB_AuditController extends Controller
{
    public function ivao()
    {
        $network = 'IVAO';
        $audit_period = 91;
        $audit_start = Carbon::now()->subDays($audit_period)->startOfDay();
        $audit_start_buffer = $audit_start->copy()->subDays(1)->startOfDay();
        $audit_end = Carbon::now()->subDays(1)->endOfDay();

        $network_field_name = DB_Setting('dbasic.networkcheck_fieldname_ivao', 'IVAO ID');
        $network_field_id = optional(UserField::select('id')->where('name', $network_field_name)->first())->id;

        $network_ids = UserFieldValue::where('user_field_id', $network_field_id)->whereNotNull('value')->pluck('value')->toArray();
        $network_pilotids = UserFieldValue::where('user_field_id', $network_field_id)->whereNotNull('value')->pluck('user_id')->toArray();
        $network_pilots = User::whereIn('id', $network_pilotids)->orderBy('created_at')->get();
        $network_pireps = PirepFieldValue::where('slug', 'network-online')->where('value', $network)->whereBetween('created_at', [$audit_start_buffer, $audit_end])->pluck('pirep_id')->toArray();

        $eager_load = ['aircraft', 'airline', 'user', 'field_values'];
        $audit_pireps = Pirep::with($eager_load)->where('state', PirepState::ACCEPTED)->whereIn('user_id', $network_pilotids)->whereIn('id', $network_pireps)->whereBetween('submitted_at', [$audit_start, $audit_end])->orderBy('submitted_at')->get();
        $audit_pirepids = $audit_pireps->pluck('id')->toArray();
        $audit_pilotids = $audit_pireps->pluck('user_id')->toArray();
        $audit_pilots = User::whereIn('id', $audit_pilotids)->orderBy('created_at')->get();

        return view('DBasic::audits.ivao', [
            'audit_start'    => $audit_start,
            'audit_end'      => $audit_end,
            'audit_ids'      => isset($audit_pilotids) ? $audit_pilotids : null,
            'audit_pilots'   => isset($audit_pilots) ? $audit_pilots : null,
            'audit_pireps'   => isset($audit_pireps) ? $audit_pireps : null,
            'audit_pids'     => isset($audit_pirepids) ? json_encode($audit_pirepids) : null,
            'audit_time'     => isset($audit_pireps) ? $audit_pireps->sum('flight_time') : null,
            'field_name'     => $network_field_name,
            'is_admin'       => (optional(Auth::user())->hasRole(['admin'])) ? true : false,
            'network_ids'    => isset($network_ids) ? $network_ids : null,
            'network_pilots' => isset($network_pilots) ? $network_pilots : null,
        ]);
    }

    public function vatsim()
    {
        $network = 'VATSIM';
        $audit_period = 91;
        $audit_start = Carbon::now()->subDays($audit_period)->startOfDay();
        $audit_start_buffer = $audit_start->copy()->subDays(1)->startOfDay();
        $audit_end = Carbon::now()->subDays(1)->endOfDay();

        $network_field_name = DB_Setting('dbasic.networkcheck_fieldname_vatsim', 'VATSIM ID');
        $network_field_id = optional(UserField::select('id')->where('name', $network_field_name)->first())->id;

        $network_ids = UserFieldValue::where('user_field_id', $network_field_id)->whereNotNull('value')->pluck('value')->toArray();
        $network_pilotids = UserFieldValue::where('user_field_id', $network_field_id)->whereNotNull('value')->pluck('user_id')->toArray();
        $network_pilots = User::whereIn('id', $network_pilotids)->orderBy('created_at')->get();
        $network_pireps = PirepFieldValue::where('slug', 'network-online')->where('value', $network)->whereBetween('created_at', [$audit_start_buffer, $audit_end])->pluck('pirep_id')->toArray();

        $eager_load = ['aircraft', 'airline', 'user', 'field_values'];
        $audit_pireps = Pirep::with($eager_load)->where('state', PirepState::ACCEPTED)->whereIn('user_id', $network_pilotids)->whereIn('id', $network_pireps)->whereBetween('submitted_at', [$audit_start, $audit_end])->orderBy('submitted_at')->get();
        $audit_pirepids = $audit_pireps->pluck('id')->toArray();
        $audit_pilotids = $audit_pireps->pluck('user_id')->toArray();
        $audit_pilots = User::whereIn('id', $audit_pilotids)->orderBy('created_at')->get();

        return view('DBasic::audits.vatsim', [
            'audit_start'    => $audit_start,
            'audit_end'      => $audit_end,
            'audit_ids'      => isset($audit_pilotids) ? $audit_pilotids : null,
            'audit_pilots'   => isset($audit_pilots) ? $audit_pilots : null,
            'audit_pireps'   => isset($audit_pireps) ? $audit_pireps : null,
            'audit_pids'     => isset($audit_pirepids) ? json_encode($audit_pirepids) : null,
            'audit_time'     => isset($audit_pireps) ? $audit_pireps->sum('flight_time') : null,
            'field_name'     => $network_field_name,
            'is_admin'       => (optional(Auth::user())->hasRole(['admin'])) ? true : false,
            'network_ids'    => isset($network_ids) ? $network_ids : null,
            'network_pilots' => isset($network_pilots) ? $network_pilots : null,
        ]);
    }

    // Export Pireps
    public function export_pireps(Request $request)
    {
        $network = $request->network;
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);
        $pireps = Pirep::with(['aircraft', 'airline', 'user', 'field_values'])->whereIn('id', json_decode($request->pireps))->orderBy('submitted_at')->get();

        if ($network === 'ivao') {
            $network_field_name = DB_Setting('dbasic.networkcheck_fieldname_ivao', 'IVAO ID');
            $id_column = 'ivao_id';
        } else {
            $network_field_name = DB_Setting('dbasic.networkcheck_fieldname_vatsim', 'VATSIM ID');
            $id_column = 'vatsim_id';
        }

        $network_field_name = DB_Setting('dbasic.networkcheck_fieldname_vatsim', 'VATSIM ID');
        $network_field_id = optional(UserField::select('id')->where('name', $network_field_name)->first())->id;
        $network_ids = UserFieldValue::where('user_field_id', $network_field_id)->whereNotNull('value')->pluck('value', 'user_id')->toArray();

        $file_name = strtolower($network).'-audit-pireps-'.$start->format('dMY').'-'.$end->format('dMY').'.csv';
        $header = ['callsign', 'orig_icao', 'dest_icao', 'date', 'dep_time', 'arr_time', 'aircraft', $id_column];
        $path = $this->runExport($pireps, $header, $file_name, $network_ids);

        return response()->download($path, $file_name, ['content-type' => 'text/csv'])->deleteFileAfterSend(true);
    }

    protected function runExport(Collection $collection, $columns, $filename, $network_ids = []): string
    {
        // Create the directory under storage/app
        Storage::makeDirectory('export');
        $path = storage_path('/app/export/'.$filename);
        Log::info('Exporting audit pireps to '.$path);
        $writer = $this->openCsv($path);
        // Write out the header first
        $writer->insertOne($columns);
        // Write the rest of the rows
        foreach ($collection as $row) {
            $writer->insertOne($this->ProcessRow($row, $columns, $network_ids));
        }

        return $path;
    }

    protected function openCsv($path): Writer
    {
        $writer = Writer::createFromPath($path, 'w+');
        CharsetConverter::addTo($writer, 'utf-8', 'utf-8');

        return $writer;
    }

    protected function ProcessRow($row, $columns, $network_ids = []): array
    {
        $ret = [];

        // Prepare fields
        $ret['callsign'] = $row->fields->firstWhere('slug', 'network-callsign-used')->value ?? $row->airline->icao.$row->flight_number;
        $ret['orig_icao'] = $row->dpt_airport_id;
        $ret['dest_icao'] = $row->arr_airport_id;
        $ret['date'] = $row->submitted_at->format('d.M.Y');
        $ret['dep_time'] = $row->block_off_time->format('H:i');
        $ret['arr_time'] = $row->block_on_time->format('H:i');
        $ret['aircraft'] = $row->aircraft->icao;        
        $last_column = end($columns);
        $ret[$last_column] = $network_ids[$row->user_id] ?? '';

        return $ret;
    }
}
