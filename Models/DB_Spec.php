<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;
use App\Models\Aircraft;
use App\Models\Subfleet;

class DB_Spec extends Model
{
    public $table = 'disposable_specs';

    protected $fillable = [
        'icao_id',
        'aircraft_id',
        'subfleet_id',
        'airframe_id',
        'icao',
        'name',
        'engines',
        'bew',
        'dow',
        'mzfw',
        'mrw',
        'mtow',
        'mlw',
        'mrange',
        'mceiling',
        'mfuel',
        'mpax',
        'mspeed',
        'cspeed',
        'cat',
        'equip',
        'transponder',
        'pbn',
        'crew',
        'saircraft',
        'stitle',
        'fuelfactor',
        'cruiselevel',
        'paxwgt',
        'bagwgt',
        'active',
    ];

    // Validation rules
    public static $rules = [
        'icao_id'     => 'nullable',
        'aircraft_id' => 'nullable|numeric',
        'subfleet_id' => 'nullable|numeric',
        'airframe_id' => 'nullable',
        'icao'        => 'nullable|max:4',
        'name'        => 'nullable',
        'engines'     => 'nullable',
        'bew'         => 'nullable|numeric',
        'dow'         => 'nullable|numeric',
        'mzfw'        => 'nullable|numeric',
        'mrw'         => 'nullable|numeric',
        'mtow'        => 'nullable|numeric',
        'mlw'         => 'nullable|numeric',
        'mrange'      => 'nullable|numeric',
        'mceiling'    => 'nullable|numeric',
        'mfuel'       => 'nullable|numeric',
        'mpax'        => 'nullable|numeric',
        'mspeed'      => 'nullable|numeric',
        'cspeed'      => 'nullable|numeric',
        'cat'         => 'nullable',
        'equip'       => 'nullable',
        'transponder' => 'nullable',
        'pbn'         => 'nullable',
        'maxfuel'     => 'nullable|numeric',
        'maxpax'      => 'nullable|numeric',
        'crew'        => 'nullable|numeric',
        'saircraft'   => 'required|max:50',
        'stitle'      => 'nullable|max:30',
        'fuelfactor'  => 'nullable|max:3',
        'cruiselevel' => 'nullable|max:5',
        'paxwgt'      => 'nullable|numeric',
        'bagwgt'      => 'nullable|numeric',
        'active'      => 'nullable',
    ];

    // Relationships
    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class, 'aircraft_id', 'id');
    }

    public function subfleet()
    {
        return $this->belongsTo(Subfleet::class, 'subfleet_id', 'id');
    }
}
