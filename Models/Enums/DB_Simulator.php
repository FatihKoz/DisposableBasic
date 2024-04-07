<?php

namespace Modules\DisposableBasic\Models\Enums;

use App\Contracts\Enum;

class DB_Simulator extends Enum
{
    public const FS9 = 1;
    public const FSX = 2;
    public const P3D = 3;
    public const XP = 4;
    public const MSFS = 5;
    public const OTHER = 6;

    public static array $labels = [
        self::FS9   => 'Ms Flight Simulator 2004',
        self::FSX   => 'Ms Flight Simulator X',
        self::P3D   => 'Prepar 3D',
        self::XP    => 'X-Plane',
        self::MSFS  => 'Microsoft Flight Simulator',
        self::OTHER => 'Other Simulators',
    ];
}
