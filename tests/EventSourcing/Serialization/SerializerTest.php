<?php

namespace Ob\Hex\Tests\EventSourcing\Serialization;

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

    public function setUp()
    {
        $this->serializer = new Serializer();
    }

    /**
     * @dataProvider primitivesProvider
     */
    public function testCanSerializePrimitives($input)
    {
        $this->assertEquals(json_encode($input), $this->serializer->serialize($input));
    }

    /**
     * @depends testCanSerializePrimitives
     * @dataProvider primitivesProvider
     */
    public function testCanUnserializePrimitives($input)
    {
        $json = $this->serializer->serialize($input);

        $this->assertEquals($input, $this->serializer->unserialize($json));
    }

    public function primitivesProvider()
    {
        return [
            [rand(1, 1000)],
            ['foo'],
            [[1, 2, 3]],
            [['foo' => 1, 'bar' => 2, 'baz' => 3]],
        ];
    }

    public function testCanSerializeObjects()
    {
        $int    = rand(1, 1000);
        $string = 'foo';
        $array  = [1, 2, 3];

        $input    = new SimpleObject($int, $string, $array);
        $expected = json_encode([
            'class' => 'Ob\Hex\Tests\EventSourcing\Serialization\SimpleObject',
            'data'  => [
                'int'    => $int,
                'string' => $string,
                'array'  => $array,
            ],
        ]);

        $this->assertEquals($expected, $this->serializer->serialize($input));

        return $input;
    }

    /**
     * @depends testCanSerializeObjects
     */
    public function testCanUnserializeObjects($input)
    {
        $json = $this->serializer->serialize($input);

        $this->assertEquals($input, $this->serializer->unserialize($json));
    }

    /**
     * @dataProvider datesProvider
     */
    public function testCanSerializeDates($input, $expected)
    {
        $this->assertEquals($expected, $this->serializer->serialize($input));
    }

    /**
     * @depends testCanSerializeDates
     * @dataProvider datesProvider
     */
    public function testCanUnserializeDates($input, $expected)
    {
        $this->assertEquals($input, $this->serializer->unserialize($expected));
    }

    public function datesProvider()
    {
        return [
            [new \DateTime('2015-12-31T21:15:30.000000-05:00'), '{"class":"DateTime","data":"2015-12-31T21:15:30.000000-05:00"}'],
            [new \DateTimeImmutable('2015-12-31T21:15:30.000000-05:00'), '{"class":"DateTimeImmutable","data":"2015-12-31T21:15:30.000000-05:00"}'],
        ];
    }

    public function testCanSerializeObjectsRecursively()
    {
        $int    = rand(1, 1000);
        $string = 'foo';
        $array  = [1, 2, 3];

        $input    = new ComplexObject(new SimpleObject($int, $string, $array));
        $expected = json_encode([
            'class' => 'Ob\Hex\Tests\EventSourcing\Serialization\ComplexObject',
            'data'  => [
                'object' => [
                    'class' => 'Ob\Hex\Tests\EventSourcing\Serialization\SimpleObject',
                    'data'  => [
                        'int'    => $int,
                        'string' => $string,
                        'array'  => $array,
                    ],
                ],
            ],
        ]);

        $this->assertEquals($expected, $this->serializer->serialize($input));

        return $input;
    }

    /**
     * @depends testCanSerializeObjectsRecursively
     */
    public function testCanUnserializeObjectsRecursively($input)
    {
        $json = $this->serializer->serialize($input);

        $this->assertEquals($input, $this->serializer->unserialize($json));
    }

    public function testCanSerializeClosures()
    {
        $closure = function ($number) {
            return $number / 2;
        };

        $serializer = new Serializer(new \SuperClosure\Serializer());
        $json = $serializer->serialize($closure);

        /** @var \Closure $unserializedClosure */
        $unserializedClosure = $serializer->unserialize($json);

        $this->assertEquals($closure, $unserializedClosure);
        $this->assertEquals(2, $unserializedClosure(4));
    }
}

class SimpleObject
{
    private $int;
    private $string;
    private $array;

    public function __construct($int, $string, $array)
    {
        $this->int    = $int;
        $this->string = $string;
        $this->array  = $array;
    }
}

class ComplexObject
{
    private $object;

    public function __construct(SimpleObject $object)
    {
        $this->object = $object;
    }
}
