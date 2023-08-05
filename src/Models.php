<?php

namespace Jsl\Models;

use Jsl\Database\ConnectionInterface;
use Jsl\Models\Components\Attributes;
use Jsl\Models\Schema\Model;

class Models
{
    /**
     * @var ?ConnectionInterface
     */
    protected static ?ConnectionInterface $connection = null;

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
     * @param ConnectionInterface $connection
     */
    public static function setConnection(ConnectionInterface $connection): void
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
