<?php

namespace Ob\Hex\Domain\Event;

class MeetingRoomWasCreated implements Event
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
