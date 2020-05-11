<?php

namespace Tortelli\CircuitBreaker\Interfaces;

use Tortelli\CircuitBreaker\Adapters\IAdapter;
use Tortelli\CircuitBreaker;

interface ICircuitBreaker {
    /**
     * @param AdapterInterface $adapter
     */
    public static function setAdapter(IAdapter $adapter): void;

    /**
     * @return AdapterInterface
     */
    public static function getAdapter(): IAdapter;

    /**
     * Set global settings for all services
     *
     * @param array $settings
     */
    public static function setGlobalSettings(array $settings): void;

    /**
     * @return array
     */
    public static function getGlobalSettings(): array;

    /**
     * Set custom settings for each service
     *
     * @param string $service
     * @param array $settings
     */
    public static function setServiceSettings(string $service, array $settings): void;

    /**
     * Get setting for a service, if not set, get from default settings
     *
     * @param string $service
     * @param string $setting
     * @return mixed
     */
    public static function getServiceSetting(string $service, string $setting);

    /**
     * Check if circuit is available (closed)
     *
     * @param string $service
     * @return bool
     */
    public static function isAvailable(string $service): bool;

    /**
     * Set new failure for a service
     *
     * @param string $service
     * @return bool
     */
    public static function failure(string $service);

    /**
     * Record success and clear all status
     *
     * @param string $service
     * @return bool|int
     */
    public static function success(string $service);
}



