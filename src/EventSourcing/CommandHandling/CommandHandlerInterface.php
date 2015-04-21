<?php

namespace Ob\Hex\EventSourcing\CommandHandling;

interface CommandHandlerInterface
{
    /**
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command);
}
