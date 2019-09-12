<?php

namespace GDGTangier\PubSub\Publisher;

use Illuminate\Support\Collection;
use GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound;

class EventsMap
{
    /**
     * @var array
     */
    protected $events;

    /**
     * EventsMap constructor.
     *
     * @param array $events
     */
    public function __construct(array $events)
    {
        $this->events = $events;
    }

    /**
     * Get the topic name form a given event.
     *
     * @param string $event
     *
     * @return mixed
     * @throws \GDGTangier\PubSub\Publisher\Exceptions\TopicNotFound
     */
    public function formEvent($event)
    {
        $events = $this->toCollection();

        if ($events->search($event)) {
            return $event;
        }

        if ($topic = $events->get($event)) {
            return $topic;
        }

        throw new TopicNotFound("Event [{$event}] Not Found");
    }

    /**
     * Get events as a collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function toCollection()
    {
        return new Collection($this->events);
    }
}
