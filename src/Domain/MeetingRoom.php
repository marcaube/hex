<?php

namespace Ob\Hex\Domain;

class MeetingRoom
{
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
     */
    public function __construct($capacity, $maxDuration)
    {
        $this->capacity        = $capacity;
        $this->maximumDuration = $maxDuration;
    }

    /**
     * @param Reservation $reservation
     */
    public function makeReservation(Reservation $reservation)
    {
        $this->ensureHasCapacity($reservation);
        $this->ensureDurationIsValid($reservation);

        $this->reservations[] = $reservation;
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

    private function ensureDurationIsValid(Reservation $reservation)
    {
        if ($this->maximumDuration < $reservation->getDuration()) {
            throw new \RuntimeException('Maximum reservation duration exceeded');
        }
    }
}
