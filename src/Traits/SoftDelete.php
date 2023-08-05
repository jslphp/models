<?php

namespace Jsl\Models\Traits;

use DateTime;

use function Jsl\Models\tzDate;

trait SoftDelete
{
    /**
     * @var DateTime|null
     */
    public ?DateTime $deletedAt = null;
}
