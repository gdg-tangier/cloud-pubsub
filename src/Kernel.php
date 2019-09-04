<?php

namespace GDGTangier\PubSub;


class Kernel
{
    public $topics = [

    ];

    public $subscribers = [

    ];

    public function bootstrap()
    {
        config(['pubsub.publisher' => $this->topics]);
        config(['pubsub.subscriber' => $this->subscribers ]);
    }
}