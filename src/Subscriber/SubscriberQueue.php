<?php

namespace GDGTangier\PubSub\Subscriber;

use Illuminate\Queue\Queue;
use Illuminate\Pipeline\Pipeline;
use Google\Cloud\PubSub\PubSubClient;
use GDGTangier\PubSub\Subscriber\Pipeline\GetMessage;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use GDGTangier\PubSub\Subscriber\Pipeline\HasTopicName;
use GDGTangier\PubSub\Subscriber\Pipeline\MessageCaching;

class SubscriberQueue extends Queue implements QueueContract
{
    /**
     * Google Cloud Client.
     *
     * @var \Google\Cloud\PubSub\PubSubClient
     */
    protected $client;

    /**
     * @var array
     */
    protected $pipes = [
        GetMessage::class,
        HasTopicName::class,
        MessageCaching::class,
    ];

    /**
     * SubscriberQueue constructor.
     *
     * @param \Google\Cloud\PubSub\PubSubClient $client
     */
    public function __construct(PubSubClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get the size of the queue.
     *
     * @param string $queue
     *
     * @return int
     */
    public function size($queue = null)
    {
        // TODO: Implement size() method.
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string|object $job
     * @param mixed         $data
     * @param string        $queue
     *
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        // TODO: Implement push() method.
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param string $payload
     * @param string $queue
     * @param array  $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        // TODO: Implement pushRaw() method.
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string|object                        $job
     * @param mixed                                $data
     * @param string                               $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        // TODO: Implement later() method.
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     *
     * @throws \GDGTangier\PubSub\Subscriber\Exceptions\SubscriberJobNotFound
     *
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $subscription = $this->client->subscription($queue);

        $messages = $subscription->pull();

        return $this->processMessages($queue, $messages);
    }

    /**
     * Process the messages coming form the subscription.
     *
     * @param $queue
     * @param $messages
     *
     * @throws \GDGTangier\PubSub\Subscriber\Exceptions\SubscriberJobNotFound
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberJob|void
     */
    protected function processMessages($queue, $messages): ?SubscriberJob
    {
        // First we need to pipe the messages through the pipeline.
        $message = $this->pipe($messages);

        if (is_null($message)) {
            return;
        }

        /** @var \GDGTangier\PubSub\Subscriber\JobsMap $map */
        $map = app('gcloud.subscriber.map');

        // Extract the job handler from the topic name.
        $handler = $map->fromTopic($message->attributes()['TopicName']);

        $cache = $this->container->get('cache');

        return (new SubscriberJob())
            ->setMessage($message)
            ->setClient($this->client)
            ->setContainer($this->container)
            ->setConnectionName($this->connectionName)
            ->setQueue($queue)
            ->setCache($cache)
            ->setHandler($handler);
    }

    /**
     * Pipe the messages through the pipeline.
     *
     * @param array $messages
     *
     * @return \Google\Cloud\PubSub\Message|null
     */
    protected function pipe(array $messages)
    {
        return (new Pipeline($this->container))->send($messages)->through($this->pipes)
            ->then(function ($message) {
                return $message;
            });
    }
}
