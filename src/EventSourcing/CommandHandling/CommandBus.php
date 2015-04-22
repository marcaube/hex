<?php

namespace Ob\Hex\EventSourcing\CommandHandling;

class CommandBus
{
    /**
     * @var CommandHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command)
    {
        $commandFQCN = get_class($command);

        if (isset($this->handlers[$commandFQCN])) {
            $this->handlers[$commandFQCN]->handle($command);
        }
    }

    /**
     * @param CommandHandlerInterface $handler
     * @param string                  $commandFQCN The command fully qualified class name
     */
    public function register(CommandHandlerInterface $handler, $commandFQCN)
    {
        // This is a hack, but I have not found a clean way to map a command to a single handler in a unit-testable
        // manner ... yet
        $this->handlers[$commandFQCN] = $handler;
    }
}
