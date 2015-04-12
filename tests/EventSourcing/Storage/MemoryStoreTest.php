<?php

namespace Ob\Hex\Tests\EventSourcing\Storage;

use Ob\Hex\EventSourcing\Storage\MemoryStore;

/**
 * @covers Ob\Hex\EventSourcing\Storage\MemoryStore
 */
class MemoryStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MemoryStore
     */
    private $store;

    private $events = [];

    public function setUp()
    {
        $this->store  = new MemoryStore();
        $this->events = [
            new MemoryStoreTestEventOne(),
            new MemoryStoreTestEventTwo(),
            new MemoryStoreTestEventOne(),
            new MemoryStoreTestEventThree(),
        ];
    }

    public function testCanWriteAndReadEvents()
    {
        $this->store->write(42, $this->events);
        $this->assertEquals($this->events, $this->store->read(42));
    }

    public function testThrowsAnExceptionIfEventStreamIsNotFound()
    {
        $this->setExpectedException('Exception');
        $this->store->read(123);
    }
}

class MemoryStoreTestEventOne {}
class MemoryStoreTestEventTwo {}
class MemoryStoreTestEventThree {}
