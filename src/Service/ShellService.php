<?php

namespace App\Service;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ShellService
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        // $this->logger->pushHandler(new ErrorLogHandler());
        $this->logger->pushHandler(new StreamHandler('php://stderr'));

        $process = new Process('echo $PATH');
        $process->run();
        $this->logger->debug("Output : " . $process->getOutput());

        return;
    }

    /**
     * Execute shell command
     * @param  string $command Command to execute
     * @return string
     * @throws ProcessFailedException
     */
    public function execute(string $binary, array $args = []): string
    {
        array_unshift($args, $binary);

        $process = new Process($args);
        $this->logger->debug("Running : " . $process->getCommandLine());
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->logger->debug("Output : " . $process->getOutput());

        return $process->getOutput();
        // return shell_exec(sprintf('RET=`%s`;echo $RET', $command));
    }
}
