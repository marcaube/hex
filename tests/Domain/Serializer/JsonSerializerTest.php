<?php

namespace Ob\Hex\Tests\Domain;

use Ob\Hex\Domain\Serializer\JsonSerializer;
use Ob\Hex\Domain\Serializer\Serializable;

/**
 * @covers Ob\Hex\Domain\Serializer\JsonSerializer
 */
class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var SerializableObject
     */
    private $object;

    public function setUp()
    {
        $this->serializer = new JsonSerializer();
        $this->object     = new SerializableObject('bar');
    }

    public function testCanSerializeObject()
    {
        $json = $this->serializer->serialize($this->object);
        $this->assertInternalType('string', $json);

        return $json;
    }

    /**
     * @depends testCanSerializeObject
     */
    public function testCanUnserializeObject($json)
    {
        $object = $this->serializer->unserialize($json);
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
