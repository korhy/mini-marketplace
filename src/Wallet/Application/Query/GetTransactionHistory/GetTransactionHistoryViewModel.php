<?php

declare(strict_types=1);

namespace App\Wallet\Application\Query\GetTransactionHistory;

final readonly class GetTransactionHistoryViewModel
{
    public function __construct(
        public string $id,
        public int $amount,
        public string $currency,
        public string $type,
        public string $createdAt,
    ) {
    }
}
