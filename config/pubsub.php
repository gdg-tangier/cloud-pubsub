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
     * Here where you can define events name with a topic.
     */
    'events'      => [
        'event_name' => '__YOUR_TOPIC_NAME__',
    ],

    /*
     * Here where you can tie the subscriptions classes (jobs) to topics.
     */
    'subscriptions'        => [
        \App\PubSub\DummyJob::class => '__YOUR_TOPIC_NAME__',
    ],
];
