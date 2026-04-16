<?php

declare(strict_types=1);

namespace App\Listing\Infrastructure\Doctrine\Type;

use App\Listing\Domain\ValueObject\ListingTitle;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class ListingTitleType extends StringType
{
    public const string NAME = 'listing_title';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?ListingTitle
    {
        if (null === $value) {
            return null;
        }

        return new ListingTitle((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        return (string) $value;
    }
}
