<?php

declare(strict_types=1);

namespace App\Wallet\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Uuid;

final readonly class TransactionId extends Uuid
{
}
