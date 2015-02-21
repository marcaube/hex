<?php

namespace Ob\Hex\Tests\Domain;

use Ob\Hex\Domain\Email;

/**
 * @covers Ob\Hex\Domain\Email
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeCreated()
    {
        $this->assertInstanceOf(Email::class, new Email('foo@bar.com'));
    }

    public function testCanBeRetrievedAsAString()
    {
        $email = 'foo@bar.com';

        $this->assertSame($email, (new Email($email))->asString());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCannotBeCreatedWithNonString()
    {
        new Email(1);
    }

    /**
     * @dataProvider invalidEmailProvider
     * @expectedException \InvalidArgumentException
     */
    public function testCannotBeCreatedWithAnInvalidEmailFormat($email)
    {
        new Email($email);
    }

    /**
     * @return array
     */
    public function invalidEmailProvider()
    {
        return [
            'plain text'          => ['foo'],
            'no tld'              => ['foo@bar'],
            'no user'             => ['@bar.com'],
            'email client #1'     => ['Foo Bar <foo@bar.com>'],
            'email client #2'     => ['foo@bar.com (Foo Bar)'],
            'no @'                => ['foo.bar.com'],
            'multiple @'          => ['foo@bar@baz.com'],
            'leading dot'         => ['.foo@bar.com'],
            'trailing dot'        => ['foo.@bar.com'],
            'consecutive dots #1' => ['foo..bar@baz.com'],
            'consecutive dots #2' => ['foo.bar@baz..com'],
            'utf-8 chars'         => ['føô@bar.com'],
            'domain leading dash' => ['foo@-bar.com'],
        ];
    }
}
