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
}
