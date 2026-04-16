<?php

declare(strict_types=1);

namespace App\Listing\Domain\ValueObject;

final readonly class ListingTitle
{
    public function __construct(
        private string $value,
    ) {
        if (empty($this->value)) {
            throw new \InvalidArgumentException('Listing title cannot be empty.');
        }

        if (strlen($this->value) > 100) {
            throw new \InvalidArgumentException('Listing title cannot exceed 100 characters.');
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
