<?php

namespace Jsl\Models\Attributes;

use Attribute;

#[Attribute]
class Schema
{
    /**
     * @param string $table
     */
    public function __construct(public readonly string $table)
    {
    }
}
