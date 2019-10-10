<?php

namespace GDGTangier\PubSub\Subscriber\Pipeline;

class MessageCaching
{
    /**
     * Handle the incoming message.
     *
     * @param \Google\Cloud\PubSub\Message $message
     * @param \Closure                     $next
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Illuminate\Container\EntryNotFoundException
     *
     * @return mixed
     */
    public function handle($message, \Closure $next)
    {
        /** @var \Illuminate\Contracts\Cache\Repository $cache */
        $cache = app()->get('cache');

        if (!$cache->has($message->id())) {
            $cache->forever($message->id(), 1);
        }

        return $next($message);
    }
}
