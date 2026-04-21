<?php

declare(strict_types=1);

namespace App\Tests\Wallet\Domain\Entity;

use App\Shared\Domain\ValueObject\Money;
use App\Wallet\Domain\Entity\Wallet;
use App\Wallet\Domain\Event\FundsCredited;
use App\Wallet\Domain\Event\FundsDebited;
use App\Wallet\Domain\Exception\InsufficientFundsException;
use App\Wallet\Domain\ValueObject\Balance;
use App\Wallet\Domain\ValueObject\WalletId;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    // --- Helper ---

    private function createWallet(): Wallet
    {
        return new Wallet(
            WalletId::generate(),
            new Balance(0, 'EUR'),
        );
    }

    // --- Crédit ---
    public function testCreditIncreasesBalance(): void
    {
        $wallet = $this->createWallet();

        $wallet->credit(new Money(50, 'EUR'));

        $this->assertEquals(new Balance(50, 'EUR'), $wallet->balance());
    }

    public function testCreditIncreasesBalanceEvent(): void
    {
        $wallet = $this->createWallet();

        $wallet->credit(new Money(50, 'EUR'));

        $events = $wallet->pullEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(FundsCredited::class, $events[0]);
    }

    // --- Debit ---
    public function testDebitDecreasesBalance(): void
    {
        $wallet = $this->createWallet();
        $wallet->credit(new Money(100, 'EUR'));

        $wallet->debit(new Money(30, 'EUR'));

        $this->assertEquals(new Balance(70, 'EUR'), $wallet->balance());
    }

    public function testDebitExactBalance(): void
    {
        $wallet = $this->createWallet();
        $wallet->credit(new Money(50, 'EUR'));

        $wallet->debit(new Money(50, 'EUR'));

        $this->assertEquals(new Balance(0, 'EUR'), $wallet->balance());
    }

    public function testDebitRecordsFundsDebitedEvent(): void
    {
        $wallet = $this->createWallet();
        $wallet->credit(new Money(100, 'EUR'));

        $wallet->debit(new Money(30, 'EUR'));

        $events = $wallet->pullEvents();

        $this->assertCount(2, $events);
        $this->assertInstanceOf(FundsCredited::class, $events[0]);
        $this->assertInstanceOf(FundsDebited::class, $events[1]);
    }

    public function testDebitThrowsIfInsufficientFunds(): void
    {
        $wallet = $this->createWallet();
        $wallet->credit(new Money(50, 'EUR'));

        $this->expectException(InsufficientFundsException::class);

        $wallet->debit(new Money(100, 'EUR'));
    }

    // --- Transactions ---
    public function testTransactionsAreRecorded(): void
    {
        $wallet = $this->createWallet();

        $wallet->credit(new Money(100, 'EUR'));
        $wallet->debit(new Money(30, 'EUR'));

        $transactions = $wallet->transactions();

        $this->assertCount(2, $transactions);
    }

    // --- pullEvents() ---
    public function testPullEventsEmptiesEventQueue(): void
    {
        $wallet = $this->createWallet();
        $wallet->credit(new Money(100, 'EUR'));

        $events = $wallet->pullEvents();

        $this->assertCount(1, $events);
        $this->assertEmpty($wallet->pullEvents());
    }
}
