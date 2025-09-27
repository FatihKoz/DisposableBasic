<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;
use App\Models\Airport;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Kyslik\ColumnSortable\Sortable;

class DB_Jumpseat extends Model
{
    use Sortable;

    public $table = 'disposable_jumpseats';

    protected $fillable = [
        'user_id',
        'curr_airport_id',
        'move_airport_id',
        'price',
        'reason',
        'status',
        'completed_by',
        'completed_at',
    ];

    public static $rules = [
        'user_id'         => 'required',
        'curr_airport_id' => 'required',
        'move_airport_id' => 'required|different:curr_airport_id',
        'price'           => 'nullable',
        'reason'          => 'nullable',
        'status'          => 'nullable',
        'completed_by'    => 'nullable',
        'completed_at'    => 'nullable',
    ];

    public $sortable = [
        'user_id',
        'curr_airport_id',
        'move_airport_id',
        'status',
        'completed_by',
        'completed_at',
    ];

    // Relationship
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function curr_airport(): HasOne
    {
        return $this->hasOne(Airport::class, 'id', 'curr_airport_id');
    }

    public function move_airport(): HasOne
    {
        return $this->hasOne(Airport::class, 'id', 'move_airport_id');
    }

    public function staff(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'completed_by');
    }
}
