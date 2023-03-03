<?php

declare(strict_types=1);

namespace PeibinLaravel\Event;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use PeibinLaravel\SwooleEvent\Events\BootApplication;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->singleton(ListenerProvider::class);
        $this->app->get(ListenerProvider::class);

        $this->app->get(Dispatcher::class)->listen(BootApplication::class, function () {
            $this->app->get(ListenerProviderFactory::class)();
        });
    }
}
