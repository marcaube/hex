<?php

namespace Ob\Hex\Domain;

interface ScheduleRenderer
{
    /**
     * @param Reservation[] $reservations
     */
    public function render(array $reservations);
}
