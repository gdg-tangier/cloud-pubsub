<?php

namespace GDGTangier\PubSub\Publisher;

use Illuminate\Support\Arr;
use Google\Cloud\PubSub\PubSubClient;

class PublisherConnector
{
    /**
     * Connect the publisher.
     *
     * @param array $config
     *
     * @return \GDGTangier\PubSub\Publisher\Publisher
     */
    public function connect($config)
    {
        $credentials = getenv('PUBSUB_EMULATOR_HOST') ? [] :
            Arr::only($config['credentials'], ['keyFilePath', 'projectId']);

        return new Publisher(new PubSubClient($credentials), new EventsMap($config['events']));
    }
}
