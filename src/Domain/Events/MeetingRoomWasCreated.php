<?php

namespace Ob\Hex\Domain\Events;

class MeetingRoomWasCreated
{
    public $capacity;

    public $maxDuration;

    /**
     * @param int $capacity
     * @param int $maxDuration
     */
    public function __construct($capacity, $maxDuration)
    {
        $this->capacity    = $capacity;
        $this->maxDuration = $maxDuration;
    }
}
