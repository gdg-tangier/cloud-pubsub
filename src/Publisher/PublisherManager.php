<?php

namespace GDGTangier\PubSub\Publisher;

class PublisherManager
{
    /**
     * The underlying container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * The underlying publisher instance.
     *
     * @var \GDGTangier\PubSub\Publisher\Publisher
     */
    protected $connection;

    /**
     * PublisherManager constructor.
     *
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Establish a new publisher connection.
     *
     * @return \GDGTangier\PubSub\Publisher\Publisher
     */
    public function connection()
    {
        if (!$this->connection)
            $this->connection = $this->resolve();

        return $this->connection;
    }

    /**
     * Resolve the Publisher.
     *
     * @return \GDGTangier\PubSub\Publisher\Publisher
     */
    protected function resolve()
    {
        $config = $this->getConfig();
        return (new PublisherConnector)->connect($config);
    }

    /**
     * Get PubSub configuration.
     *
     * @return array|null
     */
    protected function getConfig()
    {
        return $this->app['config']['pubsub'];
    }
}
