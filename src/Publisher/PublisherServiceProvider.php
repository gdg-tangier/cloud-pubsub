<?php

namespace GDGTangier\PubSub\Publisher;

use Illuminate\Support\ServiceProvider;
use GDGTangier\PubSub\Publisher\Console\Commands\PublishMessage;

class PublisherServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([PublishMessage::class,]);
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
    }

    /**
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
}
