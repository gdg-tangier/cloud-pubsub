<?php

return [
    /*
     * GCP Credentials.
     */
    'credentials' => [
        'keyFilePath' => storage_path(env('PUBSUB_CLIENT_KEY', 'client')),
        'projectId'   => env('GCP_PROJECT_ID'),
    ],

    /*
     * Here where you map events name with Google Pub/Sub topics.
     *
     * Means, map each event name to specific Google Pub/Sub topic.
     */
    'events'      => [
        'event_name' => '__YOUR_TOPIC_NAME__',
    ],

    /*
     * Here where you can tie the subscription classes (jobs) to topics.
     *
     * Means, map each subscription job to a specific Google pubsub topic.
     * The subscription job is responsible for handling the incoming data
     * from a Google Pub/Sub topic.
     */
    'subscriptions'        => [
        \App\PubSub\DummyJob::class => '__YOUR_TOPIC_NAME__',
    ],
];
