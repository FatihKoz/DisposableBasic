<?php
use \App\Models\Enums\AircraftState;
use \App\Models\Enums\AircraftStatus;
use \App\Models\Enums\PirepState;
use \App\Models\Enums\UserState;
use \App\Models\SimBrief;
use \Nwidart\Modules\Facades\Module;

// Check phpVMS Module
// Return boolean
if (!function_exists('DB_CheckModule')) {
  function DB_CheckModule($module_name)
  {
    $phpvms_module = Module::find($module_name);
    $is_enabled = ($phpvms_module) ? $phpvms_module->isEnabled() : false;

    return $is_enabled;
  }
}

// Convert Distance
// Return string
if (!function_exists('DB_ConvertDistance')) {
  function DB_ConvertDistance($value, $target_unit = null)
  {
    $target_unit = isset($target_unit) ? $target_unit : setting('units.distance');

    if ($target_unit === 'km') { $value = $value * 1.852; }
    elseif ($target_unit === 'mi') { $value = $value * 1.15078; }

    $value = number_format($value).' '.$target_unit;

    return $value;
  }
}

// Convert Minutes
// Return string
if (!function_exists('DB_ConvertMinutes')) {
  function DB_ConvertMinutes($minutes, $format = '%02d:%02d')
  {
    $minutes = intval($minutes);
    if ($minutes < 1 || blank($minutes)) { return null; }

    $hours = floor($minutes / 60);
    $minutes = ($minutes % 60);

    return sprintf($format, $hours, $minutes);
  }
}

// Convert Weight from LBS to KGS
// Return string
if (!function_exists('DB_ConvertWeight')) {
  function DB_ConvertWeight($value = 0, $target_unit = null)
  {
    if (blank($value)) { return null; }
    $target_unit = isset($target_unit) ? $target_unit : setting('units.weight');

    if ($target_unit === 'kg') { $value = $value / 2.20462262185; }
    $value = number_format($value).' '.$target_unit;

    return $value;
  }
}

// Fuel Cost Converter
// Return string
if (!function_exists('DB_FuelCost')) {
  function DB_FuelCost($cost, $unit = null, $currency = null)
  {
    $unit = isset($unit) ? $unit : setting('units.fuel');
    $currency = isset($currency) ? $currency : setting('units.currency');

    if ($unit === 'kg') { $cost = $cost / 2.20462262185; }
    $cost = number_format($cost, 3).' '.$currency.'/'.$unit;

    return $cost;
  }
}

// Aircraft Status Badge
// Return string (with html tags)
if (!function_exists('DB_AircraftStatus')) {
  function DB_AircraftStatus($aircraft)
  {
    $color = 'primary';
    $status = $aircraft->status;

    if ($status === 'A') { $color = 'success'; }
    elseif ($status === 'M') { $color = 'info'; }
    elseif ($status === 'S' || $status === 'R') { $color = 'warning'; }
    elseif ($status === 'C' || $status === 'W') { $color = 'danger'; }

    $result = '<span class="badge bg-'.$color.' text-black">'.AircraftStatus::label($status).'</span>';

    return $result;
  }
}

// Prepare Aircraft State Badge
// Return string (with html tags)
if (!function_exists('DB_AircraftState')) {
  function DB_AircraftState($aircraft)
  {
    $color = 'primary';

    $state = $aircraft->state;
    $aircraft_id = $aircraft->id;

    if ($state === 0) { $color = 'success'; }
    elseif ($state === 1) { $color = 'info'; }
    elseif ($state === 2) { $color = 'warning'; }

    $result = '<span class="badge bg-'.$color.' text-black">'.AircraftState::label($state).'</span>';

    // See if this aircraft is being used by some user's active simbrief ofp
    if ($state === 0 && isset($aircraft_id) && setting('simbrief.block_aircraft')) {
      $simbrief_book = SimBrief::with('user')->select('id')->where('aircraft_id', $aircraft_id)->whereNotNull('flight_id')->whereNull('pirep_id')->orderby('created_at', 'desc')->first();
      if (isset($simbrief_book)) {
        $result = '<span class="badge bg-secondary text-black" title="Booked By: '.$simbrief_book->user->name_private.'">Booked</span>';
      }
    }

    return $result;
  }
}

// Prepare Pirep State Badge
// Return string (with html tags)
if (!function_exists('DB_PirepState')) {
  function DB_PirepState($pirep)
  {
    $color = 'primary';
    $state = $pirep->state;

    if ($state === 0 || $state === 5) { $color = 'info'; }
    elseif ($state === 1) { $color = 'secondary'; }
    elseif ($state === 2) { $color = 'success'; }
    elseif ($state === 3 || $state === 4 || $state === 6) { $color = 'danger'; }
    elseif ($state === 7) { $color = 'warning'; }

    $result = '<span class="badge bg-'.$color.' text-black">'.PirepState::label($state).'</span>';

    return $result;
  }
}

// Prepare User State Badge
// Return string (with html tags)
if (!function_exists('DB_UserState')) {
  function DB_UserState($user)
  {
    $color = 'primary';
    $state = $user->state;

    if ($state === 0) { $color = 'secondary'; }
    elseif ($state === 1) { $color = 'success'; }
    elseif ($state === 2 || $state === 4 || $state === 5) { $color = 'danger'; }
    elseif ($state === 3) { $color = 'warning'; }

    $result = '<span class="badge bg-'.$color.' text-black">'.UserState::label($state).'</span>';

    return $result;
  }
}

// Check Disposable Module Setting
// Return mixed, either boolean or the value itself as string
// If setting is not found, return either false or provided default
if (!function_exists('DB_Setting')) {
  function DB_Setting($key, $default_value = null)
  {
    $setting = DB::table('disposable_settings')->select('key', 'value')->where('key', $key)->first();

    if (!$setting && !$default_value) { $result = false; }
    elseif (!$setting && $default_value) { $result = $default_value; }
    elseif (!$setting->value) { $result = $default_value; }
    elseif ($setting->value === 'false') { $result = false; }
    elseif ($setting->value === 'true') { $result = true; }
    else { $result = $setting->value; }

    return $result;
  }
}

// Array Unique Multi Dimensional
// Return array
if (!function_exists('DB_ArrayUnique_MD')) {
  function DB_ArrayUnique_MD($array, $key)
  {
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach ($array as $val) {
      if (!in_array($val[$key], $key_array)) {
        $key_array[$i] = $val[$key];
        $temp_array[$i] = $val;
      }
      $i++;
    }

    return $temp_array;
  }
}

// In Array Multi Dimensional
// Return boolean
if (!function_exists('DB_InArray_MD')) {
  function DB_InArray_MD($needle, $haystack, $strict = false)
  {
    foreach ($haystack as $item) {
      if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && DB_InArray_MD($needle, $item, $strict))) {

        return true;
      }
    }

    return false;
  }
}
