<?php

namespace GDGTangier\PubSub\Subscriber\Traits;

/**
 * Trait JobHandler
 *
 * @package  GDGTangier\PubSub\Subscriber\Traits
 * @property \GDGTangier\PubSub\Subscriber\SubscriberJob $job
 */
trait JobHandler
{
    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function fire()
    {
        if (!method_exists($this, 'handle')) {
            throw new \Exception(__CLASS__.'@handle does not exists!');
        }

        $container = $this->job->getContainer();
        $container->call([$this, 'handle']);

        $this->job->delete();
    }
}
