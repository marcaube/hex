<?php

namespace Ob\Hex\Tests\Application;

use Mockery as m;
use Ob\Hex\Application\PlainTextScheduleRenderer;
use Ob\Hex\Domain\Email;

/**
 * @covers Ob\Hex\Application\PlainTextScheduleRenderer
 *
 * @uses Ob\Hex\Domain\Email
 */
class PlainTextScheduleRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testCanRenderAMeetingRoomSchedule()
    {
        $organizer   = new Email('foo@bar.com');
        $startDate   = new \DateTimeImmutable();
        $endDate     = new \DateTimeImmutable('+30 minutes');
        $reservation = $this->createReservation(3, $organizer, $startDate, $endDate);

        $renderer = new PlainTextScheduleRenderer();
        $text     = $renderer->render([$reservation]);

        $this->assertContains($startDate->format('Y-m-d'), $text);
        $this->assertContains('3 attendee(s)', $text);
        $this->assertContains('organized by foo@bar.com', $text);
    }

    private function createReservation($attendees, $organizer, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate)
    {
        $reservation = m::mock('Ob\Hex\Domain\Reservation');
        $reservation->shouldReceive('getNumberOfAttendees')->andReturn($attendees);
        $reservation->shouldReceive('getStartDate')->andReturn($startDate);
        $reservation->shouldReceive('getEndDate')->andReturn($endDate);
        $reservation->shouldReceive('getOrganizer')->andReturn($organizer);

        return $reservation;
    }
}
