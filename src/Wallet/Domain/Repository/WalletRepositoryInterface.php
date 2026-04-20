<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Repository;

use App\Wallet\Domain\Entity\Wallet;
use App\Wallet\Domain\ValueObject\WalletId;

interface WalletRepositoryInterface
{
    public function save(Wallet $wallet): void;

    public function findById(WalletId $id): ?Wallet;
}
