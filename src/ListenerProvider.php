<?php

declare(strict_types=1);

namespace PeibinLaravel\Event;

use SplPriorityQueue;

class ListenerProvider
{
    /**
     * @var ListenerData[]
     */
    public array $listeners = [];

    /**
     * @param string $event       An event for which to return the relevant listeners
     * @return iterable<callable> An iterable (array, iterator, or generator) of callables.  Each
     *                            callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(string $event): iterable
    {
        $queue = new SplPriorityQueue();
        foreach ($this->listeners as $listener) {
            if ($event == $listener->event) {
                $queue->insert($listener->listener, $listener->priority);
            }
        }
        return $queue;
    }

    public function on(string $event, callable $listener, int $priority = ListenerData::DEFAULT_PRIORITY): void
    {
        $this->listeners[] = new ListenerData($event, $listener, $priority);
    }
}
