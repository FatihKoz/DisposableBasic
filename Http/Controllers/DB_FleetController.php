<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Aircraft;
use App\Models\Pirep;
use App\Models\Subfleet;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Modules\DisposableBasic\Services\DB_FleetServices;
use Modules\DisposableBasic\Services\DB_StatServices;
use Modules\DisposableSpecial\Models\DS_Maintenance;

class DB_FleetController extends Controller
{
    // Fleet
    public function index()
    {
        // User Based Fleet Display
        // Follow phpVMS Settings
        $option = (setting('pireps.restrict_aircraft_to_rank', false) || setting('pireps.restrict_aircraft_to_typerating', false)) ? true : false;
        // Use Module Setting
        // $option = DB_Setting('dbasic.fleet_user_aircraft', true);
        $user = Auth::user();
        $userSvc = app(UserService::class);
        $user_based_fleet = $userSvc->getAllowableSubfleets($user)->pluck('id')->toArray();

        $withCount = ['simbriefs' => function ($query) { $query->whereNull('pirep_id'); }];
        $with = ['airline', 'subfleet'];

        $aircraft = Aircraft::withCount($withCount)->with($with)->when($option, function ($query) use ($user_based_fleet) {
            return $query->whereIn('subfleet_id', $user_based_fleet);
        })->orderby('icao')->orderby('registration')->paginate(50);

        return view('DBasic::fleet.index', [
            'aircraft' => $aircraft,
            'units'    => $this->GetUnits(),
        ]);
    }

    // Subfleet
    public function subfleet($subfleet_type)
    {
        $units = $this->GetUnits();

        $withCount_sf = ['flights', 'fares'];
        $with_sf = ['airline', 'fares', 'files', 'hub', 'typeratings'];

        $subfleet = Subfleet::withCount($withCount_sf)->with($with_sf)->where('type', $subfleet_type)->first();

        if (!$subfleet) {
            flash()->error('Subfleet not found !');
            return redirect(route('DBasic.fleet'));
        }

        $withCount_ac = ['simbriefs' => function ($query) { $query->whereNull('pirep_id'); }];
        $with_ac = ['airline', 'subfleet'];

        $aircraft = Aircraft::withCount($withCount_ac)->with($with_ac)->where('subfleet_id', $subfleet->id)->orderby('registration')->get();

        // Latest Pireps
        $where = ['state' => 2, 'status' => 'ONB'];
        $aircraft_array = $aircraft->pluck('id')->toArray();
        $with_pireps = ['airline', 'arr_airport', 'dpt_airport', 'user'];

        $pireps = Pirep::with($with_pireps)->where($where)->whereIn('aircraft_id', $aircraft_array)->orderby('submitted_at', 'desc')->take(5)->get();

        // Subfleet Image
        $FleetSvc = app(DB_FleetServices::class);
        $image = $FleetSvc->SubfleetImage($subfleet);

        // Specifications
        $specs = DB_GetSpecs_SF($subfleet, true);

        // Files (only Subfleet level)
        $files = $subfleet->files;

        // Overflow Size Adjustment for blade
        $overflow_mh = 78;
        if(filled($files)) { $overflow_mh = $overflow_mh - 20; }
        if(filled($specs)) { $overflow_mh = $overflow_mh - 20; }
        if(filled($pireps)) { $overflow_mh = $overflow_mh - 20; }

        return view('DBasic::fleet.subfleet', [
            'aircraft' => $aircraft,
            'files'    => filled($files) ? $files : null,
            'image'    => $image,
            'over_mh'  => $overflow_mh,
            'pireps'   => filled($pireps) ? $pireps : null,
            'specs'    => $specs,
            'subfleet' => $subfleet,
            'units'    => $units,
        ]);
    }

    // Aircraft
    public function aircraft($ac_reg)
    {
        $units = $this->GetUnits();

        $withCount = ['simbriefs' => function ($query) { $query->whereNull('pirep_id'); }];
        $with_aircraft = ['airline', 'airport', 'files', 'hub', 'subfleet.fares', 'subfleet.files', 'subfleet.hub', 'subfleet.typeratings'];

        $aircraft = Aircraft::withCount($withCount)->with($with_aircraft)->where('registration', $ac_reg)->first();

        if (!$aircraft) {
            flash()->error('Aircraft not found !');
            return redirect(route('DBasic.fleet'));
        }

        // Latest Pireps
        $where = ['aircraft_id' => $aircraft->id, 'state' => 2];
        $with_pirep = ['dpt_airport', 'arr_airport', 'user', 'airline'];

        $pireps = Pirep::with($with_pirep)->where($where)->orderby('submitted_at', 'desc')->take(5)->get();

        // Aircraft or Subfleet Image
        $FleetSvc = app(DB_FleetServices::class);
        $image = $FleetSvc->AircraftImage($aircraft);

        // Specifications
        $specs = DB_GetSpecs($aircraft, true);

        // Maintenance Status
        $maint = DB_CheckModule('DisposableSpecial') ? DS_Maintenance::where('aircraft_id', $aircraft->id)->first() : null;

        // Stats
        $StatSvc = app(DB_StatServices::class);
        $stats = $StatSvc->PirepStats(null, $aircraft->id);

        // Combined files of aircraft and it's subfleet;
        $files = $aircraft->files;
        if ($aircraft->subfleet) {
            $files = $files->concat($aircraft->subfleet->files);
        }

        return view('DBasic::fleet.aircraft', [
            'aircraft'   => $aircraft,
            'files'      => filled($files) ? $files : null,
            'image'      => $image,
            'maint'      => filled($maint) ? $maint : null,
            'pireps'     => filled($pireps) ? $pireps : null,
            'specs'      => $specs,
            'stats'      => $stats,
            'units'      => $units,
        ]);
    }

    private function GetUnits()
    {
        $units = DB_GetUnits();

        // Passenger Weight
        $units['pax_weight'] = setting('simbrief.noncharter_pax_weight');
        if ($units['weight'] === 'kg') {
            $units['pax_weight'] = round($units['pax_weight'] / 2.20462262185, 2);
        }

        return $units;
    }
}
