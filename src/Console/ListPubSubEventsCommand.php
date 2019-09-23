<?php

namespace GDGTangier\PubSub\Console;

class ListPubSubEventsCommand extends ListPubSubConfig
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pubsub:events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List events';

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
        $items = config('pubsub.events', []);
        $this->list(['Event name', 'Topic name'], $items);
    }
}
