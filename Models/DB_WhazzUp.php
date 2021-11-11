<?php

namespace Modules\DisposableBasic\Models;

use App\Contracts\Model;

class DB_WhazzUp extends Model
{
    public $table = 'disposable_whazzup';

    protected $fillable = [
        'network',
        'pilots',
        'atcos',
        'observers',
        'servers',
        'voiceservers',
        'rawdata',
    ];

    // Validation rules
    public static $rules = [
        'networkd'    => 'nullable',
        'pilots'      => 'nullable',
        'atcos'       => 'nullable',
        'observers'   => 'nullable',
        'servers'     => 'nullable',
        'voiceserver' => 'nullable',
        'rawdata'     => 'nullable',
    ];
}
