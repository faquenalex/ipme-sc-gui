<?php
namespace App\Service;

use Symfony\Component\Yaml\Yaml;
use App\Entity\Service;
use App\Entity\CachedElement;
use App\Entity\CachedElementMetadata;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DockerService;

class SteamService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var DockerService
     */
    private $docker;

    public function __construct(EntityManagerInterface $EntityManager, DockerService $docker)
    {
        $this->entityManager = $EntityManager;
        $this->docker = $docker;
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
        $game->setDockerName("steamcache-game-" . $steamId);
        $game->setDateCreated(new \Datetime);
        $game->setService(
            $this->entityManager->getRepository(Service::class)->findOneByName("Steam")
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

        $this->entityManager->remove($cachedElement);
        $this->entityManager->flush();

        $this->docker->generateDockerCompose();
        $this->docker->restartContainers();
        $this->docker->dockerComposeUp();

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
