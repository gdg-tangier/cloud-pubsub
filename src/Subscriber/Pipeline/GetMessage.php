<?php

namespace GDGTangier\PubSub\Subscriber\Pipeline;

class GetMessage
{
    /**
     * Handle the incoming message.
     *
     * @param array    $messages
     * @param \Closure $next
     *
     * @return mixed|null
     */
    public function handle($messages, \Closure $next)
    {
        if (count($messages) == 0) {
            return;
        }

        /** @var \Google\Cloud\PubSub\Message $message */
        $message = $messages[0];

        return $next($message);
    }
}
