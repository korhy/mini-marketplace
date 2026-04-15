<?php

declare(strict_types=1);

namespace App\Listing\Infrastructure\Doctrine\Type;

use App\Listing\Domain\ValueObject\SellerId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class SellerIdType extends StringType
{
    public const string NAME = 'seller_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform
    $platform): ?SellerId
    {
        if ($value === null) {
            return null;
        }

        return SellerId::fromString((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform
    $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) $value;
    }
}