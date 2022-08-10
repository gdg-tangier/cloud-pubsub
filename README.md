## Laravel Cloud Pub/Sub.

<p align="center">
<img src="https://i.imgur.com/XyPYNEt.jpg"/>
</p>

<p align="center"> 
<img src="https://github.com/gdg-tangier/cloud-pubsub/actions/workflows/tests.yml/badge.svg">
<img src="https://github.styleci.io/repos/206420540/shield?branch=master">
<img src="https://poser.pugx.org/gdg-tangier/cloud-pubsub/v/stable.svg">
<img src="https://poser.pugx.org/gdg-tangier/cloud-pubsub/license.svg">
<img src="https://img.shields.io/badge/built%20for-laravel-blue.svg">    
</p>

### Why?

Build a scalable Laravel apps using event-driven microservices architecture (Pub/Sub), 
this tool adds the ability for your Laravel applications to communicate with each other using 
[Google Cloud Pub/Sub](https://cloud.google.com/pubsub/docs).

### Define your architecture.

First of all, you need to create subscriptions and topics in Google Cloud Platform, or you can use the cli.

- [Quickstart using the gcloud command-line tool](https://cloud.google.com/pubsub/docs/quickstart-cli)

### Installation.

- `composer require gdg-tangier/cloud-pubsub`

### Configuration.

- `config/queue.php`

You can define multiple subscribers (queue connections) config in `config/queue.php`, the app can subscribe to multiple subscriptions. 

Example.

```php
'pubsub' => [
      'driver' => 'pubsub',
      'queue'  => env('SUBSCRIPTION'),
      'credentials' => [
          'keyFilePath' => storage_path(env('PUBSUB_CLIENT_KEY')), // credentials file path '.json'
          'projectId'   => env('GCP_PROJECT_ID'),
      ],
  ],
```

- `config/pubsub.php`

Here where you can define your `subscriptions` jobs, events and topics mappings.

Example.

```php
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
```

### Create subscription class.

- `php artisan pubsub:make-subscriber <Name>`

A subscription class will be created at `app/Subscribers`

Example.

```php
<?php

namespace App\Subscribers;

use GDGTangier\PubSub\Subscriber\SubscriberJob;
use GDGTangier\PubSub\Subscriber\Traits\JobHandler;

class UserUpdated
{
    use JobHandler;

    /**
     * @var mixed
     */
    public $payload;

    /**
     * @var \GDGTangier\PubSub\Subscriber\SubscriberJob
     */
    public $job;

    /**
     * UserUpdated constructor.
     *
     * @param \GDGTangier\PubSub\Subscriber\SubscriberJob $job
     * @param $payload
     */
    public function __construct(SubscriberJob $job, $payload)
    {
        $this->job = $job;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 
    }
}
```

### Publishing data to the cloud.

- Using facade.

```php
use GDGTangier\PubSub\Publisher\Facades\PublisherFacade;

PublisherFacade::publish('MyData', 'event_name');
```

- Using service container.

```php
$publisher = app('gcloud.publisher.connection');

$publisher->publish('MyData', 'event_name');
```

- Using artisan command.

`php artisan pubsub:publish <message> <event>`

### Subscriptions worker.

- `php artisan pubsub:subscribe <connection>` 

Or alternatively you can run `php artisan queue:work <connection>`

> Note: To keep the queue:subscribe process running permanently in the background, 
> you should use a process monitor such as [supervisor](http://supervisord.org) to ensure that the queue worker does not stop running.

### Using GCP Pub/Sub emulator.

You need to install [GCP command line tool](https://cloud.google.com/sdk/gcloud/) and **Setup Topics/Subscriptions**

To use the emulator:

1. Go to the `AppServiceProvider@register` and add `PubSub::useEmulatorCredentials()`

2. Export the pubsub emulator host `export PUBSUB_EMULATOR_HOST=localhost:8085`

3. Run the emulator, `php artisan pubsub:emulator`

### Testing.

You need to install [GCP command line tool](https://cloud.google.com/sdk/gcloud/).

1. Run the pubsub emulator`./emulator.sh`
2. Export the pubsub emulator host `export PUBSUB_EMULATOR_HOST=localhost:8085`
3. Run `phpunit`
