<?php

use App\Models\Pirep;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Models\DB_Runway;
use Modules\DisposableBasic\Models\DB_Spec;
use Modules\DisposableBasic\Models\DB_Tech;

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
    function DB_GetSpecs_SF($subfleet)
    {
        $specs = DB_Spec::where(['subfleet_id' => $subfleet->id, 'active' => true])->orderby('saircraft')->get();
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

/*
if (!function_exists('Dispo_GetICAOTech')) {
  // Get Technical Details of an ICAO Type
  function Dispo_GetICAOTech($icao = null) {
    $tech = Disposable_Tech::where(['icao' => $icao, 'active' => true])->first();
    return $tech;
  }
}

if (!function_exists('Dispo_SimBriefWeight')) {
  // Convert Any Given Weight Value for SimBrief acdata json
  // which needs main weights as lbs with 3 digits precision (except paxwgt)
  function Dispo_SimBriefWeight($weight_value) {
    $kgstolbs = 2.20462262185;
    if (setting('units.weight') === 'kg') { $weight_value = round(($weight_value * $kgstolbs) / 1000, 3); }
    else { $weight_value = round($weight_value / 1000, 3); }
    return $weight_value;
  }
}

if (!function_exists('Dispo_SimBriefPaxWeight')) {
  // Convert Given Pax Weight Value for SimBrief acdata json
  // which needs to be lbs
  function Dispo_SimBriefPaxWeight($weight_value) {
    $kgstolbs = 2.20462262185;
    if (setting('units.weight') === 'kg') { $weight_value = round($weight_value * $kgstolbs); }
    return $weight_value;
  }
}

if (!function_exists('Dispo_Specs')) {
  // Prepare The Specs Selection Dropdown
  // Return JSON String
  function Dispo_Specs($specs) {
    $result = collect();

    if (filled($specs->airframe_id)) { $result->put('airframe_id', $specs->airframe_id); }
    if (filled($specs->icao)) { $result->put('icao', $specs->icao); }
    if (filled($specs->name)) { $result->put('name', $specs->name); }
    if (filled($specs->engines)) { $result->put('engines', $specs->engines); }
    if (filled($specs->dow)) { $result->put('oew', Dispo_SimBriefWeight($specs->dow)); }
    if (filled($specs->mzfw)) { $result->put('mzfw', Dispo_SimBriefWeight($specs->mzfw)); }
    if (filled($specs->mtow)) { $result->put('mtow', Dispo_SimBriefWeight($specs->mtow)); }
    if (filled($specs->mlw)) { $result->put('mlw', Dispo_SimBriefWeight($specs->mlw)); }
    if (filled($specs->mfuel)) { $result->put('maxfuel', Dispo_SimBriefWeight($specs->mfuel)); }
    if (filled($specs->mpax)) { $result->put('maxpax', $specs->mpax); }
    if (filled($specs->cat) && filled($specs->equip) && filled($specs->transponder)) {
      $result->put('cat', $specs->cat);
      $result->put('equip', $specs->equip);
      $result->put('transponder', $specs->transponder);
    }
    if (filled($specs->pbn)) { $result->put('pbn', $specs->pbn); }
    if (filled($specs->fuelfactor)) { $result->put('fuelfactor', $specs->fuelfactor); }
    if (filled($specs->cruiselevel)) { $result->put('cruiseoffset', $specs->cruiselevel); }
    if (filled($specs->paxwgt) && filled($specs->bagwgt)) {
      $result->put('paxw', $specs->paxwgt);
      $result->put('bagw', $specs->bagwgt);
      $result->put('paxwgt', Dispo_SimBriefPaxWeight($specs->paxwgt + $specs->bagwgt));
    }
    if (filled(Theme::getSetting('sb_rvr'))) { $sb_rvr = Theme::getSetting('sb_rvr');} else { $sb_rvr = '500';}
    if (filled(Theme::getSetting('sb_rmk'))) { $sb_rmk = Theme::getSetting('sb_rmk');} else { $sb_rmk = strtoupper(config('app.name'));}
    $result->put('extrarmk', 'RVR/'.$sb_rvr.' RMK/TCAS '.$sb_rmk);

    $acdata = json_encode($result);
    return $acdata;
  }
}

if (!function_exists('Dispo_CheckWeights')) {
  // Check Weights of a Pirep According to Specs
  // Return formatted string (with html tags)
  function Dispo_CheckWeights($pirepid, $slug) {
    if (stripos($slug, '-weight') !== false)
    {
      if ($slug === 'ramp-weight') { $specselect = 'mrw';}
      elseif ($slug === 'takeoff-weight') { $specselect = 'mtow';}
      elseif ($slug === 'landing-weight') { $specselect = 'mlw';}

      if ($pirepid && $slug) {
        $pirepwgt = DB::table('pirep_field_values')->select('id', 'value')->where(['pirep_id' => $pirepid, 'slug' => $slug])->first();
        $pirepac = DB::table('pirep_field_values')->select('id', 'value')->where(['pirep_id' => $pirepid, 'slug' => 'aircraft'])->first();
      }

      if (isset($pirepac) && isset($specselect)) {
        $spec_pirep = Pirep::with('aircraft')->select('id', 'aircraft_id')->where('id', $pirepid)->first();
        $specs = Dispo_GetAcSpecs($spec_pirep->aircraft);
        foreach ($specs as $spec) {
          if (filled($spec->stitle) && stripos($pirepac->value, $spec->stitle) !== false) { $specwgt = $spec->$specselect; }
        }
      }

      if (isset($pirepwgt) && setting('units.weight') === 'kg') {
        $pirepwgt->value = round(floatval($pirepwgt->value) / 2.20462262185, 2) ;
      }

      if (isset($pirepwgt) && isset($specwgt) && floatval($pirepwgt->value) > $specwgt) {
        return '<span class="badge badge-danger ml-1 mr-1" title="Max: '.number_format($specwgt).' '.setting('units.weight').'">OVERWEIGHT !</span>';
      }
    }
  }
}

if (!function_exists('Dispo_Flaps')) {
  // Check Flap and Gear Speeds
  // Return formatted string (with html tags)
  function Dispo_Flaps($aircraft_icao, $log_value) {
    // Check if this is a flap or gear related log entry
    $result = $log_value;
    $check_type = null;
    if (stripos($log_value, 'Gear Up') !== false || stripos($log_value, 'Gear Down') !== false) {
      $check_type = 'GEAR';
    } elseif (stripos($log_value, 'Flaps set to') !== false) {
      $check_type = 'FLAP';
    } elseif (stripos($log_value, 'Landing Speed') !== false) {
      $check_type = 'TIRE';
    }

    if (isset($check_type)) {
      $first_seperator = strpos($log_value, ',');
      $last_seperator = strrpos($log_value, ',');
      $search_array = array('kts', ',');
      $ops_info = substr($log_value, $first_seperator);
      $ops_speed = trim(str_replace($search_array,'',substr($log_value, $last_seperator)));
    }

    // Gear Checks
    // Example: Gear Down, 3974ft, 198 kts
    if ($check_type === 'GEAR') {
      $ops_name = substr($log_value, 0, $first_seperator);
      // Check the operation type and get gear speed from DB
      if ($ops_name === 'Gear Up') { $ops_type = 'gear_retract'; } else { $ops_type = 'gear_extend'; }
      $gear = Disposable_Tech::where(['icao' => $aircraft_icao, 'active' => 1])->select($ops_type.' as speed')->first();
      // Build new result and add overspeed warning if necessary
      if (isset($gear) && $ops_speed > 0 && $gear->speed > 0 && $ops_speed > $gear->speed) {
        $warning_badge = "<span class='badge badge-danger ml-1 mr-1' title='Max: ".$gear->speed." kts'>GEAR OVERSPEED !</span>";
        $result = $log_value.' '.$warning_badge;
      }
    }

    // Flap Checks
    // Example: Flaps set to CONF 1+F, 3994ft, 234 kts
    if ($check_type === 'FLAP') {
      $flap_detent = str_replace('Flaps set to ','',substr($log_value, 0, $first_seperator));
      // Get Flap Speed Limit From DB
      $flaps = Disposable_Tech::where(['icao' => $aircraft_icao, 'active' => 1])->first();
      if (isset($flaps)) { $maxspeed = $flaps->flapspeeds()->get($flap_detent); }
      // Build new result and add overspeed warning if necessary
      $result = 'Flaps '.$flap_detent.$ops_info;
      if (isset($maxspeed) && $maxspeed > 0 && $ops_speed > 0 && $ops_speed > $maxspeed) {
        $warning_badge = "<span class='badge badge-danger ml-1 mr-1' title='Max: ".$maxspeed." kts'>FLAP OVERSPEED !</span>";
        $result = $result.' '.$warning_badge;
      }
    }
    return $result;
  }
}

if (!function_exists('Dispo_Flap')) {
  // Get Flap Setting name from DB for Pirep Details
  function Dispo_Flap($aircraft_icao, $field_value) {
    $result = $field_value;
    return $result;
  }
}
*/