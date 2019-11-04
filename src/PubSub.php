<?php

namespace GDGTangier\PubSub;

class PubSub
{
    /**
     * Indicate if cloud-pubsub runs emulator.
     *
     * @var bool
     */
    public static $runsEmulator = false;

    /**
     * Use Emulator Credentials.
     *
     * @return void
     */
    public static function useEmulatorCredentials()
    {
        static::$runsEmulator = true;
    }
}
