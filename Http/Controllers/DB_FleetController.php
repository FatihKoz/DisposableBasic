<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use App\Models\Aircraft;
use App\Models\Pirep;
use App\Models\Subfleet;
use Modules\DisposableBasic\Services\DB_FleetServices;

class DB_FleetController extends Controller
{
    // Fleet
    public function index()
    {
        $aircraft = Aircraft::with('subfleet.airline')->orderby('icao')->orderby('registration')->paginate(50);

        return view('DBasic::fleet.index', [
            'aircraft' => $aircraft,
            'units'    => $this->GetUnits(),
        ]);
    }

    // Subfleet
    public function subfleet($subfleet_type)
    {
        $units = $units = $this->GetUnits();

        $subfleet = Subfleet::withCount('flights', 'fares')->with('airline', 'fares', 'hub')->where('type', $subfleet_type)->first();
        $aircraft = Aircraft::withCount('simbriefs')->with('subfleet.airline')->where('subfleet_id', $subfleet->id)->orderby('registration')->get();
        
        // Latest Pireps
        $where = array('state' => 2);
        $aircraft_array = $aircraft->pluck('id')->toArray();
        $eager_pireps = array('dpt_airport', 'arr_airport', 'user', 'airline');
        $pireps = Pirep::with($eager_pireps)->where($where)->whereIn('aircraft_id', $aircraft_array)->orderby('submitted_at', 'desc')->take(5)->get();

        if (!$subfleet) {
            flash()->error('Subfleet not found !');
            return redirect(route('DBasic.fleet'));
        }

        // Subfleet Image
        $FleetSvc = app(DB_FleetServices::class);
        $image = $FleetSvc->SubfleetImage($subfleet);

        // Specifications
        $specs = DB_GetSpecs_SF($subfleet);

        // Files (only Subfleet level)
        $files = $subfleet->files;

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

        $eager_aircraft = array('airport', 'files', 'subfleet.airline', 'subfleet.fares', 'subfleet.files', 'subfleet.hub');
        $aircraft = Aircraft::with($eager_aircraft)->where('registration', $ac_reg)->first();

        if (!$aircraft) {
            flash()->error('Aircraft not found !');
            return redirect(route('DBasic.fleet'));
        }

        // Latest Pireps
        $where = array('aircraft_id' => $aircraft->id, 'state' => 2);
        $eager_pireps = array('dpt_airport', 'arr_airport', 'user', 'airline');
        $pireps = Pirep::with($eager_pireps)->where($where)->orderby('submitted_at', 'desc')->take(5)->get();

        // Aircraft or Subfleet Image
        $FleetSvc = app(DB_FleetServices::class);
        $image = $FleetSvc->AircraftImage($aircraft);

        // Specifications
        $specs = DB_GetSpecs($aircraft, true);

        // Combined files of aircraft and it's subfleet;
        $files = $aircraft->files;
        if ($aircraft->subfleet) {
            $files = $files->concat($aircraft->subfleet->files);
        }

        return view('DBasic::fleet.aircraft', [
            'aircraft'   => $aircraft,
            'files'      => filled($files) ? $files : null,
            'image'      => $image,
            'maint'      => null,
            'pireps'     => filled($pireps) ? $pireps : null,
            'specs'      => $specs,
            'units'      => $units,
        ]);
    }

    private function GetUnits()
    {
        $units = array(
            'fuel'     => setting('units.fuel'),
            'weight'   => setting('units.weight'),
            'distance' => setting('units.distance'),
            'altitude' => setting('units.altitude')
        );

        // Passenger Weight
        $units['pax_weight'] = setting('simbrief.noncharter_pax_weight');
        if ($units['weight'] === 'kg') {
            $units['pax_weight'] = round($units['pax_weight'] / 2.20462262185, 2);
        }

        return $units;
    }
}
