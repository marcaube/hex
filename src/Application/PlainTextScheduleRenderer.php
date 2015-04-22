<?php

namespace Ob\Hex\Application;

use Ob\Hex\Domain\Reservation;
use Ob\Hex\Domain\ScheduleRenderer;

final class PlainTextScheduleRenderer implements ScheduleRenderer
{
    /**
     * @param Reservation[] $reservations
     *
     * @return string
     */
    public function render(array $reservations)
    {
        $text = '';

        foreach ($reservations as $reservation) {
            $text .= $reservation->getStartDate()->format('Y-m-d H\hi');
            $text .= ' to ';
            $text .= $reservation->getEndDate()->format('H\hi');
            $text .= ' : ';
            $text .= $reservation->getNumberOfAttendees() . ' attendee(s),';
            $text .= ' organized by ' . $reservation->getOrganizer();
            $text .= PHP_EOL;
        }

        return $text;
    }
}
