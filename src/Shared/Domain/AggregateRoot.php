<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use App\Shared\Domain\Events\DomainEvent;

class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $events = [];

    public function recordEvent(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    /** @return DomainEvent[] */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}