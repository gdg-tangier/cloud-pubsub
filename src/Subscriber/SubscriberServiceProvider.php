<?php

namespace GDGTangier\PubSub\Subscriber;

use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use GDGTangier\PubSub\Subscriber\Console\Commands\SubscribeCommand;
use GDGTangier\PubSub\Subscriber\Console\Commands\SubscriberMakeCommand;

class SubscriberServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            SubscribeCommand::class,
            SubscriberMakeCommand::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerJobsMap();
        $this->addSubscriberConnector();
    }

    /**
     * Register the subscriber.
     *
     * @return void
     */
    protected function addSubscriberConnector()
    {
        $this->app->afterResolving(QueueManager::class, function (QueueManager $manager) {
            $manager->addConnector('pubsub', function () {
                return new SubscriberConnector();
            });
        });
    }

    /**
     * Register JobsMap.
     *
     * return @void
     */
    protected function registerJobsMap()
    {
        $this->app->singleton('gcloud.subscriber.map', function () {
            $jobs = config('pubsub.subscriptions');

            return new JobsMap($jobs);
        });
    }
}
