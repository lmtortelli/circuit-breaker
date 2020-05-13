<?php

namespace Tortelli\CircuitBreaker\Adapters;

use Tortelli\CircuitBreaker\CircuitBreaker;

class RedisAdapter implements IAdapter
{

    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * @var string
     */
    protected $redisNamespace;

    /**
     * @var array
     */
    protected $cachedService = [];

    /**
     * Set settings for start circuit service
     *
     * @param \Redis $redis
     * @param string $redisNamespace
     */
    public function __construct(\Redis $redis, string $redisNamespace)
    {
        $this->redis = $redis;
        $this->redisNamespace = $redisNamespace;
    }

    /**
     * @param string $service
     * @return bool|string
     */
    public function isOpen(string $service) : bool
    {
        return $this->redis->get($this->makeNamespace($service) . ':open');
    }

    /**
     * @param string $service
     * @return bool
     */
    public function reachRateLimit(string $service) : bool
    {
        $failures = $this->redis->get(
            $this->makeNamespace($service) . ':failures'
        );
        $failures;
        return $failures && $failures >= CircuitBreaker::getServiceSetting($service, 'failureRateThreshold');
    }

    /**
     *
     *
     * @param string $service
     * @return bool|string
     */
    public function isHalfOpen(string $service) : bool
    {
        return (bool) $this->redis->get($this->makeNamespace($service) . ':half_open');
    }

    /**
     * Increment a failure count in specific service
     *
     * @param string $service
     * @return bool
     */
    public function incrementFailure(string $service) : bool
    {
        $serviceFailures = self::makeNamespace($service) . ':failures';

        if (! $this->redis->get($serviceFailures)) {
            $this->redis->multi();
            $this->redis->incr($serviceFailures);
            $this->redis->expire($serviceFailures, CircuitBreaker::getServiceSetting($service, 'timeWindow'));
            return (bool) $this->redis->exec()[0] ?? false;
        }

        return (bool) $this->redis->incr($serviceFailures);
    }

    /**
     *
     *
     * @param string $service
     * @return bool
     */
    public function setSuccess(string $service) : bool
    {
        return (bool) $this->redis->del(
            $this->redis->keys(
                $this->makeNamespace($service) . ':*'
            )
        );
    }

    /**
     * Set a circuit's service like open
     *
     * @param string $service
     */
    public function setOpenCircuit(string $service) : void
    {
        $this->redis->set(
            $this->makeNamespace($service) . ':open',
            time(),
            CircuitBreaker::getServiceSetting($service, 'timeWindow')
        );
    }

    /**
     * Set a circuit's service like half open
     *
     * @param string $service
     */
    public function setHalfOpenCircuit(string $service) : void
    {
        $this->redis->set(
            $this->makeNamespace($service) . ':half_open',
            time(),
            CircuitBreaker::getServiceSetting($service, 'timeWindow')
            + CircuitBreaker::getServiceSetting($service, 'intervalToHalfOpen')
        );
    }

    /**
     * Create a namespace for avoid conflicts into Redis
     *
     * @param string $service
     * @return string
     */
    protected function makeNamespace(string $service)
    {
        if (isset($this->cachedService[$service])) {
            return $this->cachedService[$service];
        }

        return $this->cachedService[$service] = $this->redisNamespace . ':' . base64_encode($service);
    }

    /***
     * Set a service to be managed by Redis
     *
     * @param string $service
     */
    public function setService(string $service): void
    {
        $this->redis->set($this->makeNamespace($service),
        CircuitBreaker::getServiceSetting($service, 'timeWindow')
        + CircuitBreaker::getServiceSetting($service, 'intervalToHalfOpen'));
    }
}
