<?php

declare(strict_types=1);

namespace PeibinLaravel\Event;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use PeibinLaravel\Di\Annotation\AnnotationCollector;
use PeibinLaravel\Event\Annotations\Listener;

class ListenerProviderFactory
{
    private Repository $config;

    private Dispatcher $dispatcher;

    public function __construct(protected Container $container)
    {
        $this->config = $container->get(Repository::class);
        $this->dispatcher = $container->get(Dispatcher::class);
    }

    public function __invoke(): void
    {
        $this->registerConfig();
        $this->registerAnnotations();
    }

    private function registerConfig(): void
    {
        $listeners = $this->config->get('listeners', []);
        $listeners && $this->register($listeners);
    }

    private function registerAnnotations(): void
    {
        AnnotationCollector::get(Listener::class);
        $annotationListeners = AnnotationCollector::getClassesByAnnotation(Listener::class);

        $listeners = [];

        /** @var Listener[] $annotationListeners */
        foreach ($annotationListeners as $listener => $annotation) {
            $listeners[$annotation->event] = [$listener => $annotation->priority];
        }
        $listeners && $this->register($listeners);
    }

    private function register(array $listeners): void
    {
        // Support for prioritizing events.
        // Example: event_class=>[listener_class=>priority]
        // Example: event_class=>[listener_class]
        // Example: event_class=>listener_class

        foreach ($listeners as $event => $group) {
            foreach ((array)$group as $listener => $priority) {
                if (is_int($listener)) {
                    $listener = $priority;
                    $priority = ListenerData::DEFAULT_PRIORITY;
                }

                $this->dispatcher->listen($event, $listener, $priority);
            }
        }
    }
}
