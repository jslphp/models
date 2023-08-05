<?php

namespace Jsl\Models\Traits;

use DateTime;
use Jsl\Models\Attributes\Column;

trait SoftDelete
{
    /**
     * @var DateTime|null
     */
    #[Column(isSoftDelete: true)]
    public ?DateTime $deletedAt = null;
}
