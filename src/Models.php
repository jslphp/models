<?php

namespace Jsl\Models;

use Exception;
use Jsl\Database\ConnectionInterface;

class Models
{
    protected const DEFAULT_CONNECTION_KEY = '__default__';

    /**
     * @var array<ConnectionInterface>
     */
    protected static array $connections = [];

    /**
     * @var array
     */
    protected static array $models = [];


    /**
     * Set the default connection
     *
     * @param ConnectionInterface $connection
     * @param string $model If empty, it will set the default connection
     *
     * @return void
     */
    public static function setConnection(ConnectionInterface $connection, string $model = ''): void
    {
        static::$connections[$model ?: self::DEFAULT_CONNECTION_KEY] = $connection;
    }


    /**
     * Get a connection for a model
     *
     * @param string $model
     *
     * @return ConnectionInterface
     */
    public static function getConnection(string $model): ConnectionInterface
    {
        if (key_exists($model, static::$connections)) {
            return static::$connections[$model];
        }

        if (key_exists(static::DEFAULT_CONNECTION_KEY, static::$connections) === false) {
            throw new Exception("Model {$model} has no specific connection and no default is set");
        }

        return static::$connections[static::DEFAULT_CONNECTION_KEY];
    }


    /**
     * Check if a model has a connection
     *
     * @param string|object $model If empty, it will check for a default connection
     *
     * @return bool
     */
    public static function hasConnection(string $model = ''): bool
    {
        return key_exists($model ?: static::DEFAULT_CONNECTION_KEY, static::$connections);
    }


    /**
     * Get model columns
     *
     * @param string|object $model
     *
     * @return array
     */
    public static function getColumns(string|object $model): array
    {
        $name = is_object($model) ? $model::class : $model;

        return static::$models[$name] ??= getPublicProperties($model);
    }
}
