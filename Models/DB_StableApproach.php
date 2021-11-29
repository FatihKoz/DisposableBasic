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
