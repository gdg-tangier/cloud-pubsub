<?php

namespace GDGTangier\PubSub\Console;

use Illuminate\Console\Command;

class StartEmulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pubsub:emulator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start PubSub Emulator';

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
        if (!getenv('PUBSUB_EMULATOR_HOST')) {
            $this->error('Cannot start emulator, please export the emulator host, try [export PUBSUB_EMULATOR_HOST=localhost:8085]');

            return;
        }

        $this->info('PUBSUB_EMULATOR_HOST: checked');

        exec(__DIR__.'/../../emulator.sh');
    }
}
