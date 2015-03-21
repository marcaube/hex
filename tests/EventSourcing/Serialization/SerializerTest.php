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

    public function primitivesProvider()
    {
        return [
            [rand(1, 1000)],
            ['foo'],
            [[1, 2, 3]],
        ];
    }

    public function testCanSerializeASimpleObject()
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
    }

    /**
     * @dataProvider datesProvider
     */
    public function testCanSerializeDates($input, $expected)
    {
        $this->assertEquals($expected, $this->serializer->serialize($input));
    }

    public function datesProvider()
    {
        return [
            [new \DateTime('2015-12-31T21:15:30.000000-05:00'), '{"class":"DateTime","data":"2015-12-31T21:15:30.000000-05:00"}'],
            [new \DateTimeImmutable('2015-12-31T21:15:30.000000-05:00'), '{"class":"DateTimeImmutable","data":"2015-12-31T21:15:30.000000-05:00"}'],
        ];
    }

    public function testCanSerializeAComplexObject()
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
