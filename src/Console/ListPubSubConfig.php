<?php

namespace GDGTangier\PubSub\Console;

use Illuminate\Console\Command;

class ListPubSubConfig extends Command
{
    /**
     * List the Pub/Sub configuration as table.
     *
     * @param array $headers
     * @param array $items
     *
     * @return void
     */
    protected function list(array $headers, array $items)
    {
        $rows = collect($items)->map(function ($value, $key) {
            return [$key, $value];
        });

        $this->table($headers, $rows->toArray(), 'box');
    }
}
