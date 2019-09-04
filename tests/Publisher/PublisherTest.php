<?php

namespace GDGTangier\PubSub\Publisher;

use GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound;

class PublisherTest extends \GDGTangier\PubSub\Tests\TestCase
{
    /** @var \Google\Cloud\PubSub\PubSubClient */
    public $client;

    /** @var \GDGTangier\PubSub\Publisher\Publisher */
    public $publisher;

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->publisher = app('gcloud.publisher.connection');
        $this->client = $this->publisher->getClient();
        $this->publisher->getClient()->createTopic(self::TOPIC_NAME);
    }

    /**
     * Teardown.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client->topic(self::TOPIC_NAME)->delete();
    }

    /**
     * @test
     * @throws \GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound
     */
    public function can_publish_a_message()
    {
        $response = $this->publisher->publish('DummyMessage', self::TOPIC_NAME);

        self::assertTrue(count($response['messageIds']) != 0);
    }

    /** @test */
    public function throws_exception_if_event_is_not_exists()
    {
        self::expectException(TopicNotFound::class);
        $this->publisher->publish('DummyMessage', 'event');
    }
}
