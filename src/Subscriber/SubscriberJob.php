<?php

namespace GDGTangier\PubSub\Subscriber;

use Illuminate\Queue\Jobs\Job;
use Google\Cloud\PubSub\Message;
use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;

class SubscriberJob extends Job implements JobContract
{
    /**
     * Message.
     *
     * @var \Google\Cloud\PubSub\Message
     */
    protected $message;

    /**
     * Google Cloud PubSub client.
     *
     * @var \Google\Cloud\PubSub\PubSubClient
     */
    protected $client;

    /**
     * The underlying application container.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Queue Connection.
     *
     * @var string
     */
    protected $connectionName;

    /**
     * Queue.
     *
     * @var string
     */
    protected $queue;

    /**
     * Job Handler.
     *
     * @var string
     */
    protected $handler;

    /**
     * Cache Repository.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Fire the Job.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function fire(): void
    {
        $payload = $this->payload();

        $this->instance = ($this->container->make($payload['job'],
            [
                'job'     => $this,
                'payload' => $payload['data'],
            ]
        ));

        $this->instance->fire();
    }

    /**
     * Release the message.
     *
     * @param int $delay
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function release($delay = 0): void
    {
        if ($this->cache->has($this->message->id())) {
            $this->cache->increment($this->message->id());
        }
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId(): string
    {
        return $this->message->id();
    }

    /**
     * Get the raw body of the job.
     *
     * @return string
     */
    public function getRawBody(): string
    {
        return $this->message->data();
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function attempts(): int
    {
        /** @var int $attempts */
        $attempts = $this->cache->get($this->message->id());

        return $attempts;
    }

    /**
     * Delete the job.
     *
     * @reeturn void
     */
    public function delete(): void
    {
        parent::delete();

        $this->cache->forget($this->message->id());

        $this->client->subscription($this->queue)->acknowledge($this->message);
    }

    /**
     * Get the body of the underlying message.
     *
     * @return array
     */
    public function payload(): array
    {
        return [
            'data' => $this->getRawBody(),
            'job'  => $this->handler,
        ];
    }

    /**
     * @return mixed|null
     */
    public function getResolvedInstance()
    {
        return $this->instance;
    }

    /**
     * Set the pulled message from the queue.
     *
     * @param \Google\Cloud\PubSub\Message $message
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public function setMessage(Message $message): SubscriberJob
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set PubSub client.
     *
     * @param \Google\Cloud\PubSub\PubSubClient $client
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public function setClient(PubSubClient $client): SubscriberJob
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Set the underlying container.
     *
     * @param \Illuminate\Container\Container $container
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public function setContainer(Container $container): SubscriberJob
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Set the underlying connection name.
     *
     * @param string $connectionName
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public function setConnectionName(string $connectionName): SubscriberJob
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    /**
     * Set the underlying queue name.
     *
     * @param string $queue
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public function setQueue(string $queue): SubscriberJob
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set the underlying job handler.
     *
     * @param string $handler
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public function setHandler(string $handler): SubscriberJob
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Set the cache instance.
     *
     * @param \Illuminate\Contracts\Cache\Repository $cache
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public function setCache(Repository $cache): SubscriberJob
    {
        $this->cache = $cache;

        return $this;
    }
}
