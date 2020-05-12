<?php

namespace Tortelli\CircuitBreaker\Facade;

use \Illuminate\Support\Facades\Facade;

class CircuitBreaker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'circuit-breaker';
    }
}
