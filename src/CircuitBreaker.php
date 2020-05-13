<?php

namespace MelhorEnvio\CircuitBreaker;

use Tortelli\CircuitBreaker\Adapters\IAdapter;
use Tortelli\CircuitBreaker\Interfaces\ICircuitBreaker;

class CircuitBreaker implements ICircuitBreaker
{

    /**
     * @var IAdapter
     */
    protected static $adapter;


    /**
     * @var array
     */
    protected static $servicesSettings;

    /**
     * @var array
     */
    protected static $defaultSettings = [
        'timeWindow' => 60,
        'failureRateThreshold' => 50,
        'intervalToHalfOpen' => 30,
    ];

    /**
     * Set a Adapter for Circuit Breaker management
     *
     * @param IAdapter $adapter
     */
    public static function setAdapter(IAdapter $adapter): void
    {
        self::$adapter = $adapter;
    }

    /**
     * Retrieve the instance of Adapter
     *
     * @return AdapterInterface
     */
    public static function getAdapter(): IAdapter
    {
        return self::$adapter;
    }

    /**
     * Set a service to be managed by Circuit Breaker
     *
     * @param string $service
     * @param array $settings
     */
    public static function setService(string $service, array $settings) : void
    {
        self::$adapter->setService($service);

        self::setServiceSettings($service,$settings);
    }

    /**
     * Get setting for a service, if not set, get from default settings
     *
     * @param string $service
     * @param string $setting
     * @return mixed
     */
    public static function getServiceSetting(string $service, string $setting)
    {
        return self::$servicesSettings[$service][$setting]
            ?? self::$globalSettings[$setting]
            ?? self::$defaultSettings[$setting];
    }

    /**
     * Check if circuit is available (closed)
     *
     * @param string $service
     * @return bool
     */
    public static function isAvailable(string $service): bool
    {
        if (self::$adapter->isOpen($service)) {
            return false;
        }

        if (self::$adapter->reachRateLimit($service)) {

            self::$adapter->setOpenCircuit($service);
            self::$adapter->setHalfOpenCircuit($service);
            return false;
        }

        return true;
    }

    /**
     * Set new failure for a service
     *
     * @param string $service
     * @return bool
     */
    public static function failure(string $service)
    {

        if (self::$adapter->isHalfOpen($service)) {
            self::$adapter->setOpenCircuit($service);
            self::$adapter->setHalfOpenCircuit($service);
            return false;
        }

        return self::$adapter->incrementFailure($service);
    }

    /**
     * Record success and clear all status
     *
     * @param string $service
     * @return bool|int
     */
    public static function success(string $service)
    {
        return self::$adapter->setSuccess($service);
    }

    /**
     * @inheritDoc
     */
    public static function setMassiveServices(array $services, array $settings): void
    {
        foreach ($services as $service) {
            self::$adapter->setService($service);

            self::setServiceSettings($service,$settings);
        }
    }

    /**
     * Set custom settings for each service
     *
     * @param string $service
     * @param array $settings
     */
    protected static function setServiceSettings(string $service, array $settings): void
    {
        foreach (self::$defaultSettings as $defaultSetting => $settingValue) {
            self::$servicesSettings[$service][$defaultSetting] =
                (int)($settings[$defaultSetting] ?? self::$globalSettings[$defaultSetting] ?? $settingValue);
        }
    }

}
