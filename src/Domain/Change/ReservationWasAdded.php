<?php

namespace Ob\Hex\Domain\Change;

use Ob\Hex\Domain\Reservation;

class ReservationWasAdded
{
    public $reservation;

    /**
     * @param Reservation $reservation
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }
}
