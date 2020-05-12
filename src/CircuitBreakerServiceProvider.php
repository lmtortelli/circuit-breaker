<?php

namespace Tortelli\CircuitBreaker;

use Illuminate\Support\ServiceProvider;


class CircuitBreakerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('circuit-breaker', function () {
            return new CircuitBreaker();
        });

    }

}
