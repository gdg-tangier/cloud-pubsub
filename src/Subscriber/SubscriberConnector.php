<?php

namespace GDGTangier\PubSub\Subscriber;

use Illuminate\Support\Arr;
use GDGTangier\PubSub\PubSub;
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
        $pubusbConfig = $this->getPubSubConfig();

        $credentials = PubSub::$runsEmulator ? ['projectId' => 'gdgtangier-23412'] :
            Arr::only($pubusbConfig['credentials'], ['keyFilePath', 'projectId']);

        return new SubscriberQueue(new PubSubClient($credentials));
    }

    /**
     * Get PubSub Configuration.
     *
     * @return array
     */
    protected function getPubSubConfig()
    {
        /** @var array $config */
        $config = app()['config']['pubsub'];

        return $config;
    }
}
