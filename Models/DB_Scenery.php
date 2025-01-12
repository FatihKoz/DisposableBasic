<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Kyslik\ColumnSortable\Sortable;

class DB_Scenery extends Model
{
    use Sortable;

    public $table = 'disposable_sceneries';

    protected $fillable = [
        'user_id',
        'airport_id',
        'region',
        'simulator',
        'notes',
    ];

    public static $rules = [
        'user_id'    => 'required',
        'airport_id' => 'required|max:5',
        'region'     => 'nullable',
        'simulator'  => 'nullable',
        'notes'      => 'nullable',
    ];

    public $sortable = [
        'user_id',
        'airport_id',
        'region',
        'simulator',
        'notes',
    ];

    // Relationship
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function airport(): HasOne
    {
        return $this->hasOne(Airport::class, 'id', 'airport_id');
    }

    public function departures(): HasMany
    {
        return $this->hasMany(Flight::class, 'dpt_airport_id', 'airport_id');
    }

    public function arrivals(): HasMany
    {
        return $this->hasMany(Flight::class, 'arr_airport_id', 'airport_id');
    }
}
