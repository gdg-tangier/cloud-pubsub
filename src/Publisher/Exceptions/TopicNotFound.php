<?php

namespace GDGTangier\PubSub\Publisher\Exceptions;

class TopicNotFound extends \Exception
{
    /**
     * TopicNotFound constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
