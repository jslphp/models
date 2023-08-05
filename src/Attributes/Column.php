<?php

namespace Jsl\Models\Attributes;

use Attribute;

#[Attribute]
class Column
{
    /**
     * @param string $column - Leave empty to use property name
     * @param bool $isPrimaryKey
     * @param bool $isReadOnly
     * @param bool $isCreatedAt
     * @param bool $isUpdatedAt
     * @param bool $isSoftDelete
     */
    public function __construct(
        public string $column = '',
        public bool $isPrimaryKey = false,
        public bool $isReadOnly = false,
        public bool $isCreatedAt = false,
        public bool $isUpdatedAt = false,
        public bool $isSoftDelete = false,
    ) {
    }
}
