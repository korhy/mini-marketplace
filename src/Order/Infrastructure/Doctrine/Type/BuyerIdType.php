<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Doctrine\Type;

use App\Order\Domain\ValueObject\BuyerId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class BuyerIdType extends StringType
{
    public const string NAME = 'buyer_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?BuyerId
    {
        if (null === $value) {
            return null;
        }

        return BuyerId::fromString((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        return (string) $value;
    }
}
