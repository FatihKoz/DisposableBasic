<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;

class DB_Discord extends Model
{
    public $table = 'disposable_discord';

    protected $fillable = [
        'server_id',
        'rawdata',
    ];

    // Validation rules
    public static $rules = [
        'server_id'  => 'nullable',
        'rawdata' => 'nullable',
    ];
}
