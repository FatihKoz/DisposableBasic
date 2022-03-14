<?php

use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Models\DB_Runway;
use Modules\DisposableBasic\Models\DB_Spec;

// Get Average Taxi Time for given airport
// Return numeric string
if (!function_exists('DB_AvgTaxiTime')) {
    function DB_AvgTaxiTime($icao, $type = 'out', $default = 10)
    {
        if ($type === 'in') {
            $dep_arr = 'arr_airport_id';
            $out_in = 'taxi-in-time';
        } else {
            $dep_arr = 'dpt_airport_id';
            $out_in = 'taxi-out-time';
        }

        $pireps = DB::table('pireps')->select('id')->where([$dep_arr => $icao, 'state' => 2])->pluck('id')->all();
        $field_values = DB::table('pirep_field_values')->select('value')->whereIn('pirep_id', $pireps)->where('slug', $out_in)->orderby('created_at', 'desc')->take(100)->pluck('value')->all();
        $taxi_times = collect();

        foreach ($field_values as $fv) {
            $duration = substr($fv, 0, stripos($fv, 'm'));
            if (is_numeric($duration) && $duration > 0) {
                $taxi_times->push($duration);
            }
        }

        $avg_time = $taxi_times->avg();
        if ($avg_time > 0) {
            $result = ceil($avg_time);
        }

        return isset($result) ? $result : $default;
    }
}

// Get runways of an airport (by icao code)
if (!function_exists('DB_GetRunways')) {
    function DB_GetRunways($icao)
    {
        $runways = DB_Runway::where('airport_id', $icao)->orderby('runway_ident', 'asc')->get();
        return $runways;
    }
}

// Get detailed addon specifications for an Aircraft 
// Start from Aircraft level, then check Subfleet and ICAO Type (with $deep_check = true)
if (!function_exists('DB_GetSpecs')) {
    function DB_GetSpecs($aircraft, $deep_check = false)
    {
        $specs = DB_Spec::where(['aircraft_id' => $aircraft->id, 'active' => true])->orderby('saircraft')->get();

        if ($deep_check && blank($specs) && filled($aircraft->subfleet)) {
            $specs = DB_GetSpecs_SF($aircraft->subfleet);
        }
        if ($deep_check && blank($specs)) {
            $specs = DB_GetSpecs_ICAO($aircraft->icao);
        }

        return filled($specs) ? $specs : [];
    }
}

// Get detailed addon specificatiions for a Subfleet
if (!function_exists('DB_GetSpecs_SF')) {
    function DB_GetSpecs_SF($subfleet, $deep_check = false)
    {
        $specs = DB_Spec::where(['subfleet_id' => $subfleet->id, 'active' => true])->orderby('saircraft')->get();

        if ($deep_check && blank($specs)) {
            $specs = DB_GetSpecs_ICAO(substr($subfleet->type, 0, 4));
        }
        return filled($specs) ? $specs : null;
    }
}

// Get detailed addon specifications for an ICAO Type
if (!function_exists('DB_GetSpecs_ICAO')) {
    function DB_GetSpecs_ICAO($icao)
    {
        $specs = DB_Spec::where(['icao_id' => $icao, 'active' => true])->orderby('saircraft')->get();
        return filled($specs) ? $specs : null;
    }
}