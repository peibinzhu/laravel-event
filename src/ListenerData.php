<?php

declare(strict_types=1);

namespace PeibinLaravel\Event;

class ListenerData
{
    public const DEFAULT_PRIORITY = 0;

    /**
     * @var callable
     */
    public $listener;

    public function __construct(public string $event, callable $listener, public int $priority)
    {
        $this->listener = $listener;
    }
}
