<?php

declare(strict_types=1);

namespace App\Order\Domain\Repository;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\ValueObject\OrderId;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function findById(OrderId $id): ?Order;
}
