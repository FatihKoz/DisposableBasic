<?php

namespace Modules\DisposableBasic\Models\Enums;

use App\Contracts\Enum;

class DB_WorldRegion extends Enum
{
    public const AFRICA = 1;
    public const ASIA = 2;
    public const EUROPE = 3;
    public const NORTH_AMERICA = 4;
    public const SOUTH_AMERICA = 5;
    public const OCEANIA = 6;
    public const OTHER = 7;

    public static array $labels = [
        self::AFRICA        => 'Africa',
        self::ASIA          => 'Asia',
        self::EUROPE        => 'Europe',
        self::NORTH_AMERICA => 'North America',
        self::SOUTH_AMERICA => 'South America',
        self::OCEANIA       => 'Oceania',
        self::OTHER         => 'Other Regions',
    ];
}
