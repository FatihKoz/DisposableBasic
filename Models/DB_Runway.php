<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;
use App\Models\Airport;

class DB_Runway extends Model
{
    public $table = 'disposable_runways';

    protected $fillable = [
        'airport_id',
        'runway_ident',
        'lat',
        'lon',
        'heading',
        'length',
        'ils_freq',
        'loc_course',
        'airac',
    ];

    // Validation rules
    public static $rules = [
        'airport_id'   => 'required|max:5',
        'runway_ident' => 'required',
        'lat'          => 'required',
        'lon'          => 'required',
        'heading'      => 'required',
        'length'       => 'required',
        'ils_freq'     => 'nullable',
        'loc_course'   => 'nullable',
        'airac'        => 'nullable',
    ];

    protected $appends = [
        'ident',
        'imperial',
        'metric',
    ];

    // Attributes
    public function GetIdentAttribute()
    {
        $runway_data = $this->runway_ident;

        if ($this->length) {
            $unit = (setting('units.distance') === 'km') ? 'm' : 'ft';
            $length = (setting('units.distance') === 'km') ? ltrim($this->length, '0') : round(intval($this->length) * 3.28084);
            $runway_data = $this->runway_ident . ' | ' . $length . $unit;
        }

        if ($this->ils_freq && $this->loc_course) {
            $runway_data = $runway_data . ' (' . $this->ils_freq . 'mhz ' . $this->loc_course . '&deg;)';
        }

        return $runway_data;
    }

    public function GetImperialAttribute()
    {
        return round(intval($this->length) * 3.28084);
    }

    public function GetMetricAttribute()
    {
        return intval($this->length);
    }

    // Relationships
    public function airport()
    {
        return $this->hasOne(Airport::class, 'id', 'airport_id');
    }
}
