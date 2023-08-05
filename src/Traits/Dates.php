<?php

namespace Jsl\Models\Traits;

use DateTime;
use Jsl\Models\Attributes\Column;

trait Dates
{
    /**
     * @var DateTime|null
     */
    #[Column(isCreatedAt: true)]
    public ?DateTime $createdAt = null;

    /**
     * @var DateTime|null
     */
    #[Column(isUpdatedAt: true)]
    public ?DateTime $updatedAt = null;


    /**
     * Get formatted createdAt
     *
     * @param string $format
     *
     * @return string
     */
    public function createdAt(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->createdAt->format($format);
    }


    /**
     * Get formatted updatedAt
     *
     * @param string $format
     *
     * @return string
     */
    public function updatedAt(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->updatedAt->format($format);
    }
}
