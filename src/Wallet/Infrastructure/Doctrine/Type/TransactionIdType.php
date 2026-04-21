<?php

namespace App\Wallet\Infrastructure\Doctrine\Type;

use App\Wallet\Domain\ValueObject\TransactionId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class TransactionIdType extends GuidType
{
    public const NAME = 'transaction_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof TransactionId) {
            throw new \InvalidArgumentException('Expected instance of '.TransactionId::class);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?TransactionId
    {
        if (null === $value) {
            return null;
        }

        return TransactionId::fromString($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
