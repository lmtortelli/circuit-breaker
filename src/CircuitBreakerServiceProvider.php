<?php

namespace MelhorEnvio\CircuitBreaker;

use Illuminate\Support\ServiceProvider;
use MelhorEnvio\CircuitBreaker\Interfaces\ICircuitBreaker;

class CircuitBreakerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(ICircuitBreaker::class,CircuitBreaker::class);
    }
}
