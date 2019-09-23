<?php

namespace GDGTangier\PubSub\Console;

class ListSubscriptionsCommand extends ListPubSubConfig
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pubsub:subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List subscriptions';

    /**
     * ListPubSubEventsCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $items = config('pubsub.subscriptions', []);

        $this->list(['Subscription class', 'Topic name'], $items);
    }
}
