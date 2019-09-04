<?php

namespace GDGTangier\PubSub\Publisher;

use GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound;

class Publisher
{
    /**
     * Google Cloud PubSub Client.
     *
     * @var \Google\Cloud\PubSub\PubSubClient
     */
    public $client;

    /**
     * Publisher constructor.
     *
     * @param \Google\Cloud\PubSub\PubSubClient $pubsubClient
     */
    public function __construct($pubsubClient)
    {
        $this->client = $pubsubClient;
    }

    /**
     * Publish data to the cloud.
     *
     * @param string $data
     * @param string $topicName
     * @param array $attributes
     *
     * @return array
     * @throws \GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound
     */
    public function publish($data, $topicName, $attributes = [])
    {
        $topicName = $this->getTopicName($topicName);

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
     * @param array $attributes
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
     * Get the topic name.
     *
     * @param string $topicName
     *
     * @return mixed
     * @throws \GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound
     */
    protected function getTopicName($topicName)
    {
        $events = collect(config('pubsub.events'));

        if ($events->search($topicName)) {
            return $topicName;
        }

        if ($topic = $events->get($topicName)) {
            return $topic;
        }

        Throw new TopicNotFound("Event [{$topicName}] Not Found");
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
