<?php
namespace App\Service;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ShellService
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @return null
     */
    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new StreamHandler('php://stderr'));

        return;
    }

    /**
     *
     * Execute shell command
     *
     * @param  string         $binary
     * @param  array          $args
     * @param  bool|boolean   $autoParse
     * @return string|array
     */
    public function execute(string $binary, array $args = []): string
    {
        array_unshift($args, $binary);

        $process = new Process($args);
        // $this->logger->info("Running : " . $process->getCommandLine());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // $this->logger->debug("Output : " . $process->getOutput());

        return $process->getOutput();
    }
}
