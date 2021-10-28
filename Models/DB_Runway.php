<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;
use App\Models\Airport;

class DB_Runway extends Model
{
    public $table = 'disposable_runways';

    // Attributes
    public function GetIdentAttribute()
    {
        $runway_data = $this->runway_ident;

        if ($this->lenght) {
            $runway_data = $this->runway_ident . ' : ' . $this->lenght;
        }

        if ($this->ils_freq && $this->loc_course) {
            $runway_data = $runway_data . ' > ' . $this->ils_freq . ' Mhz ' . $this->loc_course . '&deg;';
        }

        return $runway_data;
    }

    // Relationships
    public function airport()
    {
        return $this->hasOne(Airport::class, 'id', 'airport_id');
    }
}
