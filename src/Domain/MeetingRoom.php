<?php

namespace Ob\Hex\Domain;

use Ob\Hex\Domain\Event\MeetingRoomWasCreated;
use Ob\Hex\Domain\Event\ReservationWasAdded;

class MeetingRoom extends EventSourcedEntity
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
     * @var Reservation[]
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
     * @param Reservation $reservation
     */
    public function makeReservation(Reservation $reservation)
    {
        $this->ensureHasCapacity($reservation);
        $this->ensureDurationIsValid($reservation);
        $this->ensureDoesNotOverlap($reservation);
        $this->ensureInsideReservationPeriod($reservation);

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
     * @param Reservation $reservation
     *
     * @throws \RuntimeException
     */
    private function ensureDoesNotOverlap(Reservation $reservation)
    {
        foreach ($this->reservations as $reservedTimeSlot) {
            // The start date is inside a reservation time slot
            $startOverlaps = $reservation->getStartDate() > $reservedTimeSlot->getStartDate() && $reservation->getStartDate() < $reservedTimeSlot->getEndDate();

            // The end date is inside a reservation time slot
            $endOverlaps = $reservation->getEndDate() > $reservedTimeSlot->getStartDate() && $reservation->getEndDate() < $reservedTimeSlot->getEndDate();

            // A reservation is inside the requested time slot
            $includesReservation = $reservation->getStartDate() <= $reservedTimeSlot->getStartDate() && $reservation->getEndDate() >= $reservedTimeSlot->getEndDate();

            if ($startOverlaps || $endOverlaps || $includesReservation) {
                throw new \RuntimeException('Time slot unavailable');
            }
        }
    }

    /**
     * @param Reservation $reservation
     *
     * @throws \RuntimeException
     */
    private function ensureInsideReservationPeriod(Reservation $reservation)
    {
        $now = new \DateTimeImmutable();

        if ($now->diff($reservation->getStartDate())->d > 7) {
            throw new \RuntimeException('A reservation can not be created more than 7 days in advance');
        }
    }

    /**
     * @param MeetingRoomWasCreated $event
     */
    protected function applyMeetingRoomWasCreated(MeetingRoomWasCreated $event)
    {
        $this->capacity        = $event->capacity;
        $this->maximumDuration = $event->maxDuration;
    }

    /**
     * @param ReservationWasAdded $event
     */
    protected function applyReservationWasAdded(ReservationWasAdded $event)
    {
        $this->reservations[] = $event->reservation;
    }
}
