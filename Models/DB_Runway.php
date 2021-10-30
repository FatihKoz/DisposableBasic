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
        'lenght',
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
        'lenght'       => 'required',
        'ils_freq'     => 'nullable',
        'loc_course'   => 'nullable',
        'airac'        => 'nullable',
    ];

    // Attributes
    public function GetIdentAttribute()
    {
        $runway_data = $this->runway_ident;

        if ($this->lenght) {
            $unit = (setting('units.distance') === 'km') ? 'm' : 'ft';
            $runway_data = $this->runway_ident . ' | ' . ltrim($this->lenght, '0').$unit;
        }

        if ($this->ils_freq && $this->loc_course) {
            $runway_data = $runway_data . ' (' . $this->ils_freq . 'mhz ' . $this->loc_course . '&deg;)';
        }

        return $runway_data;
    }

    // Relationships
    public function airport()
    {
        return $this->hasOne(Airport::class, 'id', 'airport_id');
    }
}
