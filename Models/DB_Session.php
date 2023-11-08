<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;
use App\Models\User;

class DB_Session extends Model
{
    public $table = 'sessions';

    protected $casts = [
        'last_activity' => 'datetime:timestamp',
    ];

    // Relationship
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
