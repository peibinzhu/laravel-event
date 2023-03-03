<?php

declare(strict_types=1);

namespace PeibinLaravel\Event;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->get(ListenerProviderFactory::class)();

        $this->app->singleton(ListenerProvider::class);
        $this->app->get(ListenerProvider::class);
    }
}
