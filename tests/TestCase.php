<?php

namespace GDGTangier\PubSub\Tests;

use GDGTangier\PubSub\PubSub;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use GDGTangier\PubSub\Tests\Subscriber\SubscriptionJobs\SubscriberClass;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * The underlying PubSub client.
     *
     * @var \Google\Cloud\PubSub\PubSubClient
     */
    public $client;

    /**
     * The underlying publisher instance.
     *
     * @var \GDGTangier\PubSub\Publisher\Publisher
     */
    public $publisher;

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
     * Get package aliases.
     *
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
     * Get package providers.
     *
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
     * Get configuration.
     *
     * @return \Illuminate\Config\Repository
     */
    public function getConfig()
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app['config'];

        return $config;
    }

    /**
     * Setup Pub/Sub env, creating the topic and the subscription.
     *
     * @return void
     */
    public function setUpPubSub()
    {
        PubSub::useEmulatorCredentials();
        $this->publisher = app('gcloud.publisher.connection');
        $this->client = $this->publisher->getClient();
        $this->publisher->getClient()->createTopic(self::TOPIC_NAME);
        $this->publisher->getClient()->subscribe(self::SUBSCRIPTION_NAME, self::TOPIC_NAME);
    }

    /**
     * Delete the Pub/Sub env. delete the topic and the subscription.
     *
     * @return void
     */
    public function deletePubSub()
    {
        parent::tearDown();
        $this->client->topic(self::TOPIC_NAME)->delete();
        $this->client->subscription(self::SUBSCRIPTION_NAME)->delete();
    }
}
