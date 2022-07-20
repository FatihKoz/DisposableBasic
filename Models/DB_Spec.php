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
        'selcal',
        'hexcode',
        'rmk',
        'rvr',
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
        'selcal'      => 'nullable',
        'hexcode'     => 'nullable',
        'rmk'         => 'nullable',
        'rvr'         => 'nullable',
        'active'      => 'nullable',
    ];

    // Attributes
    public function getSimbriefAttribute()
    {
        $sb = collect();

        if (filled($this->airframe_id)) {
            $sb->put('airframe_id', $this->airframe_id);
        }
        if (filled($this->icao)) {
            $sb->put('icao', $this->icao);
        }
        if (filled($this->name)) {
            $sb->put('name', $this->name);
        }
        if (filled($this->engines)) {
            $sb->put('engines', $this->engines);
        }
        if (filled($this->dow)) {
            $sb->put('oew', $this->dow);
        }
        if (filled($this->mzfw)) {
            $sb->put('mzfw', $this->mzfw);
        }
        if (filled($this->mtow)) {
            $sb->put('mtow', $this->mtow);
        }
        if (filled($this->mlw)) {
            $sb->put('mlw', $this->mlw);
        }
        if (filled($this->mfuel)) {
            $sb->put('maxfuel', $this->mfuel);
        }
        if (filled($this->mpax)) {
            $sb->put('maxpax', $this->mpax);
        }
        if (filled($this->cat) && filled($this->equip) && filled($this->transponder)) {
            $sb->put('cat', $this->cat);
            $sb->put('equip', $this->equip);
            $sb->put('transponder', $this->transponder);
        }
        if (filled($this->pbn)) {
            $sb->put('pbn', $this->pbn);
        }
        if (filled($this->fuelfactor)) {
            $sb->put('fuelfactor', $this->fuelfactor);
        }
        if (filled($this->cruiselevel)) {
            $sb->put('cruiseoffset', $this->cruiselevel);
        }
        if (filled($this->paxwgt)) {
            $sb->put('paxw', $this->paxwgt);
            $sb->put('paxwgt', $this->paxwgt);
        }
        if (filled($this->bagwgt)) {
            $sb->put('bagw', $this->bagwgt);
            $sb->put('bagwgt', $this->bagwgt);
        }
        if (filled($this->selcal)) {
            $sb->put('selcal', $this->selcal);
        }
        if (filled($this->hexcode)) {
            $sb->put('hexcode', $this->hexcode);
        }
        if (filled($this->rmk)) {
            $sb->put('rmk', $this->rmk);
        }
        if (filled($this->rvr)) {
            $sb->put('rvr', $this->rvr);
        }

        return json_encode($sb);
    }

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
