<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Uuid;

final readonly class BuyerId extends Uuid
{
}
