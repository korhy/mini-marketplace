<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Exception;

final class WalletNotFoundException extends \DomainException
{
    public static function withId(string $walletId): self
    {
        return new self(sprintf('Wallet with id %s not found.', $walletId));
    }
}
