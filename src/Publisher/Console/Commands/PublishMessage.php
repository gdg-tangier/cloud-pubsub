<?php

namespace GDGTangier\PubSub\Publisher\Console\Commands;

use Illuminate\Console\Command;
use GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound;

class PublishMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pubsub:publish
                            {message : Message to be published}
                            {event : Event or topic name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a message';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $bar = $this->initProgressBar();

        /** @var \GDGTangier\PubSub\Publisher\Publisher $publisher */
        $publisher = app('gcloud.publisher.connection');

        $message = $this->argument('message');

        $event = $this->argument('event');

        try {
            $publisher->publish($message, $event);
            $bar->advance();
        } catch (TopicNotFound $e) {
            $this->error($e->getMessage());
        }

        $bar->finish();
        $this->line('');
        $this->info("Message Sent! \xF0\x9F\x9A\x80 \xF0\x9F\x92\xA5");
    }

    /**
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    protected function initProgressBar()
    {
        $bar = $this->output->createProgressBar(1);
        $bar->setBarCharacter("<fg=magenta>=</>");
        $bar->setProgressCharacter("\xF0\x9F\x9A\x80");

        return $bar;
    }
}
