<?php

namespace GDGTangier\PubSub\Subscriber;

use GDGTangier\PubSub\Subscriber\Exceptions\SubscriberJobNotFound;

class JobsMap
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $jobs;

    /**
     * JobsMap constructor.
     *
     * @param array $jobs
     */
    public function __construct(array $jobs)
    {
        $this->jobs = collect($jobs);
    }

    /**
     * Get the job class from a given topic name.
     *
     * @param $topic
     *
     * @return string|boolean
     * @throws \GDGTangier\PubSub\Subscriber\Exceptions\SubscriberJobNotFound
     */
    public function fromTopic($topic)
    {
        if ($job = $this->jobs->search($topic))
            return $job;

        Throw new SubscriberJobNotFound('Subscriber job not found!');
    }
}
