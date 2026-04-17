<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

readonly class Uuid
{
    public function __construct(
        private string $value
    ) {
        if (!\Symfony\Component\Uid\Uuid::isValid($value)) {
            throw new \InvalidArgumentException("Invalid UUID: $value");
        }
    }

    public static function generate(): static
    {
        return new static(\Symfony\Component\Uid\Uuid::v7()->toRfc4122());
    }

    public static function fromString(string $value): static
    {
        return new static($value);
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
