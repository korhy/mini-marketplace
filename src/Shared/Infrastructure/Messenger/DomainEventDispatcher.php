<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Messenger;

use App\Shared\Domain\AggregateRoot;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

final class DomainEventDispatcher implements EventSubscriber
{
    public function __construct(
        private MessageBusInterface $eventBus,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [Events::postPersist, Events::postUpdate];
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $args */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->dispatchEvents($args->getObject());
    }

    /** @param LifecycleEventArgs<EntityManagerInterface> $args */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->dispatchEvents($args->getObject());
    }

    private function dispatchEvents(object $entity): void
    {
        if (!$entity instanceof AggregateRoot) {
            return;
        }

        foreach ($entity->pullEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
