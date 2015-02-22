<?php

namespace Ob\Hex\Domain;

abstract class EventSourcedEntity
{
    protected $changes = [];

    public static function createFromChanges(array $changes)
    {
        return new static($changes);
    }

    protected function __construct(array $changes)
    {
        foreach ($changes as $change) {
            $this->apply($change);
        }
    }

    /**
     * @param mixed $change
     */
    protected function apply($change)
    {
        $classParts = explode('\\', get_class($change));
        $method     = 'apply' . end($classParts);

        if (!method_exists($this, $method)) {
            return;
        }

        $this->changes[] = $change;
        $this->$method($change);
    }

    /**
     * @return array
     */
    public function getChanges()
    {
        $changes       = $this->changes;
        $this->changes = [];

        return $changes;
    }
}
