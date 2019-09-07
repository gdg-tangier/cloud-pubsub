<?php

namespace GDGTangier\PubSub;

use Illuminate\Support\ServiceProvider;
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
        $configPath = __DIR__ . '/../config/pubsub.php';
        $this->publishes([
            $configPath => config_path('pubsub.php'),
        ], 'pubsub');
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
}
