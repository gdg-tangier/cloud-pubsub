<?php

namespace GDGTangier\PubSub\Subscriber;

use Illuminate\Support\Arr;
use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Queue\Connectors\ConnectorInterface;

class SubscriberConnector implements ConnectorInterface
{
    /**
     * Establish new PubSub connection.
     *
     * @param array $config
     *
     * @return \GDGTangier\PubSub\Subscriber\SubscriberQueue|\Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $config = getenv('PUBSUB_EMULATOR_HOST') ? [] :
            Arr::only($config['credentials'], ['keyFilePath', 'projectId']);

        return new SubscriberQueue(new PubSubClient($config));
    }
}
