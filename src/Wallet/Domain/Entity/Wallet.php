<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Entity;

use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\ValueObject\Money;
use App\Wallet\Domain\Event\FundsCredited;
use App\Wallet\Domain\Event\FundsDebited;
use App\Wallet\Domain\Exception\InsufficientFundsException;
use App\Wallet\Domain\ValueObject\Balance;
use App\Wallet\Domain\ValueObject\WalletId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'wallets')]
class Wallet extends AggregateRoot
{
    /** @var Collection<int, Transaction> */
    #[ORM\OneToMany(mappedBy: 'wallet', targetEntity: Transaction::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $transactions;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'wallet_id')]
        private WalletId $id,

        #[ORM\Embedded(class: Balance::class)]
        private Balance $balance,
    ) {
        $this->transactions = new ArrayCollection();
    }

    public function credit(Money $amount): void
    {
        $this->balance = $this->balance->increase($amount);
        $this->transactions[] = Transaction::credit($amount, 'Funds credited', $this);

        $this->recordEvent(new FundsCredited(
            $this->id,
            $amount,
        ));
    }

    public function debit(Money $amount): void
    {
        if ($this->balance->isLessThan($amount)) {
            throw InsufficientFundsException::forWallet((string) $this->id);
        }

        $this->balance = $this->balance->decrease($amount);
        $this->transactions[] = Transaction::debit($amount, 'Funds debited', $this);
        $this->recordEvent(new FundsDebited(
            $this->id,
            $amount,
        ));
    }

    public function id(): WalletId
    {
        return $this->id;
    }

    public function balance(): Balance
    {
        return $this->balance;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function transactions(): Collection
    {
        return $this->transactions;
    }
}
