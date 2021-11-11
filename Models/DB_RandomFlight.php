<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\User;

class DB_RandomFlight extends Model
{
    public $table = 'disposable_random_flights';

    protected $fillable = [
        'user_id',
        'airport_id',
        'flight_id',
        'pirep_id',
        'assign_date',
    ];

    // Validation
    public static $rules = [
        'user_id'     => 'nullable',
        'airport_id'  => 'nullable',
        'flight_id'   => 'nullable',
        'pirep_id'    => 'nullable',
        'assign_date' => 'nullable',
    ];

    // Completed Attribute
    public function getCompletedAttribute()
    {
        $pirep = Pirep::where([
            'user_id'   => $this->user_id,
            'flight_id' => $this->flight_id,
            'state'     => 2,
        ])->whereDate('submitted_at', $this->assign_date)->first();

        return isset($pirep) ? true : false;
    }

    // Relationships
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function flight()
    {
        return $this->hasOne(Flight::class, 'id', 'flight_id');
    }

    public function pirep()
    {
        $where = ['user_id' => $this->user_id, 'state' => 2];
        return $this->hasOne(Pirep::class, 'flight_id', 'flight_id')->where($where)->whereDate('submitted_at', $this->assign_date);
    }
}
