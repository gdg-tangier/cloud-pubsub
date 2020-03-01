<?php

namespace GDGTangier\PubSub\Subscriber;

use Illuminate\Queue\Jobs\Job;
use Google\Cloud\PubSub\Message;
use Illuminate\Cache\CacheManager;
use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Queue\ManuallyFailedException;
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return int
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
     * Delete the job, call the "failed" method, and raise the failed job event.
     *
     * @param \Throwable|null $e
     *
     * @return void
     */
    public function fail($e = null)
    {
        $this->markAsFailed();

        if ($this->isDeleted()) {
            return;
        }

        try {
            if (config('pubsub.acknowledge_if_failed')) {
                $this->delete();
            }

            $this->failed($e);
        } finally {
            $this->cache->forget($this->message->id());
            $this->resolve(Dispatcher::class)->dispatch(new JobFailed(
                $this->connectionName, $this, $e ?: new ManuallyFailedException()
            ));
        }
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
    public function setMessage(Message $message): self
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
    public function setClient(PubSubClient $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Set the underlying container.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public function setContainer(Container $container): self
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
    public function setConnectionName(string $connectionName): self
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
    public function setQueue(string $queue): self
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
    public function setHandler(string $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Set the cache instance.
     *
     * @param \Illuminate\Cache\CacheManager $cache
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public function setCache(CacheManager $cache): self
    {
        $this->cache = $cache;

        return $this;
    }
}
