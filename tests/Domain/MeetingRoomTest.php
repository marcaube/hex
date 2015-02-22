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
class MeetingRoomTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
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

    /**
     * @dataProvider overlapProvider
     */
    public function testReservationsCanNotOverlap($reservation1, $reservation2)
    {
        $this->setExpectedException('\RuntimeException');
        $this->meetingRoom->makeReservation($reservation1);
        $this->meetingRoom->makeReservation($reservation2);
    }

    public function overlapProvider()
    {
        $now              = new \DateTimeImmutable();
        $priorReservation = $this->createReservation($this->capacityLimit, $this->maxDuration, $now, $now->modify('+1 hour'));

        return [
            // Reservation overlap the start of a prior reservation
            [$priorReservation, $this->createReservation($this->capacityLimit, $this->maxDuration, $now->modify('-30 minutes'), $now->modify('+30 minutes'))],

            // Reservation overlap the end of a prior reservation
            [$priorReservation, $this->createReservation($this->capacityLimit, $this->maxDuration, $now->modify('+30 minutes'), $now->modify('+90 minutes'))],

            // Reservation "includes" a prior reservation
            [$priorReservation, $this->createReservation($this->capacityLimit, $this->maxDuration, $now->modify('-30 minutes'), $now->modify('+90 minutes'))],

            // Reservation is "inside" a prior reservation
            [$priorReservation, $this->createReservation($this->capacityLimit, $this->maxDuration, $now->modify('+15 minutes'), $now->modify('+30 minutes'))],

            // Reservation is exactly like a prior reservation
            [$priorReservation, $priorReservation],
        ];
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

    public function testReservationScheduleCanBeRendered()
    {
        $startDate   = new \DateTimeImmutable();
        $endDate     = new \DateTimeImmutable('+1 hour');
        $reservation = $this->createReservation(1, 60, $startDate, $endDate);

        $renderer = m::mock('Ob\Hex\Domain\ScheduleRenderer');
        $renderer->shouldReceive('render')->with([$startDate->format('YmdHis') => $reservation]);

        $this->meetingRoom->makeReservation($reservation);
        $this->meetingRoom->renderScheduleWith($renderer);
    }
}
