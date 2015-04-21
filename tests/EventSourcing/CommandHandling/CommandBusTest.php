<?php

namespace Ob\Hex\Tests\EventSourcing\CommandHandling;

use Mockery as m;
use Ob\Hex\EventSourcing\CommandHandling\CommandBus;
use Ob\Hex\EventSourcing\CommandHandling\CommandHandlerInterface;
use Ob\Hex\EventSourcing\CommandHandling\CommandInterface;

/**
 * @covers Ob\Hex\EventSourcing\CommandHandling\CommandBus
 */
class CommandBusTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var CommandBus
     */
    protected $bus;

    protected function setUp()
    {
        $this->bus = new CommandBus();
    }

    public function testCanRegisterCommandHandlers()
    {
        $command = m::mock('Ob\Hex\EventSourcing\CommandHandling\CommandInterface');

        $handler = m::mock('Ob\Hex\EventSourcing\CommandHandling\CommandHandlerInterface');
        $handler->shouldReceive('handle')->once()->with($command);

        $this->bus->register($handler, get_class($command));
        $this->bus->handle($command);
    }
}
