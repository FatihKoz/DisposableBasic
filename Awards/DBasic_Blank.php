<?php

namespace Modules\DisposableBasic\Awards;

use App\Contracts\Award;

class DBasic_Blank extends Award
{
    public $name = 'Blank Award';
    public $param_description = 'Parameter is not needed at all but write something';

    public function check($parameter = null): bool
    {
        return false;
    }
}
