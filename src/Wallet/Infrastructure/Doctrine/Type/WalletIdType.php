<?php

namespace App\Wallet\Infrastructure\Doctrine\Type;

use App\Wallet\Domain\ValueObject\WalletId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class WalletIdType extends GuidType
{
    public const NAME = 'wallet_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof WalletId) {
            throw new \InvalidArgumentException('Expected instance of '.WalletId::class);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?WalletId
    {
        if (null === $value) {
            return null;
        }

        return WalletId::fromString($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
