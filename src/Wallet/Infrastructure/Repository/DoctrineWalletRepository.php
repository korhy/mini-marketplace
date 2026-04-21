<?php

declare(strict_types=1);

namespace App\Wallet\Infrastructure\Repository;

use App\Wallet\Domain\Entity\Wallet;
use App\Wallet\Domain\Repository\WalletRepositoryInterface;
use App\Wallet\Domain\ValueObject\WalletId;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineWalletRepository implements WalletRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Wallet $wallet): void
    {
        $this->entityManager->persist($wallet);
        $this->entityManager->flush();
    }

    public function findById(WalletId $id): ?Wallet
    {
        return $this->entityManager->find(Wallet::class, $id);
    }
}
