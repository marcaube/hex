<?php

namespace Ob\Hex\Domain;

class Reservation
{
    /**
     * @var \DateTimeImmutable
     */
    private $startDate;

    /**
     * @var \DateTimeImmutable
     */
    private $endDate;

    /**
     * @var Email
     */
    private $organizer;

    /**
     * @var array
     */
    private $attendeesAdded = [];

    /**
     * @var array
     */
    private $attendeesRemoved = [];

    /**
     * @param \DateTimeImmutable $start
     * @param \DateTimeImmutable $end
     * @param Email              $organizer
     */
    public function __construct(\DateTimeImmutable $start, \DateTimeImmutable $end, Email $organizer)
    {
        $this->startDate = $start;
        $this->endDate   = $end;
        $this->organizer = $organizer;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return \DateInterval
     */
    public function getDuration()
    {
        $interval = $this->startDate->diff($this->endDate);

        return $interval->h * 60 + $interval->i;
    }

    /**
     * @return Email
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * @return int
     */
    public function getNumberOfAttendees()
    {
        return count($this->getAttendees());
    }

    /**
     * @return Email[]
     */
    private function getAttendees()
    {
        $attendees = $this->attendeesAdded;

        foreach ($this->attendeesRemoved as $key => $attendee) {
            if (in_array($attendee, $attendees)) {
                unset($attendees[$key]);
            }
        }

        return $attendees;
    }

    /**
     * @param Email $attendee
     */
    public function addAttendee(Email $attendee)
    {
        $this->attendeesAdded[] = $attendee;
    }

    /**
     * @param Email $attendee
     */
    public function removeAttendee(Email $attendee)
    {
        $this->attendeesRemoved[] = $attendee;
    }
}
