<?php

namespace App\Service;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;

class ShellService
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('steam');
        $this->logger->pushHandler(new ErrorLogHandler());

        return;
    }

    public static function commandExist(string $cmd)
    {
        return ! empty(shell_exec(sprintf("which %s", $cmd)));
    }


    /**
     * Execute shell command
     * @param  string $command Command to execute
     * @return string
     */
    public function execute(string $command): string
    {
        $cmdTest = explode(" ", $command);

        if (! $this->commandExist($cmdTest[0])) {
            $this->logger->error(sprintf("Command %s missing", $cmdTest[0]));
            $this->logger->debug(sprintf("Command was `%s`", $command));
        }

        return shell_exec(sprintf('RET=`%s`;echo $RET', $command));
    }
}
