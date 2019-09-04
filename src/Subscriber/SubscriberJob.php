<?php

namespace GDGTangier\PubSub\Subscriber;

use Illuminate\Queue\Jobs\Job;
use Google\Cloud\PubSub\Message;
use Google\Cloud\PubSub\PubSubClient;
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
     * @var \Illuminate\Contracts\Foundation\Application
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
     * SubscriberJob constructor.
     *
     * @param \Google\Cloud\PubSub\Message $message
     * @param \Google\Cloud\PubSub\PubSubClient $client
     * @param \Illuminate\Contracts\Container\Container $container
     * @param string $connectionName
     * @param string $queue
     * @param string $handler
     */
    public function __construct(Message $message, PubSubClient $client,
                                Container $container,
                                string $connectionName,
                                string $queue,
                                string $handler)
    {
        $this->queue = $queue;
        $this->client = $client;
        $this->handler = $handler;
        $this->message = $message;
        $this->container = $container;
        $this->connectionName = $connectionName;

        $this->cache = $this->container->get('cache');
    }

    /**
     * Fire the Job.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function fire()
    {
        $payload = $this->payload();

        $this->instance = ($this->container->make($payload['job'],
            [
                'job'     => $this,
                'payload' => $payload['data']
            ]
        ));

        $this->instance->fire();
    }

    /**
     * Release the message.
     *
     * @param int $delay
     */
    public function release($delay = 0)
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
    public function getJobId()
    {
        return $this->message->id();
    }

    /**
     * Get the raw body of the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->message->data();
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        /** @var integer $attempts */
        $attempts = $this->cache->get($this->message->id());

        return $attempts;
    }

    /**
     * Delete the job.
     *
     * @reeturn void
     */
    public function delete()
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
    public function payload()
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
}
