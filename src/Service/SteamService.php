<?php
namespace App\Service;

use Symfony\Component\Yaml\Yaml;
use App\Entity\Service;
use App\Entity\CachedElement;
use App\Entity\CachedElementMetadata;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DockerService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;


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

    public function __construct(EntityManagerInterface $EntityManager, DockerService $docker, KernelInterface $appKernel)
    {
        $this->entityManager = $EntityManager;
        $this->docker = $docker;
        $this->appKernel = $appKernel;
        $stream = new StreamHandler(__DIR__.'/my_app.log', Logger::DEBUG);
        $firephp = new FirePHPHandler();
        $this->StreamHandler = $this->appKernel->getProjectDir()
    }

    /**
     * @param string       $steamId
     * @param bool|boolean $forceDownload
     *
     * @todo STEAMCMD
     *
     * @return bool|boolean Result
     */
    public function addGameBySteamId(string $steamId, bool $forceDownload = false)
    {
        $game = new CachedElement();

        $game->setName($steamId);
        $game->setDockerName(self::NAME_PATTERN . '-' . $steamId);
        $game->setDateCreated(new \Datetime);
        $game->setService(
            $this->entityManager->getRepository(Service::class)->findOneByName("Steam")
        );

        $maxPort = $this->entityManager->getRepository(CachedElement::class)->findMaxPort();
        $game->setDockerPort(
            is_null($maxPort) ? 8080 : ((int) ($maxPort)) + 1
        );

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $this->docker->generateDockerCompose();
        $this->docker->restartContainers();
        $this->docker->dockerComposeUp();

        // shell_exec("steamCMD")

        return true;
    }

    /**
     * @param  string $steamId
     * @return  bool|boolean
     */
    public function removeGameById(string $steamId)
    {
        $cachedElement = $this->entityManager
            ->getRepository(CachedElement::class)->findOneByName($steamId);

        if ($cachedElement) {
            $this->entityManager->remove($cachedElement);
            $this->entityManager->flush();

            $this->docker->generateDockerCompose();
            $this->docker->restartContainers();
            $this->docker->dockerComposeUp();

        }
        // shell_exec(steamCMD)

        return true;
    }

    /**
     * [getCachedElements description]
     * @return array[CachedElement]
     */
    public function getCachedElements(): array
    {
        return $this->entityManager
            ->getRepository(CachedElement::class)
            ->findBy([], ['name' => 'ASC']);
    }
}
