<?php

namespace Ob\Hex\Domain;

class Event
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
     * @return int
     */
    public function getNumberOfAttendees()
    {
        $count = count($this->attendeesAdded);

        foreach ($this->attendeesRemoved as $attendee) {
            if (in_array($attendee, $this->attendeesAdded)) {
                $count -= 1;
            }
        }

        return $count;
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
