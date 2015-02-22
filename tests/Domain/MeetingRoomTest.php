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

    public function testReservationCanBeAdded()
    {
        $reservation = m::mock('Ob\Hex\Domain\Reservation');
        $reservation->shouldReceive('getNumberOfAttendees')->andReturn($this->capacityLimit);
        $reservation->shouldReceive('getDuration')->andReturn($this->maxDuration);

        $this->meetingRoom->makeReservation($reservation);
        $this->assertEquals(1, $this->meetingRoom->getNumberOfReservations());
    }

    public function testHasALimitedCapacity()
    {
        $reservation = m::mock('Ob\Hex\Domain\Reservation');
        $reservation->shouldReceive('getNumberOfAttendees')->andReturn($this->capacityLimit + 1);
        $reservation->shouldReceive('getDuration')->andReturn($this->maxDuration);

        $this->setExpectedException('\RuntimeException');
        $this->meetingRoom->makeReservation($reservation);
    }

    public function testReservationHasMaximumDuration()
    {
        $reservation = m::mock('Ob\Hex\Domain\Reservation');
        $reservation->shouldReceive('getNumberOfAttendees')->andReturn(1);
        $reservation->shouldReceive('getDuration')->andReturn(60 * 4);

        $this->setExpectedException('\RuntimeException');
        $this->meetingRoom->makeReservation($reservation);
    }
}
