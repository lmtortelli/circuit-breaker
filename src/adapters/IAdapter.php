<?php

namespace Tortelli\CircuitBreaker\Adapters;

interface IAdapter
{

    public function isOpen(string $service) : bool;

    public function reachRateLimit(string $service) : bool;

    public function setOpenCircuit(string $service) : void;

    public function setHalfOpenCircuit(string $service) : void;

    public function isHalfOpen(string $service) : bool;

    public function incrementFailure(string $service) : bool;

    public function setSuccess(string $service) : bool;

}
