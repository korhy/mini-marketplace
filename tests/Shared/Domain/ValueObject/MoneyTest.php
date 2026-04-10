<?php

declare(strict_types=1);

namespace App\Tests\Shared\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_it_creates_money(): void
    {
        $money = new Money(1000, 'USD');
        $this->assertEquals(1000, $money->amount());
        $this->assertEquals('USD', $money->currency());
    }

    public function test_it_throws_exception_for_negative_amount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money(-100, 'USD');
    }

    public function test_it_throws_exception_for_unsupported_currency(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money(1000, 'JPY');
    }

    public function test_it_adds_money(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(500, 'USD');
        $result = $money1->add($money2);
        $this->assertEquals(1500, $result->amount());
        $this->assertEquals('USD', $result->currency());
    }

    public function test_it_throws_exception_when_adding_different_currencies(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(500, 'EUR');
        $this->expectException(\InvalidArgumentException::class);
        $money1->add($money2);
    }

    public function test_it_subtracts_money(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(500, 'USD');
        $result = $money1->subtract($money2);
        $this->assertEquals(500, $result->amount());
        $this->assertEquals('USD', $result->currency());
    }

    public function test_it_throws_exception_when_subtracting_different_currencies(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(500, 'EUR');
        $this->expectException(\InvalidArgumentException::class);
        $money1->subtract($money2);
    }

    public function test_it_throws_exception_when_subtracting_to_negative(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(1500, 'USD');
        $this->expectException(\InvalidArgumentException::class);
        $money1->subtract($money2);
    }

    public function test_it_checks_equality(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(1000, 'USD');
        $money3 = new Money(500, 'USD');
        $money4 = new Money(1000, 'EUR');

        $this->assertTrue($money1->equals($money2));
        $this->assertFalse($money1->equals($money3));
        $this->assertFalse($money1->equals($money4));
    }

    public function test_it_is_immutable(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = $money1->add(new Money(500, 'USD'));

        $this->assertEquals(1000, $money1->amount());
        $this->assertEquals(1500, $money2->amount());
    }
}