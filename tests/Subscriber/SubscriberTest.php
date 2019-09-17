<?php

namespace GDGTangier\PubSub\Tests\Subscriber;

use Illuminate\Queue\Events\JobProcessed;
use GDGTangier\PubSub\Subscriber\SubscriberJob;
use GDGTangier\PubSub\Tests\Subscriber\SubscriptionJobs\SubscriberClass;

class SubscriberTest extends \GDGTangier\PubSub\Tests\TestCase
{
    /**
     * @var \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public $job;

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPubSub();
    }

    /**
     * Teardown.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->deletePubSub();
    }

    /**
     * @test
     *
     * @throws \GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound
     */
    public function can_work_a_publishable_message()
    {
        $this->publisher->publish('MyMessage', self::TOPIC_NAME);

        $this->app['events']->listen(JobProcessed::class, function ($event) {
            $this->job = $event->job;
        });

        $this->artisan('queue:work', ['connection' => 'pubsub', '--once' => true]);

        self::assertNotNull($this->job);
        self::assertInstanceOf(SubscriberJob::class, $this->job);

        /** @var \GDGTangier\PubSub\Tests\Subscribers\SubscriberClass|null $subscriberInstance */
        $subscriberInstance = $this->job->getResolvedInstance();

        self::assertNotNull($subscriberInstance);
        self::assertInstanceOf(SubscriberClass::class, $subscriberInstance);
        self::assertEquals('MyMessage', $subscriberInstance->payload);
        self::assertTrue($this->job->isDeleted());
    }
}
