<?php

namespace Ob\Hex\Domain;

abstract class EventSourcedEntity
{
    protected $events = [];

    /**
     * @param array $events
     */
    protected function __construct($events)
    {
        foreach ($events as $event) {
            $this->apply($event);
        }
    }

    /**
     * @param mixed $event
     */
    protected function apply($event)
    {
        $classParts = explode('\\', get_class($event));
        $method     = 'apply' . end($classParts);

        if (!method_exists($this, $method)) {
            return;
        }

        $this->events[] = $event;
        $this->$method($event);
    }
}
