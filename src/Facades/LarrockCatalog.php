<?php

namespace Larrock\ComponentCatalog\Facades;

use Illuminate\Support\Facades\Facade;

class LarrockCatalog extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'larrockcatalog';
    }

}