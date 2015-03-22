<?php

namespace Ob\Hex\Tests\EventSourcing;

use Ob\Hex\EventSourcing\EventSourcedEntity;

/**
 * @covers Ob\Hex\EventSourcing\EventSourcedEntity
 */
class EventSourcedEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeCreatedFromAListOfEvents()
    {
        $entity = Entity::createFromChanges([
            new EntitityWasCreated(),
        ]);

        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals(1, $entity->getFoo());
    }

    public function testIgnoresUnhandledEvents()
    {
        $entity = Entity::createFromChanges([
            new FooWasChanged(),
        ]);

        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals(0, $entity->getFoo());
    }

    public function testEventsCanBeRetrieved()
    {
        $events = [new EntitityWasCreated()];
        $entity = Entity::createFromChanges($events);

        $this->assertEquals($events, $entity->getChanges());
    }
}

class Entity extends EventSourcedEntity
{
    private $foo = 0;

    public function getFoo()
    {
        return $this->foo;
    }

    protected function applyEntitityWasCreated(EntitityWasCreated $event)
    {
        $this->foo = 1;
    }
}

// This event sets the internal "foo" counter
class EntitityWasCreated
{
}

// This event is not handled, so it has no effect in the state of the entity
class FooWasChanged
{
}
