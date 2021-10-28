<?php

use \App\Models\Enums\AircraftState;
use \App\Models\Enums\AircraftStatus;
use \App\Models\Enums\PirepState;
use \App\Models\Enums\UserState;
use \Nwidart\Modules\Facades\Module;

// Check phpVMS Module
// Return boolean
if (!function_exists('DB_CheckModule')) {
    function DB_CheckModule($module_name)
    {
        $phpvms_module = Module::find($module_name);
        return isset($phpvms_module) ? $phpvms_module->isEnabled() : false;
    }
}

// Convert Distance
// Return string
if (!function_exists('DB_ConvertDistance')) {
    function DB_ConvertDistance($value = 0, $target_unit = null)
    {
        if ($value == 0) {
            return null;
        }
        $target_unit = isset($target_unit) ? $target_unit : setting('units.distance');

        if ($target_unit === 'km') {
            $value = $value * 1.852;
        } elseif ($target_unit === 'mi') {
            $value = $value * 1.15078;
        }
        $value = number_format($value) . ' ' . $target_unit;

        return $value;
    }
}

// Convert Minutes
// Return string
if (!function_exists('DB_ConvertMinutes')) {
    function DB_ConvertMinutes($minutes = 0, $format = '%02d:%02d')
    {
        $minutes = intval($minutes);

        if ($minutes < 1) {
            return null;
        }
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
        if ($value == 0) {
            return null;
        }
        $target_unit = isset($target_unit) ? $target_unit : setting('units.weight');

        if ($target_unit === 'kg') {
            $value = $value / 2.20462262185;
        }
        $value = number_format($value) . ' ' . $target_unit;

        return $value;
    }
}

// Fuel Cost Converter
// Return string
if (!function_exists('DB_FuelCost')) {
    function DB_FuelCost($cost = 0, $unit = null, $currency = null)
    {
        if ($cost == 0) {
            return null;
        }
        $unit = isset($unit) ? $unit : setting('units.fuel');
        $currency = isset($currency) ? $currency : setting('units.currency');

        if ($unit === 'kg') {
            $cost = $cost / 2.20462262185;
        }
        $cost = number_format($cost, 3) . ' ' . ucfirst($currency) . '/' . ucfirst($unit);

        return $cost;
    }
}

// Aircraft Status Badge
// Return string (with html tags)
if (!function_exists('DB_AircraftStatus')) {
    function DB_AircraftStatus($aircraft, $type = 'badge')
    {
        $color = 'primary';
        $status = $aircraft->status;

        if ($status === AircraftStatus::ACTIVE) {
            $color = 'success';
        } elseif ($status === AircraftStatus::MAINTENANCE) {
            $color = 'info';
        } elseif ($status === AircraftStatus::STORED || $status === AircraftStatus::RETIRED) {
            $color = 'warning';
        } elseif ($status === AircraftStatus::SCRAPPED || $status === AircraftStatus::WRITTEN_OFF) {
            $color = 'danger';
        }

        if ($type === 'bg') {
            $result = 'class="bg-' . $color . '"';
        } elseif ($type === 'row') {
            $result = 'class="table-' . $color . '"';
        } else {
            $result = '<span class="badge bg-' . $color . ' text-black">' . AircraftStatus::label($status) . '</span>';
        }

        return $result;
    }
}

// Aircraft State
// Return mixed
if (!function_exists('DB_AircraftState')) {
    function DB_AircraftState($aircraft, $type = 'badge')
    {
        $color = 'primary';
        $state = $aircraft->state;

        if ($state === AircraftState::PARKED) {
            $color = 'success';
        } elseif ($state === AircraftState::IN_USE) {
            $color = 'info';
        } elseif ($state === AircraftState::IN_AIR) {
            $color = 'warning';
        }

        if ($type === 'bg') {
            $result = 'class="bg-' . $color . '"';
        } elseif ($type === 'row') {
            $result = 'class="table-' . $color . '"';
        } else {
            $result = '<span class="badge bg-' . $color . ' text-black">' . AircraftState::label($state) . '</span>';
        }

        return $result;
    }
}

// Pirep State
// Return mixed
if (!function_exists('DB_PirepState')) {
    function DB_PirepState($pirep, $type = 'badge')
    {
        $color = 'primary';
        $state = $pirep->state;

        if ($state === PirepState::IN_PROGRESS || $state === PirepState::DRAFT) {
            $color = 'info';
        } elseif ($state === PirepState::PENDING) {
            $color = 'secondary';
        } elseif ($state === PirepState::ACCEPTED) {
            $color = 'success';
        } elseif ($state === PirepState::CANCELLED || $state === PirepState::DELETED || $state === PirepState::REJECTED) {
            $color = 'danger';
        } elseif ($state === PirepState::PAUSED) {
            $color = 'warning';
        }

        if ($type === 'bg') {
            $result = 'class="bg-' . $color . '"';
        } elseif ($type === 'row') {
            $result = 'class="table-' . $color . '"';
        } else {
            $result = '<span class="badge bg-' . $color . ' text-black">' . PirepState::label($state) . '</span>';
        }

        return $result;
    }
}

// User State
// Return mixed
if (!function_exists('DB_UserState')) {
    function DB_UserState($user, $type = 'badge')
    {
        $color = 'primary';
        $state = $user->state;

        if ($state === UserState::PENDING) {
            $color = 'secondary';
        } elseif ($state === UserState::ACTIVE) {
            $color = 'success';
        } elseif ($state === UserState::REJECTED || $state === UserState::SUSPENDED || $state === UserState::DELETED) {
            $color = 'danger';
        } elseif ($state === UserState::ON_LEAVE) {
            $color = 'warning';
        }

        if ($type === 'bg') {
            $result = 'class="bg-' . $color . '"';
        } elseif ($type === 'row') {
            $result = 'class="table-' . $color . '"';
        } else {
            $result = '<span class="badge bg-' . $color . ' text-light">' . UserState::label($state) . '</span>';
        }

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

        if (!$setting && !$default_value) {
            $result = false;
        } elseif (!$setting && $default_value) {
            $result = $default_value;
        } elseif (!$setting->value) {
            $result = $default_value;
        } elseif ($setting->value === 'false') {
            $result = false;
        } elseif ($setting->value === 'true') {
            $result = true;
        } else {
            $result = $setting->value;
        }
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
