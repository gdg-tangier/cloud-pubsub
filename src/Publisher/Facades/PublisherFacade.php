<?php

namespace GDGTangier\PubSub\Publisher\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array publish(string $data, string $topicName, array $attributes = [])
 * @method static \Google\Cloud\PubSub\PubSubClient getClient()
 */
class PublisherFacade extends Facade
{
    /**
     * Get facade accessor.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'gcloud.publisher.connection';
    }
}
