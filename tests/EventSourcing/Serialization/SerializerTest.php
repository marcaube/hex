<?php

namespace Ob\Hex\Tests\EventSourcing\Serialization;

use Ob\Hex\EventSourcing\Serialization\Serializable;
use Ob\Hex\EventSourcing\Serialization\Serializer;

/**
 * @covers Ob\Hex\EventSourcing\Serialization\Serializer
 */
class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var SerializableObject
     */
    private $object;

    public function setUp()
    {
        $this->serializer = new Serializer();
        $this->object     = new SerializableObject('bar');
    }

    public function testCanSerializeObject()
    {
        $data = $this->serializer->serialize($this->object);
        $this->assertInternalType('array', $data);

        return $data;
    }

    /**
     * @depends testCanSerializeObject
     */
    public function testCanUnserializeObject($data)
    {
        $object = $this->serializer->unserialize($data);
        $this->assertEquals($this->object, $object);
    }
}

class SerializableObject implements Serializable
{
    private $foo;

    public function __construct($foo)
    {
        $this->foo = $foo;
    }

    public function serialize()
    {
        return [
            'foo' => $this->foo,
        ];
    }

    public static function unserialize(array $data)
    {
        return new SerializableObject($data['foo']);
    }
}
