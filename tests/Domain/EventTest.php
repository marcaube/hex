<?php

namespace Ob\Hex\Tests\Domain;

use Mockery as m;
use Ob\Hex\Domain\Reservation;

/**
 * @covers Ob\Hex\Domain\Event
 */
class EventTest extends \PHPUnit_Framework_TestCase
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
    private $event;

    protected function setUp()
    {
        $this->startDate = new \DateTimeImmutable();
        $this->endDate   = new \DateTimeImmutable();
        $this->organizer = m::mock('Ob\Hex\Domain\Email');
        $this->attendee  = m::mock('Ob\Hex\Domain\Email');

        $this->event = new Reservation($this->startDate, $this->endDate, $this->organizer);
    }

    public function testCanBeCreated()
    {
        $organizer = m::mock('Ob\Hex\Domain\Email');

        $this->assertInstanceOf(Reservation::class, new Reservation(new \DateTimeImmutable(), new \DateTimeImmutable(), $organizer));
    }

    public function testStartDateCanBeRetrieved()
    {
        $this->assertEquals($this->startDate, $this->event->getStartDate());
        $this->assertInstanceOf('DateTimeImmutable', $this->event->getStartDate());
    }

    public function testEndDateCanBeRetrieved()
    {
        $this->assertEquals($this->endDate, $this->event->getEndDate());
        $this->assertInstanceOf('DateTimeImmutable', $this->event->getStartDate());
    }

    public function testInitiallyHasNoAttendees()
    {
        $this->assertEquals(0, $this->event->getNumberOfAttendees());
    }

    public function testAttendeeCanBeAdded()
    {
        $this->event->addAttendee($this->attendee);
        $this->assertEquals(1, $this->event->getNumberOfAttendees());

        return $this->event;
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
