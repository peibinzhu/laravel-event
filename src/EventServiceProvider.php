<?php

declare(strict_types=1);

namespace PeibinLaravel\Event;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Events\MainServerStarting;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $events = [
            ArtisanStarting::class,
            MainServerStarting::class,
        ];
        $this->app->get(DispatcherContract::class)->listen($events, function () {
            $this->app->get(ListenerProviderFactory::class)();
        });
    }
}
