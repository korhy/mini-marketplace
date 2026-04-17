<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Doctrine\Type;

use App\Order\Domain\ValueObject\OrderId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class OrderIdType extends StringType
{
    public const string NAME = 'order_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?OrderId
    {
        if (null === $value) {
            return null;
        }

        return OrderId::fromString((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        return (string) $value;
    }
}
