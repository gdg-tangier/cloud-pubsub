<?php

namespace GDGTangier\PubSub\Publisher;

use Illuminate\Support\Arr;
use GDGTangier\PubSub\PubSub;
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
        $credentials = PubSub::$runsEmulator ? ['projectId' => 'gdgtangier-23412'] :
            Arr::only($config['credentials'], ['keyFilePath', 'projectId']);

        return new Publisher(new PubSubClient($credentials), new EventsMap($config['events']));
    }
}
