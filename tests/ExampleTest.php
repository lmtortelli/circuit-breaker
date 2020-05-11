<?php

namespace Tortelli\CircuitBreaker\Tests;

use Orchestra\Testbench\TestCase;
use Tortelli\CircuitBreaker\CircuitBreakerServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [CircuitBreakerServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
