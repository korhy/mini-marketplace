<?php

declare(strict_types=1);

namespace App\Order\Domain\Exception;

final class OrderCannotBeCancelledException extends \DomainException
{
    public static function forOrder(string $orderId): self
    {
        return new self(sprintf('Order "%s" cannot be cancelled.', $orderId));
    }
}
