<?php

namespace Ob\Hex\Domain;

use Ob\Hex\Domain\Event\Event;
use Ob\Hex\Domain\Event\MeetingRoomWasCreated;
use Ob\Hex\Domain\Event\ReservationWasAdded;

class MeetingRoom
{
    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * @var int
     */
    private $capacity;

    /**
     * @var int
     */
    private $maximumDuration;

    /**
     * @var array
     */
    private $reservations = [];

    /**
     * @param int $capacity    The maximum numbers of seats in the room
     * @param int $maxDuration Maximum duration of a reservation in minutes
     *
     * @return static
     */
    public static function create($capacity, $maxDuration)
    {
        return new static([new MeetingRoomWasCreated($capacity, $maxDuration)]);
    }

    /**
     * @param Event[] $events
     */
    private function __construct($events)
    {
        foreach ($events as $event) {
            $this->apply($event);
        }
    }

    /**
     * @param Reservation $reservation
     */
    public function makeReservation(Reservation $reservation)
    {
        $this->ensureHasCapacity($reservation);
        $this->ensureDurationIsValid($reservation);

        $this->apply(new ReservationWasAdded($reservation));
    }

    /**
     * @return int
     */
    public function getNumberOfReservations()
    {
        return count($this->reservations);
    }

    /**
     * @param Reservation $reservation
     *
     * @throws \RuntimeException
     */
    private function ensureHasCapacity(Reservation $reservation)
    {
        if ($this->capacity < $reservation->getNumberOfAttendees()) {
            throw new \RuntimeException('Capacity exceeded');
        }
    }

    /**
     * @param Reservation $reservation
     *
     * @throws \RuntimeException
     */
    private function ensureDurationIsValid(Reservation $reservation)
    {
        if ($this->maximumDuration < $reservation->getDuration()) {
            throw new \RuntimeException('Maximum reservation duration exceeded');
        }
    }

    /**
     * @param Event $event
     */
    private function apply(Event $event)
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
     * @param MeetingRoomWasCreated $event
     */
    private function applyMeetingRoomWasCreated(MeetingRoomWasCreated $event)
    {
        $this->capacity        = $event->capacity;
        $this->maximumDuration = $event->maxDuration;
    }

    /**
     * @param ReservationWasAdded $event
     */
    private function applyReservationWasAdded(ReservationWasAdded $event)
    {
        $this->reservations[] = $event->reservation;
    }
}
