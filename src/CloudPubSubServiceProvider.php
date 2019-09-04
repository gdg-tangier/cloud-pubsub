<?php

namespace GDGTangier\PubSub;

use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use GDGTangier\PubSub\Subscriber\JobsMap;
use GDGTangier\PubSub\Publisher\PublisherManager;
use GDGTangier\PubSub\Subscriber\SubscriberConnector;
use GDGTangier\PubSub\Publisher\Console\Commands\PublishMessage;
use GDGTangier\PubSub\Subscriber\Console\Commands\SubscribeCommand;
use GDGTangier\PubSub\Subscriber\Console\Commands\SubscriberMakeCommand;

class CloudPubSubServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            PublishMessage::class,
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
        $this->registerPublisherManager();
        $this->registerPublisherConnection();
        $this->registerJobsMap();
        $this->addSubscriberConnector();
    }

    /**
     * Register the subscriber.
     */
    protected function addSubscriberConnector()
    {
        $this->app->afterResolving(QueueManager::class, function (QueueManager $manager) {
            $manager->addConnector('pubsub', function () {
                return new SubscriberConnector;
            });
        });
    }

    /*
     * Register PublisherManager.
     *
     * return @void
     */
    protected function registerPublisherManager()
    {
        $this->app->singleton('gcloud.publisher', function ($app) {
            return new PublisherManager($app);
        });
    }

    /**
     * Register publisher connection.
     *
     * @return void
     */
    public function registerPublisherConnection()
    {
        $this->app->singleton('gcloud.publisher.connection', function ($app) {
            /** @var \GDGTangier\PubSub\Publisher\PublisherManager $manager */
            $manager = $app['gcloud.publisher'];

            return $manager->connection();
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
