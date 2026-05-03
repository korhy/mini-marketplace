<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Messenger;

use App\Shared\Domain\AggregateRoot;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

final class DomainEventDispatcher
{
    public function __construct(
        private MessageBusInterface $eventBus,
    ) {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->dispatchEvents($args->getObject());
    }

    public function postUpdate(PostUpdateEventArgs $args): void
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
