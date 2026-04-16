<?php

declare(strict_types=1);

namespace App\Listing\Domain\ValueObject;

final readonly class ListingDescription
{
    public function __construct(
        private string $value,
    ) {
        if (empty($this->value)) {
            throw new \InvalidArgumentException('Listing description cannot be empty.');
        }

        if (strlen($this->value) > 2000) {
            throw new \InvalidArgumentException('Listing description cannot exceed 2000 characters.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
