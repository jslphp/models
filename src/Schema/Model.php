<?php

namespace Jsl\Models\Schema;

class Model
{
    /**
     * @var string
     */
    public readonly string $model;

    /**
     * @var string
     */
    public readonly string $table;

    /**
     * @var array<Property>
     */
    public array $properties;

    /**
     * @var array
     */
    public array $columns;

    /**
     * @var Property|null
     */
    public ?Property $primaryKey = null;

    /**
     * @var Property|null
     */
    public ?Property $createdAt = null;

    /**
     * @var Property|null
     */
    public ?Property $updatedAt = null;

    /**
     * @var Property|null
     */
    public ?Property $softDelete = null;


    /**
     * @param string $model
     * @param string $table
     */
    public function __construct(string $model, string $table)
    {
        $this->model = $model;
        $this->table = $table;
        $this->properties = [];
        $this->columns = [];
    }


    /**
     * @param Property $property
     *
     * @return self
     */
    public function add(Property $property): self
    {
        $this->properties[$property->name] = $property;
        $this->columns[$property->column] = $property;

        if ($property->isPrimaryKey) {
            $this->primaryKey = $property;
        }

        if ($property->isCreatedAt) {
            $this->createdAt = $property;
        }

        if ($property->isUpdatedAt) {
            $this->updatedAt = $property;
        }

        if ($property->isSoftDelete) {
            $this->softDelete = $property;
        }

        return $this;
    }


    /**
     * @param string $property
     *
     * @return bool
     */
    public function hasProperty(string $property): bool
    {
        return key_exists($property, $this->properties);
    }


    /**
     * @param string $property
     *
     * @return Property|null
     */
    public function property(string $property): ?Property
    {
        return $this->properties[$property] ?? null;
    }


    /**
     * @param string $column
     *
     * @return bool
     */
    public function hasColumn(string $column): bool
    {
        return key_exists($column, $this->columns);
    }


    /**
     * @param string $column
     *
     * @return Property|null
     */
    public function column(string $column): ?Property
    {
        return $this->columns[$column] ?? null;
    }
}
