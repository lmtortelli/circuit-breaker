<?php

namespace Tortelli\CircuitBreaker;

use Illuminate\Support\ServiceProvider;
use Tortelli\CircuitBreaker\Interfaces\ICircuitBreaker;

class CircuitBreakerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(ICircuitBreaker::class, CircuitBreaker::class);
    }
}
