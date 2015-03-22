<?php

namespace Ob\Hex\EventSourcing;

abstract class EventSourcedEntity
{
    /**
     * @var array
     */
    protected $events = [];

    /**
     * @param array $events
     *
     * @return static
     */
    public static function createFromEvents(array $events)
    {
        $entity = new static($events);

        // Release events so they are not persisted multiple times
        $entity->getEvents();

        return $entity;
    }

    /**
     * @param array $events
     */
    protected function __construct(array $events)
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

    /**
     * @return array
     */
    public function getEvents()
    {
        $events       = $this->events;
        $this->events = [];

        return $events;
    }
}
