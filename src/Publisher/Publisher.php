<?php

namespace GDGTangier\PubSub\Publisher;

use Google\Cloud\PubSub\PubSubClient;

class Publisher
{
    /**
     * Google Cloud PubSub Client.
     *
     * @var \Google\Cloud\PubSub\PubSubClient
     */
    protected $client;

    /**
     * @var \GDGTangier\PubSub\Publisher\EventsMap
     */
    protected $events;

    /**
     * Publisher constructor.
     *
     * @param \Google\Cloud\PubSub\PubSubClient      $client
     * @param \GDGTangier\PubSub\Publisher\EventsMap $events
     */
    public function __construct(PubSubClient $client, EventsMap $events)
    {
        $this->client = $client;
        $this->events = $events;
    }

    /**
     * Publish data to the cloud.
     *
     * @param string $data
     * @param string $event
     * @param array  $attributes
     *
     * @throws \GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound
     *
     * @return array
     */
    public function publish($data, $event, $attributes = [])
    {
        $topicName = $this->events->formEvent($event);

        $topic = $this->client->topic($topicName);

        return $topic->publish([
            'data'       => $data,
            'attributes' => $this->prepareMessageAttributes($topicName, $attributes),
        ]);
    }

    /**
     * Prepare message attributes.
     *
     * @param string $topic
     * @param array  $attributes
     *
     * @return array
     */
    protected function prepareMessageAttributes($topic, $attributes)
    {
        return array_merge($attributes, [
            'TopicName' => $topic,
        ]);
    }

    /**
     * Get PubSubClient.
     *
     * @return \Google\Cloud\PubSub\PubSubClient
     */
    public function getClient()
    {
        return $this->client;
    }
}
