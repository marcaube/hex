<?php

require __DIR__ . '/../vendor/autoload.php';

use Ob\Hex\Domain\Email;
use Ob\Hex\Domain\MeetingRoom;
use Ob\Hex\Domain\Reservation;


// Create a meeting room with capacity for 10 people
$room = MeetingRoom::create(10, 180);


// Make a reservation at noon
$room->makeReservation(new Reservation(
    new DateTimeImmutable('today 12:00'),
    new DateTimeImmutable('today 13:00'),
    new Email('foo@bar.com')
));


// Plan a second reservation at 2 o'clock
$reservation = new Reservation(
    new DateTimeImmutable('today 14:00'),
    new DateTimeImmutable('today 16:15'),
    new Email('foo@bar.com')
);
// Invite 3 employees to the meeting
$reservation->invite(new Email('bar@bar.com'));
$reservation->invite(new Email('baz@bar.com'));
$reservation->invite(new Email('qux@bar.com'));
// Uninvite the last invitee
$reservation->uninvite(new Email('qux@bar.com'));
// Make the reservation
$room->makeReservation($reservation);


// Render the schedule
echo $room->renderScheduleWith(new \Ob\Hex\Application\PlainTextScheduleRenderer()) . PHP_EOL;


// Create an in-memory event store
$store = new \Ob\Hex\EventSourcing\Storage\MemoryStore();
// Persist the room schedule (the events)
$store->write(1, $room->getEvents());


// "Hydrate" the room from the event store
$storedRoom = MeetingRoom::createFromEvents($store->read(1));


// Render the schedule again, it should be identical
echo $room->renderScheduleWith(new \Ob\Hex\Application\PlainTextScheduleRenderer());
