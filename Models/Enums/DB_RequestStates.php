<?php

namespace Modules\DisposableBasic\Models\Enums;

use App\Contracts\Enum;

class DB_RequestStates extends Enum
{
    public const WAITING = 0;
    public const REJECTED = 1;
    public const COMPLETED = 2;
    public const BYFUNDS = 3;
    public const BYPIREP = 4;
    public const BYSTAFF = 5;

    public static array $labels = [
        self::WAITING   => 'Waiting',
        self::REJECTED  => 'Rejected',
        self::COMPLETED => 'Completed Automatically',
        self::BYFUNDS   => 'Completed By Funds',
        self::BYPIREP   => 'Completed By PIREP',
        self::BYSTAFF   => 'Completed By Staff',
    ];
}
