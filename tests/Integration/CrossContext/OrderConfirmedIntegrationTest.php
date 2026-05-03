<?php

declare(strict_types=1);

namespace App\Tests\Integration\CrossContext;

use App\Listing\Application\Command\CreateListing\CreateListingCommand;
use App\Listing\Application\Command\PublishListing\PublishListingCommand;
use App\Listing\Application\Query\GetListing\GetListingQuery;
use App\Listing\Application\Query\GetListing\ListingViewModel;
use App\Listing\Domain\Exception\ListingNotAvailableException;
use App\Order\Application\Command\ConfirmOrder\ConfirmOrderCommand;
use App\Order\Application\Command\PlaceOrder\PlaceOrderCommand;
use App\Order\Domain\Entity\Order;
use App\Wallet\Application\Command\CreditWallet\CreditWalletCommand;
use App\Wallet\Application\Query\GetWalletBalance\GetWalletBalanceQuery;
use App\Wallet\Application\Query\GetWalletBalance\GetWalletBalanceViewModel;
use App\Wallet\Domain\Entity\Wallet;
use App\Wallet\Domain\Exception\InsufficientFundsException;
use App\Wallet\Domain\ValueObject\WalletId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderConfirmedIntegrationTest extends KernelTestCase
{
    private MessageBusInterface $commandBus;
    private MessageBusInterface $queryBus;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $commandBus */
        $commandBus = self::getContainer()->get('command.bus');
        $this->commandBus = $commandBus;

        /** @var MessageBusInterface $queryBus */
        $queryBus = self::getContainer()->get('query.bus');
        $this->queryBus = $queryBus;

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager = $em;

        $this->resetDatabase();
    }

    private function resetDatabase(): void
    {
        $this->entityManager->getConnection()->executeStatement('DELETE FROM transactions');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM wallets');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM orders');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM listings');
    }

    private function createPublishedListing(string $sellerId, int $price = 5000): string
    {
        $this->commandBus->dispatch(new CreateListingCommand(
            $sellerId,
            'Vintage Guitar',
            'A beautiful vintage guitar in great condition.',
            $price,
            'EUR',
            'used',
        ));

        /** @var \App\Listing\Domain\Entity\Listing $listing */
        $listing = $this->entityManager
            ->getRepository(\App\Listing\Domain\Entity\Listing::class)
            ->findOneBy([]);

        $this->commandBus->dispatch(new PublishListingCommand((string) $listing->id()));

        $this->entityManager->clear();

        return (string) $listing->id();
    }

    private function createWalletWithFunds(string $walletId, int $amount): void
    {
        $wallet = new Wallet(new WalletId($walletId), new \App\Wallet\Domain\ValueObject\Balance(0, 'EUR'));
        $this->entityManager->persist($wallet);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->commandBus->dispatch(new CreditWalletCommand($walletId, $amount, 'EUR'));
        $this->entityManager->clear();
    }

    private function placeOrder(string $buyerId, string $listingId, int $price): string
    {
        $this->commandBus->dispatch(new PlaceOrderCommand($buyerId, $listingId, $price, 'EUR'));

        /** @var Order[] $orders */
        $orders = $this->entityManager
            ->createQuery('SELECT o FROM App\Order\Domain\Entity\Order o WHERE o.buyerId = :buyerId')
            ->setParameter('buyerId', $buyerId)
            ->getResult();

        $this->entityManager->clear();

        return (string) $orders[0]->id();
    }

    // --- Cas nominal ---

    public function testConfirmOrderMarkListingAsSoldAndDebitsWallet(): void
    {
        $buyerId = \Symfony\Component\Uid\Uuid::v7()->toRfc4122();
        $sellerId = \Symfony\Component\Uid\Uuid::v7()->toRfc4122();

        $this->createWalletWithFunds($buyerId, 10000);
        $listingId = $this->createPublishedListing($sellerId, 5000);
        $orderId = $this->placeOrder($buyerId, $listingId, 5000);

        $this->commandBus->dispatch(new ConfirmOrderCommand($orderId));
        $this->entityManager->clear();

        $listingEnvelope = $this->queryBus->dispatch(new GetListingQuery($listingId));
        /** @var \Symfony\Component\Messenger\Stamp\HandledStamp $listingStamp */
        $listingStamp = $listingEnvelope->last(\Symfony\Component\Messenger\Stamp\HandledStamp::class);
        /** @var ListingViewModel $listing */
        $listing = $listingStamp->getResult();
        $this->assertSame('sold', $listing->status);

        $walletEnvelope = $this->queryBus->dispatch(new GetWalletBalanceQuery($buyerId));
        /** @var \Symfony\Component\Messenger\Stamp\HandledStamp $walletStamp */
        $walletStamp = $walletEnvelope->last(\Symfony\Component\Messenger\Stamp\HandledStamp::class);
        /** @var GetWalletBalanceViewModel $wallet */
        $wallet = $walletStamp->getResult();
        $this->assertSame(5000, $wallet->balance);
    }

    // --- InsufficientFundsException ---

    public function testConfirmOrderWithInsufficientFundsThrowsException(): void
    {
        $buyerId = \Symfony\Component\Uid\Uuid::v7()->toRfc4122();
        $sellerId = \Symfony\Component\Uid\Uuid::v7()->toRfc4122();

        $this->createWalletWithFunds($buyerId, 1000);
        $listingId = $this->createPublishedListing($sellerId, 5000);
        $orderId = $this->placeOrder($buyerId, $listingId, 5000);

        try {
            $this->commandBus->dispatch(new ConfirmOrderCommand($orderId));
            $this->fail('Expected InsufficientFundsException');
        } catch (HandlerFailedException $e) {
            $cause = $e;
            while ($cause instanceof HandlerFailedException) {
                $cause = $cause->getPrevious();
            }
            $this->assertInstanceOf(InsufficientFundsException::class, $cause);
        }
    }

    // --- Concurrence ---

    public function testConcurrentOrdersOnSameListingThrowsListingNotAvailableException(): void
    {
        $buyerAId = \Symfony\Component\Uid\Uuid::v7()->toRfc4122();
        $buyerBId = \Symfony\Component\Uid\Uuid::v7()->toRfc4122();
        $sellerId = \Symfony\Component\Uid\Uuid::v7()->toRfc4122();

        $this->createWalletWithFunds($buyerAId, 10000);
        $this->createWalletWithFunds($buyerBId, 10000);

        $listingId = $this->createPublishedListing($sellerId, 5000);

        $orderAId = $this->placeOrder($buyerAId, $listingId, 5000);
        $orderBId = $this->placeOrder($buyerBId, $listingId, 5000);

        $this->commandBus->dispatch(new ConfirmOrderCommand($orderAId));
        $this->entityManager->clear();

        try {
            $this->commandBus->dispatch(new ConfirmOrderCommand($orderBId));
            $this->fail('Expected ListingNotAvailableException');
        } catch (HandlerFailedException $e) {
            $cause = $e;
            while ($cause instanceof HandlerFailedException) {
                $cause = $cause->getPrevious();
            }
            $this->assertInstanceOf(ListingNotAvailableException::class, $cause);
        }
    }
}
