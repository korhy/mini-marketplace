<?php

declare(strict_types=1);

namespace App\Listing\Domain\ValueObject;

final readonly class ListingId
{
    public function __construct(
        private string $value)
    {
        if (empty(trim($this->value))) {
            throw new \InvalidArgumentException('Listing ID cannot be empty');
        }
    }

    public static function generate(): self
    {
        return new self(\Symfony\Component\Uid\Uuid::v7()->toRfc4122());
    }

    public static function fromString(string $value): self
    {
        if (!\Symfony\Component\Uid\Uuid::isValid($value)) {
            throw new \InvalidArgumentException("Invalid UUID: $value");
        }

        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
