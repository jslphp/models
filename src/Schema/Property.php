<?php

namespace Jsl\Models\Schema;

use Jsl\Models\Attributes\Column;

class Property
{
    /**
     * @var string
     */
    public readonly string $name;

    /**
     * @var string
     */
    public readonly string $column;

    /**
     * @var bool
     */
    public bool $isPrimaryKey = false;

    /**
     * @var bool
     */
    public bool $isReadOnly = false;

    /**
     * @var bool
     */
    public bool $isCreatedAt = false;

    /**
     * @var bool
     */
    public bool $isUpdatedAt = false;

    /**
     * @var bool
     */
    public bool $isSoftDelete = false;


    /**
     * @param string $name
     * @param string $column
     */
    public function __construct(string $name, string $column)
    {
        $this->name = $name;
        $this->column = $column;
    }


    /**
     * @param Column $attribute
     *
     * @return self
     */
    public function setFromAttribute(Column $attribute): self
    {
        $this->isPrimaryKey = $attribute->isPrimaryKey;
        $this->isReadOnly = $attribute->isReadOnly;
        $this->isCreatedAt = $attribute->isCreatedAt;
        $this->isUpdatedAt = $attribute->isUpdatedAt;
        $this->isSoftDelete = $attribute->isSoftDelete;

        return $this;
    }


    /**
     * Check if it's a system property (set by the model or the database)
     *
     * @return bool
     */
    public function isSystemProperty(): bool
    {
        return $this->isPrimaryKey
            || $this->isReadOnly
            || $this->isCreatedAt
            || $this->isUpdatedAt
            || $this->isSoftDelete;
    }


    /**
     * Check if the property is a date
     *
     * @return bool
     */
    public function isDate(): bool
    {
        return $this->isCreatedAt || $this->isUpdatedAt || $this->isSoftDelete;
    }
}
