<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;
use App\Models\Pirep;
use App\Models\User;

class DB_StableApproach extends Model
{
    public $table = 'disposable_sap_reports';

    protected $fillable = [
        'sap_analysisID',
        'sap_userID',
        'user_id',
        'pirep_id',
        'is_stable',
        'raw_report',
    ];

    // Validation
    public static $rules = [
        'sap_analysisID' => 'required',
        'sap_userID'     => 'required',
        'user_id'        => 'required|numeric',
        'pirep_id'       => 'required',
        'is_stable'      => 'nullable|numeric',
        'raw_report'     => 'required',
    ];

    // Attributes
    public function getStableAttribute()
    {
        return ($this->is_stable == 1) ? true : false;
    }

    public function getReportAttribute() 
    {
        return filled($this->raw_report) ? json_decode($this->raw_report) : null;
    }

    public function getMessagesAttribute()
    {
        return (filled($this->report) && is_array($this->report->messages)) ? collect($this->report->messages) : null;
    }

    public function getTouchdownsAttribute()
    {
        return (filled($this->report) && is_array(optional($this->report->analysis)->touchdowns)) ? collect($this->report->analysis->touchdowns) : null;
    }

    // Relationships
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function pirep()
    {
        return $this->hasOne(Pirep::class, 'id', 'pirep_id');
    }
}
