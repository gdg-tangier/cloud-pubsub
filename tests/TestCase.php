<?php

namespace GDGTangier\PubSub\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use GDGTangier\PubSub\Tests\Subscriber\SubscriptionJobs\SubscriberClass;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Topic name in pubsub.
     *
     * @var string
     */
    const TOPIC_NAME = 'testTopic';

    /**
     * Subscriber name (queue).
     *
     * @var string
     */
    const SUBSCRIPTION_NAME = 'testPull';

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            //
        ];
    }
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['GDGTangier\PubSub\CloudPubSubServiceProvider'];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $app['config'];
        // Queue
        $config->set('queue.default', 'pubsub');
        $config->set('queue.connections.pubsub.queue', self::SUBSCRIPTION_NAME);
        $config->set('queue.connections.pubsub.driver', 'pubsub');

        $config->set('pubsub.subscriptions.'.SubscriberClass::class, self::TOPIC_NAME);
        $config->set('pubsub.events.testTopic', self::TOPIC_NAME);
    }
    /**
     * @return \Illuminate\Config\Repository
     */
    public function getConfig()
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app['config'];
        return $config;
    }
}