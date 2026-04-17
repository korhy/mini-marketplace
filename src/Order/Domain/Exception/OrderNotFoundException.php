<?php

declare(strict_types=1);

namespace App\Order\Domain\Exception;

final class OrderNotFoundException extends \DomainException
{
    public static function forOrder(string $orderId): self
    {
        return new self(sprintf('Order "%s" not found.', $orderId));
    }
}
