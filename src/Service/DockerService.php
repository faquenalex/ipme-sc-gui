<?php
namespace App\Service;

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;


class DockerService
{
    const GENERATION_DIR = "./generated/";

    /**
     * @var EntityManager
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
    public function __construct(EntityManager $entityManager, KernelInterface $appKernel)
    {
        $this->entityManager = $entityManager;
        $this->appKernel = $appKernel;
        $this->dockerComposeFile = $this->appKernel->getProjectDir() . self::GENERATION_DIR . "docker-compose.yml";
    }

    public function flushContainerCache(string $dockerName)
    {
        shell_exec(
            sprintf("docker exec -it %s rm -Rf /cache/data/", [$dockerName])
        );
    }

    public function flushContainersCaches()
    {
        $dockerVM = $this->getContainers();

        foreach ($dockerVM as $key => $cachedElement) {
            $this->flushContainerCache($cachedElement->getName());
        }
    }

    public function removeContainer(string $dockerName, bool $rebuildContainers = true)
    {
        shell_exec(sprintf("docker stop %s", $dockerName));
        shell_exec(sprintf("docker rm %s", $dockerName));

        if ($rebuildContainers) {
            $this->generateDockerCompose();
            $this->launchContainers();
        }
    }

    public function removeContainers()
    {
        $dockerVM = $this->getContainers();

        foreach ($dockerVM as $key => $cachedElement) {
            $this->removeContainer($cachedElement->getName(), false);
        }

        $this->generateDockerCompose();
        $this->launchContainers();
    }

    /**
     * @return array[CachedElement]
     */
    public function getContainers(): array
    {
        return $this
        ->entityManager
        ->getRepository(CachedElement::class)
        ->findAll();
    }

    /**
     * Generate docker compose
     * @var array[CachedElement] $elements
     * @return array Return names of created machines in docker
     */
    public function generateDockerCompose(array $elements): array
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

        foreach ($elements as $key => $cachedElement) {
            $vmId = md5($cachedElement->getName());

            $dockerCompose['volumes']['vol_' . $vmId] = [];
            $dockerCompose['services']['ser_' . $vmId] = [
                'image' => 'steamcache/monolithic:latest',
                'container_name' => $cachedElement->getName(),
                'environment' => [
                    'PUID=1000',
                    'PGID=1000',
                    'TZ=' . date_default_timezone_get(),
                ],
                'volumes' => ['vol_' . $vmId],
                'depends_on' => ['game-cache-steamcache-dns'],
                'restart' => 'unless-stopped',
            ];;
        }

        file_put_contents(
            $this->dockerComposeFile,
            Yaml::dump($dockerCompose)
        );

        return array_keys($dockerCompose['services']);
    }

    public function launchContainers()
    {
        shell_exec("docker restart $(docker ps -aq)");
    }
}
