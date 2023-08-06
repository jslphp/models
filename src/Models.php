<?php

namespace Jsl\Models;

use Closure;
use Jsl\Database\ConnectionInterface;
use Jsl\Models\Components\Attributes;
use Jsl\Models\Schema\Model;

class Models
{
    /**
     * @var callable|ConnectionInterface|null
     */
    protected static Closure|string|ConnectionInterface|null $connection = null;

    /**
     * @var Attributes
     */
    protected Attributes $attributes;

    /**
     * @var array<Model>
     */
    protected array $models = [];


    public function __construct()
    {
        $this->attributes = new Attributes;
    }


    /**
     * Set the connection for the models
     * 
     * @param callable|ConnectionInterface $connection
     */
    public static function setConnection(callable|ConnectionInterface $connection): void
    {
        static::$connection = $connection;
    }



    /**
     * Check if a connection is already set
     *
     * @return bool
     */
    public static function hasConnection(): bool
    {
        return static::$connection !== null;
    }


    /**
     * Get the connection
     *
     * @return ConnectionInterface|null
     */
    public function getConnection(): ?ConnectionInterface
    {
        if (is_callable(static::$connection) && static::$connection instanceof ConnectionInterface === false) {
            static::$connection = call_user_func(static::$connection);
        }

        return static::$connection;
    }


    /**
     * Get a model
     *
     * @param string $modelClass
     *
     * @return Model|null
     */
    public function get(string $modelClass): ?Model
    {
        if (key_exists($modelClass, $this->models)) {
            return $this->models[$modelClass];
        }

        if ($model = $this->attributes->fromModel($modelClass)) {
            $this->models[$modelClass] = $model;
        }

        return $model;
    }
}
