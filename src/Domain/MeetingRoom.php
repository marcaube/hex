<?php

namespace Ob\Hex\Domain;

use Ob\Hex\Domain\Events\MeetingRoomWasCreated;
use Ob\Hex\Domain\Events\ReservationWasAdded;
use Ob\Hex\EventSourcing\EventSourcedEntity;

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
        $this->ensureStartDoesNotOverlapWithAnotherReservation($reservation);
        $this->ensureEndDoesNotOverlapWithAnotherReservation($reservation);
        $this->ensureNotInsideAnotherReservation($reservation);
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
     * @param ScheduleRenderer $renderer
     *
     * @return mixed
     */
    public function renderScheduleWith(ScheduleRenderer $renderer)
    {
        return $renderer->render($this->reservations);
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
    private function ensureStartDoesNotOverlapWithAnotherReservation(Reservation $reservation)
    {
        foreach ($this->reservations as $reservedTimeSlot) {
            if ($reservation->getStartDate() > $reservedTimeSlot->getStartDate() && $reservation->getStartDate() < $reservedTimeSlot->getEndDate()) {
                throw new \RuntimeException('Time slot unavailable');
            }
        }
    }

    /**
     * @param Reservation $reservation
     *
     * @throws \RuntimeException
     */
    private function ensureEndDoesNotOverlapWithAnotherReservation(Reservation $reservation)
    {
        foreach ($this->reservations as $reservedTimeSlot) {
            if ($reservation->getEndDate() > $reservedTimeSlot->getStartDate() && $reservation->getEndDate() < $reservedTimeSlot->getEndDate()) {
                throw new \RuntimeException('Time slot unavailable');
            }
        }
    }

    /**
     * @param Reservation $reservation
     *
     * @throws \RuntimeException
     */
    private function ensureNotInsideAnotherReservation(Reservation $reservation)
    {
        foreach ($this->reservations as $reservedTimeSlot) {
            if ($reservation->getStartDate() <= $reservedTimeSlot->getStartDate() && $reservation->getEndDate() >= $reservedTimeSlot->getEndDate()) {
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
     * @param MeetingRoomWasCreated $change
     */
    protected function applyMeetingRoomWasCreated(MeetingRoomWasCreated $change)
    {
        $this->capacity        = $change->capacity;
        $this->maximumDuration = $change->maxDuration;
    }

    /**
     * @param ReservationWasAdded $change
     */
    protected function applyReservationWasAdded(ReservationWasAdded $change)
    {
        // This key ensure the reservations are sorted
        $date = $change->reservation->getStartDate()->format('YmdHis');

        $this->reservations[$date] = $change->reservation;
    }
}
