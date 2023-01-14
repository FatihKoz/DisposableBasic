<?php

use App\Models\User;
use App\Models\UserField;
use App\Models\UserFieldValue;
use App\Models\Enums\AircraftState;
use App\Models\Enums\AircraftStatus;
use App\Models\Enums\PirepState;
use App\Models\Enums\UserState;
use Nwidart\Modules\Facades\Module;

// Aircraft State
// Return mixed
if (!function_exists('DB_AircraftState')) {
    function DB_AircraftState($aircraft, $type = 'badge')
    {
        $color = 'primary';
        $state = $aircraft->state;
        $title = null;

        if ($state === AircraftState::PARKED) {
            $color = 'success';
        } elseif ($state === AircraftState::IN_USE) {
            $color = 'info';
        } elseif ($state === AircraftState::IN_AIR) {
            $color = 'warning';
        }

        if ($aircraft->simbriefs_count > 0) {
            $color = 'primary';
            $title = 'Booked with SimBrief OFP';
        }

        if ($type === 'bg') {
            $result = 'class="bg-' . $color . '"';
        } elseif ($type === 'row') {
            $result = 'class="table-' . $color . '"';
        } else {
            $result = '<span class="badge bg-' . $color . ' text-black" title="' . $title . '">' . AircraftState::label($state) . '</span>';
        }

        return $result;
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

// Check Duplicate Custom Field
// Return boolean
if (!function_exists('DB_CheckDuplicateCustom')) {
    function DB_CheckDuplicateCustom($field)
    {
        // Check the value of the same custom profile field for duplicates
        // Can be used to check IVAO, VATSIM IDs to be unique
        $where = [];
        $where['user_field_id'] = $field->user_field_id;
        $where['value'] = $field->value;

        if (is_null($field->value)) {
            return false;
        }

        $check = UserFieldValue::where($where)->count();

        return ($check > 1) ? true : false;
    }
}

// Check phpVMS Module
// Return boolean
if (!function_exists('DB_CheckModule')) {
    function DB_CheckModule($module_name)
    {
        $phpvms_module = Module::find($module_name);
        return isset($phpvms_module) ? $phpvms_module->isEnabled() : false;
    }
}

if (!function_exists('DB_CheckPilotIdent')) {
    function DB_CheckPilotIdent($field, $user)
    {
        // This is usefull for something like "Preferred Callsign" field
        // ( which a new pilot fills out during registration )
        // It will check the value agains assigned Pilot IDs (idents)
        $where = [];
        $where['pilot_id'] = intval($field->value);
        $where[] = ['id', '!=', $user->id];

        $check = User::where($where)->count();

        return ($check != 0) ? true : false;
    }
}

// Convert Distance
// Return string
if (!function_exists('DB_ConvertDistance')) {
    function DB_ConvertDistance($value = 0, $target_unit = null)
    {
        $target_unit = isset($target_unit) ? $target_unit : setting('units.distance');

        if (!$value[$target_unit] > 0) {
            return null;
        }

        $value = number_format($value[$target_unit]) . ' ' . $target_unit;

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
    function DB_ConvertWeight($value, $target_unit = null)
    {
        $target_unit = isset($target_unit) ? $target_unit : setting('units.weight');

        if (!$value[$target_unit] > 0) {
            return null;
        }

        $value = number_format($value[$target_unit]) . ' ' . $target_unit;

        return $value;
    }
}

// Format Flight STA and STD Times (from 1200 to 12:30)
// Return string
if (!function_exists('DB_FormatScheduleTime')) {
    function DB_FormatScheduleTime($time = null)
    {
        if (is_null($time) || !is_numeric($time) || strlen($time) === 5) {
            return $time;
        }

        if (!str_contains($time, ':') && strlen($time) === 4) {
            $time = substr($time, 0, 2) . ':' . substr($time, 2, 2);
        }

        return $time;
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
            $cost = $cost / 0.45359237;
        }
        $cost = number_format($cost, 3) . ' ' . ucfirst($currency) . '/' . ucfirst($unit);

        return $cost;
    }
}

// Get Used Online Network Member ID's
// Return array
if (!function_exists('DB_GetNetworkMembers')) {
    function DB_GetNetworkMembers($field_name)
    {
        $result = [];
        $field = UserField::where('name', $field_name)->first();
        if (isset($field)) {
            $result = UserFieldValue::whereNotNull('value')->where('user_field_id', $field->id)->orderBy('value')->pluck('value')->toArray();
        }

        return $result;
    }
}

// Get Used Pilot ID's (Idents, Callsigns)
// Return array
if (!function_exists('DB_GetPilotIdents')) {
    function DB_GetPilotIdents()
    {
        return User::select('pilot_id')->orderBy('pilot_id')->pluck('pilot_id')->toArray();
    }
}

// Get Required Units
// Return array
if (!function_exists('DB_GetUnits')) {
    function DB_GetUnits($type = null)
    {
        $units = [];
        $units['currency'] = setting('units.currency');
        $units['distance'] = setting('units.distance');
        $units['fuel'] = setting('units.fuel');
        $units['weight'] = setting('units.weight');

        if ($type === 'full') {
            $units['volume'] = setting('units.volume');
            $units['altitude'] = setting('units.altitude');
            $units['speed'] = setting('units.speed');
        }

        return $units;
    }
}

// Network Presence Display with Calculated Result
// Return html formatted string
if (!function_exists('DB_NetworkPresence')) {
    function DB_NetworkPresence($pirep, $type = 'button')
    {
        $network_online = optional($pirep->fields->firstWhere('slug', 'network-online'))->value;
        $network_presence = optional($pirep->fields->firstWhere('slug', 'network-presence-check'))->value;

        // Network Name
        $network_name = $network_online;

        // Title
        if (isset($network_presence) && $network_presence == 0) {
            $button_title = 'No Network Presence';
            $network_name = 'OFFLINE';
        } elseif (isset($network_presence) && $network_presence > 0) {
            $button_title = 'Network Presence ' . $network_presence . '%';
        } else {
            $button_title = 'Network Presence Not Calculated';
        }

        // Color by Network
        if ($network_name == 'OFFLINE') {
            $button_color = 'bg-secondary';
        } elseif ($network_name == 'VATSIM') {
            $button_color = 'bg-success';
        } elseif ($network_name == 'IVAO') {
            $button_color = 'bg-primary';
        } else {
            $button_color = 'bg-info';
        }

        if (filled($network_online) && $network_name != 'NONE' && $type == 'badge') {
            $result = '<span class="badge badge-sm ' . $button_color . ' mx-1 px-1 text-black" title="' . $button_title . '">' . $network_name . '</span>';
        } elseif (filled($network_online) && $network_name != 'NONE' && $type == 'button') {
            $result = '<span class="btn btn-sm ' . $button_color . ' m-0 mx-1 p-0 px-1 text-black" title="' . $button_title . '">' . $network_name . '</span>';
        } else {
            $result = null;
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

// Read Json File
// Return object
if (!function_exists('DB_ReadJson')) {
    function DB_ReadJson($file = null)
    {
        if (!is_file($file)) {
            return null;
        }

        $string = file_get_contents($file);
        $result = json_decode($string);

        return (json_last_error() === 0) ? $result : null;
    }
}

// Check if the user has any SAP Reports
// Return boolean
if (!function_exists('DB_SapReports')) {
    function DB_SapReports($user_id = null)
    {
        $count = DB::table('disposable_sap_reports')->where('user_id', $user_id)->count();

        return ($count > 0) ? true : false;
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

// Get Total User Count
// Return integer
if (!function_exists('DB_UserCount')) {
    function DB_UserCount()
    {
        return User::count();
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
        } elseif ($type === 'bg_add') {
            $result = 'bg-' . $color;
        } elseif ($type === 'row') {
            $result = 'class="table-' . $color . '"';
        } else {
            $result = '<span class="badge bg-' . $color . ' text-black">' . UserState::label($state) . '</span>';
        }

        return $result;
    }
}

// X-Plane SDK Data to be used by SAP Reports
// Return array
if (!function_exists('DB_XPlane_SDK')) {
    function DB_XPlane_SDK($type = null)
    {
        // X-Plane Runway Surface Types
        $runway_surface = [
            '0' => 'Not Defined',
            '1' => 'Asphalt',
            '2' => 'Concrete',
            '3' => 'Grass',
            '4' => 'Dirt',
            '5' => 'Gravel',
            '12' => 'Dry Lakebed',
            '13' => 'Water',
            '14' => 'Snow/Ice',
            '15' => 'Custom',
        ];
        // X-Plane Runway Markings
        $runway_marking = [
            '0' => 'None',
            '1' => 'Visual Markings',
            '2' => 'Non-precision Approach Markings',
            '3' => 'Precision Approach Markings',
            '4' => 'Non-precision Approach Markings II',
            '5' => 'Precision Approach Markings II',
        ];
        // X-Plane Approach Light Types
        $approach_lights = [
            '0' => 'None',
            '1' => 'ALSF-I',
            '2' => 'ALSF-II',
            '3' => 'Calvert',
            '4' => 'Calvert II',
            '5' => 'SSALR',
            '6' => 'SSALF',
            '7' => 'SALS',
            '8' => 'MALSR',
            '9' => 'MALSF',
            '10' => 'MALS',
            '11' => 'ODALS',
            '12' => 'RAIL',
        ];

        $result = [];

        if ($type === 'surface') {
            $result = $runway_surface;
        } elseif ($type === 'markings') {
            $result = $runway_marking;
        } elseif ($type === 'applights') {
            $result = $approach_lights;
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
