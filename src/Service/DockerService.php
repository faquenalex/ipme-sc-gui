<?php
namespace App\Service;

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use App\Entity\CachedElement;
use Symfony\Component\HttpKernel\KernelInterface;

class DockerService
{
    const GENERATION_DIR = "/generated/";

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var KernelInterface
     */
    private $appKernel;

    /**
     * @var string
     */
    private $dockerComposeFile;

    /**
     * @param EntityManager $entityManager
     * @param KernelInterface $appKernel
     */
    public function __construct(EntityManagerInterface $entityManager, KernelInterface $appKernel)
    {
        $this->entityManager = $entityManager;
        $this->appKernel = $appKernel;
        $this->dockerComposeFile = $this->appKernel->getProjectDir() . self::GENERATION_DIR . "docker-compose.yml";
    }

    /**
     * @return array[string]
     */
    public function getContainers()
    {
        $cmdResult = explode(" ", $this->execute("docker ps -aq --filter 'name=steamcache-game'"));

        return array_map(
            'trim',
            $cmdResult
        );
    }

    public function startContainer($name): string
    {
        return $this->execute(sprintf("docker start %s", $name));
    }

    public function stopContainer($name): string
    {
        return $this->execute(sprintf("docker stop %s", $name));
    }

    public function startContainers()
    {
        foreach ($this->getContainers() as $key => $containerName) {
            $this->startContainer($containerName);
        }
    }

    public function stopContainers()
    {
        foreach ($this->getContainers() as $key => $containerName) {
            $this->stopContainer($containerName);
        }
    }

    public function restartContainers()
    {
        $this->stopContainers();
        $this->startContainers();
    }

    public function flushContainerCache(string $dockerName)
    {
        $this->execute(
            sprintf("docker exec -it %s rm -Rf /cache/data/", $dockerName)
        );
    }

    public function flushContainersCaches()
    {
        $containers = $this->getContainers();

        foreach ($containers as $key => $containerName) {
            $this->flushContainerCache($containerName);
        }
    }

    public function removeContainer(string $dockerName)
    {
        $this->stopContainer($dockerName);
        $this->execute(sprintf("docker rm %s", $dockerName));
    }

    public function removeContainers()
    {
        $containers = $this->getContainers();

        foreach ($containers as $key => $containerName) {
            $this->removeContainer($containerName);
        }

        $this->generateDockerCompose();
    }

    /**
     * Generate docker compose
     *
     * @return array Return names of created machines in docker
     */
    public function generateDockerCompose(): array
    {
        $dockerCompose = [
            'version' => '3.3',
            'services' => [
                'steamcache-dns' => [
                    'image' => 'steamcache/steamcache-dns:latest',
                    'container_name' => 'game-cache-steamcache-dns',
                ]
            ]
        ];

        $elements = $this
            ->entityManager
            ->getRepository(CachedElement::class)
            ->findBy(
                [],
                ['name' => 'ASC']
            );

        foreach ($elements as $key => $cachedElement) {
            $vmId = md5($cachedElement->getName());

            $dockerCompose['volumes']['vol_' . $vmId] = [];
            $dockerCompose['services']['ser_' . $vmId] = [
                'image' => 'steamcache/monolithic:latest',
                'container_name' => $cachedElement->getDockerName(),
                'environment' => [
                    'PUID=1000',
                    'PGID=1000',
                    'TZ=' . date_default_timezone_get(),
                ],
                'volumes' => ['vol_' . $vmId],
                'depends_on' => ['steamcache-dns'],
                'restart' => 'unless-stopped',
            ];;
        }

        file_put_contents(
            $this->dockerComposeFile,
            Yaml::dump($dockerCompose)
        );

        return array_keys($dockerCompose['services']);
    }

    public function dockerComposeUp(): string
    {
        return $this->execute(sprintf("docker-compose -f %s up -d", $this->dockerComposeFile));
    }

    public function execute(string $command): string
    {
        return shell_exec(sprintf('RET=`%s`;echo $RET', $command));
    }
}
