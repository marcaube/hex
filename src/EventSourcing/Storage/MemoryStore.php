<?php

namespace Ob\Hex\EventSourcing\Storage;

class MemoryStore implements EventStoreInterface
{
    private $events = [];

    /**
     * {@inheritdoc}
     */
    public function read($id)
    {
        if (!isset($this->events[$id])) {
            throw new \Exception(sprintf('Event stream not found for id %s', $id));
        }

        return $this->events[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function write($id, $events)
    {
        if (!isset($this->events[$id])) {
            $this->events[$id] = [];
        }

        foreach ($events as $event) {
            $this->events[$id][] = $event;
        }
    }
}
