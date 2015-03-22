<?php

namespace Ob\Hex\EventSourcing\Storage;

interface EventStoreInterface
{
    /**
     * Load events from storage.
     *
     * @param mixed $id Entity/Aggregate identifier
     *
     * @return array
     */
    public function read($id);

    /**
     * Append events to storage.
     *
     * @param mixed $id     Entity/Aggregate identifier
     * @param array $events
     */
    public function write($id, $events);
}
