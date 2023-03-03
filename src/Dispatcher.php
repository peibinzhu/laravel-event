<?php

declare(strict_types=1);

namespace PeibinLaravel\Event;

use Closure;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Events\QueuedClosure;

class Dispatcher extends \Illuminate\Events\Dispatcher
{
    private ListenerProvider $listenerProvider;

    public function __construct(ContainerContract $container = null)
    {
        parent::__construct($container);
        $this->listenerProvider = $container->get(ListenerProvider::class);
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param Closure|string|array      $events
     * @param Closure|string|array|null $listener
     * @return void
     */
    public function listen($events, $listener = null): void
    {
        // If the third parameter is passed, it means to set the priority of
        // the event listener.
        $priority = func_num_args() > 2 ? func_get_arg(2) : ListenerData::DEFAULT_PRIORITY;
        $this->recordListenerLriority($events, $listener, $priority);

        parent::listen($events, $listener);
    }

    private function recordListenerLriority($events, $listener, int $priority = ListenerData::DEFAULT_PRIORITY)
    {
        if ($events instanceof Closure) {
            return collect($this->firstClosureParameterTypes($events))
                ->each(function ($event) use ($events) {
                    $this->listen($event, $events);
                });
        } elseif ($events instanceof QueuedClosure) {
            return collect($this->firstClosureParameterTypes($events->closure))
                ->each(function ($event) use ($events) {
                    $this->listen($event, $events->resolve());
                });
        } elseif ($listener instanceof QueuedClosure) {
            $listener = $listener->resolve();
        }

        // We will record the priority of event listeners and execute them
        // in priority order when triggering event scheduling.
        foreach ((array)$events as $event) {
            $wildcard = str_contains($event, '*');
            $this->listenerProvider->on($event, $this->makeListener($listener, $wildcard), $priority);
        }
    }

    /**
     * Get all of the listeners for a given event name.
     *
     * @param $eventName
     * @return array|callable[]|iterable
     */
    public function getListeners($eventName)
    {
        return $this->listenerProvider->getListenersForEvent($eventName);
    }
}
