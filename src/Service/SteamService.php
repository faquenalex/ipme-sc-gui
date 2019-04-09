<?php
namespace App\Service;

use App\Entity\CachedElement;
use App\Entity\Service;
use App\Service\DockerService;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpKernel\KernelInterface;

class SteamService
{
    /**
     * For naming containers
     * @var string
     */
    const NAME_PATTERN = "cache-steam";

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var DockerService
     */
    private $docker;

    /**
     * @var SteamCmdService
     */
    private $steamCmdService;

    /**
     * @var KernelInterface
     */
    private $appKernel;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param EntityManagerInterface $EntityManager
     * @param DockerService          $docker
     * @param KernelInterface        $appKernel
     */
    public function __construct(
        EntityManagerInterface $EntityManager,
        DockerService $docker,
        KernelInterface $appKernel,
        SteamCmdService $steamCmdService)
    {
        $this->entityManager = $EntityManager;
        $this->docker = $docker;
        $this->appKernel = $appKernel;
        $this->steamCmdService = $steamCmdService;

        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new StreamHandler('php://stderr'));
    }

    /**
     * @todo STEAMCMD
     * @param  string       $steamId
     * @param  bool|boolean $forceDownload
     * @return bool|boolean Result
     */
    public function addGameBySteamId(string $steamId, bool $forceDownload = false)
    {
        $this->logger->info("Try to add game with id '" . $steamId . "'");
        $cachedElement = $this->entityManager
                              ->getRepository(CachedElement::class)
                              ->findOneByName($steamId);

        $this->logger->info("Send request to lancache-autofill for. ID:" . $steamId, [Logger::INFO]);
        $this->steamCmdService->queueApp($steamId);

        if (!is_null($cachedElement)) {
            $this->logger->info("Game already exist");

            return true;
        }

        $game = new CachedElement();

        $game->setName($steamId);
        $game->setDockerName(self::NAME_PATTERN . '-' . $steamId);
        $game->setDateCreated(new \Datetime);
        $game->setService(
            $this->entityManager->getRepository(Service::class)->findOneByName("Steam")
        );

        $maxPort = $this->entityManager
                        ->getRepository(CachedElement::class)
                        ->findMaxPort();
        $game->setDockerPort(
            is_null($maxPort) ? 8080 : ((int) ($maxPort)) + 1
        );

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $this->logger->info("Game added. ID:" . $steamId, [Logger::INFO]);

        $this->logger->info("Send request to lancache-autofill to download queue");
        $this->steamCmdService->startDownloading();

        $this->docker->generateDockerCompose();

        return true;
    }

    /**
     * @param  int         $steamId
     * @return bool|boolean
     */
    public function removeGameById(int $steamId)
    {
        $cachedElement = $this->entityManager
                              ->getRepository(CachedElement::class)
                              ->findOneByName($steamId);

        if (is_null($cachedElement)) {
            return false;
        }

        $this->entityManager->remove($cachedElement);
        $this->entityManager->flush();

        $this->docker->generateDockerCompose();

        $this->steamCmdService->dequeue($steamId);

        return true;
    }

    /**
     * @return array[CachedElement]
     */
    public function getCachedElements(): array
    {
        return $this->entityManager
                    ->getRepository(CachedElement::class)
                    ->findBy([], ['name' => 'ASC']);
    }
}
