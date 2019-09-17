<?php

namespace GDGTangier\PubSub\Publisher;

use GDGTangier\PubSub\Publisher\Facades\PublisherFacade;
use GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound;

class PublisherTest extends \GDGTangier\PubSub\Tests\TestCase
{
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
    public function can_publish_a_message()
    {
        $response = $this->publisher->publish('DummyMessage', self::TOPIC_NAME);

        self::assertTrue(count($response['messageIds']) != 0);
    }

    /** @test */
    public function can_publish_a_message_trough_facade()
    {
        $response = PublisherFacade::publish('DummyMessage', self::TOPIC_NAME);

        self::assertTrue(count($response['messageIds']) != 0);
    }

    /** @test */
    public function throws_exception_if_event_is_not_exists()
    {
        self::expectException(TopicNotFound::class);
        $this->publisher->publish('DummyMessage', 'event');
    }
}
