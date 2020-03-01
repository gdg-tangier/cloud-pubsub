<?php

namespace GDGTangier\PubSub\Subscriber\Pipeline;

class HasTopicName
{
    /**
     * Handle the incoming message.
     *
     * @param \Google\Cloud\PubSub\Message $message
     * @param \Closure                     $next
     *
     * @return mixed|null
     */
    public function handle($message, \Closure $next)
    {
        if (!array_key_exists('TopicName', $message->attributes())) {
            return;
        }

        return $next($message);
    }
}
