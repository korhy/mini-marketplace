<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Repository;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Repository\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\OrderId;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function findById(OrderId $id): ?Order
    {
        return $this->entityManager->find(Order::class, $id);
    }
}
