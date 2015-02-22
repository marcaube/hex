<?php

namespace Ob\Hex\Tests\Domain;

use Ob\Hex\Domain\EventSourcedEntity;

/**
 * @covers Ob\Hex\Domain\EventSourcedEntity
 */
class EventSourcedEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeCreatedFromAListOfEvents()
    {
        $entity = Entity::createFromEvents([
            new EntitityWasCreated(),
        ]);

        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals(1, $entity->getFoo());
    }

    public function testIgnoresUnhandledEvents()
    {
        $entity = Entity::createFromEvents([
            new FooWasChanged(),
        ]);

        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals(0, $entity->getFoo());
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
