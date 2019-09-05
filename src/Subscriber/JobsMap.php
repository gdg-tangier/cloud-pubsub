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
     * @throws \GDGTangier\PubSub\Subscriber\Exceptions\SubscriberJobNotFound
     *
     * @return string|bool
     */
    public function fromTopic($topic)
    {
        if ($job = $this->jobs->search($topic)) {
            return $job;
        }

        throw new SubscriberJobNotFound('Subscriber job not found!');
    }
}
