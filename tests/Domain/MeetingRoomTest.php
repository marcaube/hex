<?php

namespace Ob\Hex\Tests\Domain;

use Mockery as m;
use Ob\Hex\Domain\MeetingRoom;

/**
 * @covers Ob\Hex\Domain\MeetingRoom
 * @covers Ob\Hex\Domain\Event\MeetingRoomWasCreated
 * @covers Ob\Hex\Domain\Event\ReservationWasAdded
 *
 * @uses Ob\Hex\Domain\EventSourcedEntity
 */
class MeetingRoomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    private $capacityLimit;

    /**
     * @var int
     */
    private $maxDuration;

    /**
     * @var MeetingRoom
     */
    private $meetingRoom;

    public function setUp()
    {
        $this->capacityLimit = 3;
        $this->maxDuration   = 60 * 3;
        $this->meetingRoom   = MeetingRoom::create($this->capacityLimit, $this->maxDuration);
    }

    public function testCanBeCreated()
    {
        $this->assertInstanceOf(MeetingRoom::class, $this->meetingRoom);
    }

    public function testInitiallyHasNoReservations()
    {
        $this->assertEquals(0, $this->meetingRoom->getNumberOfReservations());
    }

    public function testReservationsCanBeAdded()
    {
        $reservation1 = $this->createReservation($this->capacityLimit, $this->maxDuration, new \DateTimeImmutable(), new \DateTimeImmutable('+1 hour'));
        $reservation2 = $this->createReservation($this->capacityLimit, $this->maxDuration, new \DateTimeImmutable('+1 hour'), new \DateTimeImmutable('+1 hour 30 minutes'));

        $this->meetingRoom->makeReservation($reservation1);
        $this->meetingRoom->makeReservation($reservation2);
        $this->assertEquals(2, $this->meetingRoom->getNumberOfReservations());
    }

    public function testHasALimitedCapacity()
    {
        $reservation = $this->createReservation($this->capacityLimit + 1, $this->maxDuration);

        $this->setExpectedException('\RuntimeException');
        $this->meetingRoom->makeReservation($reservation);
    }

    public function testReservationsHaveAMaximumDuration()
    {
        $reservation = $this->createReservation(1, 60 * 4);

        $this->setExpectedException('\RuntimeException');
        $this->meetingRoom->makeReservation($reservation);
    }

    public function testReservationsCanNotOverlap()
    {
        $reservation1 = $this->createReservation(
            $this->capacityLimit,
            $this->maxDuration,
            new \DateTimeImmutable('now'),
            new \DateTimeImmutable('+1 hour')
        );

        $reservation2 = $this->createReservation(
            $this->capacityLimit,
            $this->maxDuration,
            new \DateTimeImmutable('+30 minutes'),
            new \DateTimeImmutable('+2 hours')
        );

        $this->setExpectedException('\RuntimeException');
        $this->meetingRoom->makeReservation($reservation1);
        $this->meetingRoom->makeReservation($reservation2);
    }

    public function testReservationCanBeMadeUpTo7DaysInAdvance()
    {
        $startDate   = new \DateTimeImmutable('+8 days');
        $endDate     = $startDate->modify('+1 hour');
        $reservation = $this->createReservation($this->capacityLimit, $this->maxDuration, $startDate, $endDate);

        $this->setExpectedException('\RuntimeException');
        $this->meetingRoom->makeReservation($reservation);
    }

    private function createReservation($attendees, $duration, \DateTimeImmutable $startDate = null, \DateTimeImmutable $endDate = null)
    {
        if (!$startDate) {
            $startDate = new \DateTimeImmutable();
        }

        if (!$endDate) {
            $endDate = new \DateTimeImmutable();
        }

        $reservation = m::mock('Ob\Hex\Domain\Reservation');
        $reservation->shouldReceive('getNumberOfAttendees')->andReturn($attendees);
        $reservation->shouldReceive('getDuration')->andReturn($duration);
        $reservation->shouldReceive('getStartDate')->andReturn($startDate);
        $reservation->shouldReceive('getEndDate')->andReturn($endDate);

        return $reservation;
    }
}
