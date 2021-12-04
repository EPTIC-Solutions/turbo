<?php

namespace Eptic\Turbo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Eptic\Turbo\Turbo
 */
class Turbo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'turbo';
    }
}
