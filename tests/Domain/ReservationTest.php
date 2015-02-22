<?php

namespace Ob\Hex\Tests\Domain;

use Mockery as m;
use Ob\Hex\Domain\Reservation;

/**
 * @covers Ob\Hex\Domain\Reservation
 */
class ReservationTest extends \PHPUnit_Framework_TestCase
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
     * @var \Ob\Hex\Domain\Email
     */
    private $organizer;

    /**
     * @var \Ob\Hex\Domain\Email
     */
    private $attendee;

    /**
     * @var Reservation
     */
    private $reservation;

    protected function setUp()
    {
        $this->startDate = new \DateTimeImmutable();
        $this->endDate   = $this->startDate->modify('+1 hour 30 minutes');
        $this->organizer = m::mock('Ob\Hex\Domain\Email');
        $this->attendee  = m::mock('Ob\Hex\Domain\Email');

        $this->reservation = new Reservation($this->startDate, $this->endDate, $this->organizer);
    }

    public function testCanBeCreated()
    {
        $organizer = m::mock('Ob\Hex\Domain\Email');

        $this->assertInstanceOf(Reservation::class, new Reservation(new \DateTimeImmutable(), new \DateTimeImmutable(), $organizer));
    }

    public function testStartDateCanBeRetrieved()
    {
        $this->assertEquals($this->startDate, $this->reservation->getStartDate());
        $this->assertInstanceOf('DateTimeImmutable', $this->reservation->getStartDate());
    }

    public function testEndDateCanBeRetrieved()
    {
        $this->assertEquals($this->endDate, $this->reservation->getEndDate());
        $this->assertInstanceOf('DateTimeImmutable', $this->reservation->getStartDate());
    }

    public function testDurationCanBeRetrieved()
    {
        $this->assertEquals(90, $this->reservation->getDuration());
    }

    public function testOrganizerCanBeRetrieved()
    {
        $this->assertEquals($this->organizer, $this->reservation->getOrganizer());
    }

    public function testInitiallyHasNoAttendees()
    {
        $this->assertEquals(0, $this->reservation->getNumberOfAttendees());
    }

    public function testAttendeeCanBeAdded()
    {
        $this->reservation->addAttendee($this->attendee);
        $this->assertEquals(1, $this->reservation->getNumberOfAttendees());

        return $this->reservation;
    }

    /**
     * @depends testAttendeeCanBeAdded
     */
    public function testAttendeeCanBeRemoved(Reservation $event)
    {
        $event->removeAttendee($this->attendee);
        $this->assertEquals(0, $event->getNumberOfAttendees());
    }
}
