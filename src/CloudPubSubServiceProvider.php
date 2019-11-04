<?php

namespace GDGTangier\PubSub;

use GDGTangier\PubSub\Console\StartEmulator;
use Illuminate\Support\ServiceProvider;
use GDGTangier\PubSub\Console\ListPubSubEventsCommand;
use GDGTangier\PubSub\Console\ListSubscriptionsCommand;
use GDGTangier\PubSub\Publisher\PublisherServiceProvider;
use GDGTangier\PubSub\Subscriber\SubscriberServiceProvider;

class CloudPubSubServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
        $this->commands([
            StartEmulator::class,
            ListPubSubEventsCommand::class,
            ListSubscriptionsCommand::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(PublisherServiceProvider::class);
        $this->app->register(SubscriberServiceProvider::class);
    }

    /**
     * Publish the package configuration file.
     *
     * @return void
     */
    protected function publishConfig()
    {
        $configPath = __DIR__ . '/../config/pubsub.php';
        $this->publishes([
            $configPath => config_path('pubsub.php'),
        ], 'pubsub');
    }
}
