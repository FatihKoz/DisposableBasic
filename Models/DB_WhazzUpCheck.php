<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;

class DB_WhazzUpCheck extends Model
{
    public $table = 'disposable_whazzup_checks';

    protected $fillable = [
        'user_id',
        'pirep_id',
        'network',
        'is_online',
        'callsign',
    ];

    // Validation rules
    public static $rules = [
        'user_id'     => 'required',
        'pirep_id'    => 'required',
        'network'     => 'required',
        'is_online'   => 'required',
        'callsign'    => 'nullable',
    ];

    // Relationships
    public function pirep()
    {
        return $this->hasOne(Pirep::class, 'id', 'pirep_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
