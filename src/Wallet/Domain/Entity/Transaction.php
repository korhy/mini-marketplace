<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Entity;

use App\Shared\Domain\ValueObject\Money;
use App\Wallet\Domain\ValueObject\TransactionId;
use App\Wallet\Domain\ValueObject\TransactionType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'transactions')]
final class Transaction
{
    private function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'transaction_id')]
        private readonly TransactionId $id,

        #[ORM\Embedded(class: Money::class)]
        private readonly Money $amount,

        #[ORM\Column(type: 'string', enumType: TransactionType::class)]
        private readonly TransactionType $type,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $createdAt,

        #[ORM\Column(type: 'string')]
        private readonly string $description,

        #[ORM\ManyToOne(targetEntity: Wallet::class, inversedBy: 'transactions')]
        #[ORM\JoinColumn(nullable: false)]
        private Wallet $wallet,
    ) {
    }

    public static function credit(Money $amount, string $description, Wallet $wallet): self
    {
        return new self(TransactionId::generate(), $amount, TransactionType::CREDIT, new \DateTimeImmutable(), $description, $wallet);
    }

    public static function debit(Money $amount, string $description, Wallet $wallet): self
    {
        return new self(TransactionId::generate(), $amount, TransactionType::DEBIT, new \DateTimeImmutable(), $description, $wallet);
    }

    public function id(): TransactionId
    {
        return $this->id;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function type(): TransactionType
    {
        return $this->type;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function wallet(): Wallet
    {
        return $this->wallet;
    }
}
