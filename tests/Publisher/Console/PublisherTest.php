<?php

use Google\Cloud\PubSub\Message;
use GDGTangier\PubSub\Tests\TestCase;

class PublisherTest extends TestCase
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

    /** @test */
    public function can_publish_message_through_artisan_command()
    {
        $data = 'DummyMessage';

        $this->artisan('pubsub:publish', ['message' => $data, 'event' => self::TOPIC_NAME]);

        $result = $this->client->subscription(self::SUBSCRIPTION_NAME)->pull();

        /** @var \Google\Cloud\PubSub\Message $message */
        $message = $result[0];

        self::assertNotNull($message);
        self::assertInstanceOf(Message::class, $message);
        self::assertEquals($data, $message->data());
    }
}
