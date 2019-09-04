<?php

namespace GDGTangier\PubSub\Subscriber\Console\Commands;

use Illuminate\Console\Command;

class SubscribeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pubsub:subscribe
                            {connection : The name of the queue connection to work}
                            {--queue= : The names of the queues to work}
                            {--once : Only process the next job on the queue}
                            {--stop-when-empty : Stop when the queue is empty}
                            {--force : Force the worker to run even in maintenance mode}
                            {--memory=128 : The memory limit in megabytes}
                            {--sleep=3 : Number of seconds to sleep when no job is available}
                            {--timeout=60 : The number of seconds a child process can run}
                            {--tries=0 : Number of times to attempt a job before logging it failed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to Google Cloud PubSub messages';

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
        $this->call('queue:work', $this->getArgumentsAndOptions());
    }

    /**
     * @return array
     */
    protected function getArgumentsAndOptions()
    {
        return [
            'connection'        => $this->argument('connection'),
            '--queue'           => $this->option('queue'),
            '--once'            => $this->option('once'),
            '--stop-when-empty' => $this->option('stop-when-empty'),
            '--force'           => $this->option('force'),
            '--memory'          => $this->option('memory'),
            '--sleep'           => $this->option('sleep'),
            '--timeout'         => $this->option('timeout'),
            '--tries'           => $this->option('tries'),
        ];
    }
}
