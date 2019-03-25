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
     * Get all containers name's
     * @return array[string]
     */
    public function getContainers()
    {
        $cmdResult = explode(
            " ",
            $this->execute("docker ps -aq --filter 'name=cache-'")
        );

        return array_map(
            'trim',
            $cmdResult
        );
    }

    /**
     * Start ONE containers
     * @return string
     */
    public function startContainer($name): string
    {
        return $this->execute(sprintf("docker start %s", $name));
    }

    /**
     * Stop ONE containers
     * @return string
     */
    public function stopContainer($name): string
    {
        return $this->execute(sprintf("docker stop %s", $name));
    }

    /**
     * Start ALL containers
     * @return
     */
    public function startContainers()
    {
        foreach ($this->getContainers() as $key => $containerName) {
            $this->startContainer($containerName);
        }
    }

    /**
     * Stop ALL containers
     * @return
     */
    public function stopContainers()
    {
        foreach ($this->getContainers() as $key => $containerName) {
            $this->stopContainer($containerName);
        }
    }

    /**
     * Restart ALL containers
     * @return
     */
    public function restartContainers()
    {
        $this->stopContainers();
        $this->startContainers();
    }

    /**
     * Delete cache from ONE containers
     * @return
     */
    public function flushContainerCache(string $dockerName)
    {
        $this->execute(
            sprintf("docker exec -it %s rm -Rf /cache/data/", $dockerName)
        );
    }

    /**
     * Delete cache from ALL containers
     * @return
     */
    public function flushContainersCaches()
    {
        foreach ($this->getContainers() as $key => $containerName) {
            $this->flushContainerCache($containerName);
        }
    }

    /**
     * return ONE container by name
     * @param  string $dockerName Container to remove
     * @return
     */
    public function removeContainer(string $dockerName)
    {
        $this->stopContainer($dockerName);
        $this->execute(sprintf("docker rm %s", $dockerName));
    }

    /**
     * Remove ALL containers
     * @return
     */
    public function removeContainers()
    {
        foreach ($this->getContainers() as $key => $containerName) {
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
                'cache-proxy-service' => [
                    'image' => 'nginx',
                    // 'image' => 'jwilder/nginx-proxy',
                    'container_name' => 'cache-proxy-service',
                    // 'environment' => [],
                    'ports' => [
                        '80:80'
                    ],
                    'volumes' => [
                        './overlays/cache-proxy-service/etc/nginx/conf.d/default.conf:/etc/nginx/conf.d/default.conf',
                        // '/var/run/docker.sock:/tmp/docker.sock:ro'
                    ],
                ],
                'cache-dns-01' => [
                    'image' => 'steamcache/steamcache-dns:latest',
                    'container_name' => 'cache-dns-01',
                    'environment' => [
                        // 'USE_GENERIC_CACHE=true',
                        // 'LANCACHE_IP=192.168.42.224',
                        // 'LANCACHE_IP=cache-proxy-service',
                        // 'STEAMCACHE_IP=172.19.0.2',
                        'STEAMCACHE_IP=192.168.1.34',
                        // 'STEAMCACHE_IP=cache-proxy-service',
                    ],
                    'depends_on' => [
                        'cache-proxy-service',
                    ],
                    'ports' => [
                        '53:53/udp'
                    ],
                    // 'expose' => ['53']
                ],
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
                'volumes' => [
                    'vol_' . $vmId . ':/data',
                ],
                'depends_on' => [
                    'cache-proxy-service'
                ],
                'restart' => 'unless-stopped',
                // 'ports' => [
                //     $cachedElement->getDockerPort()  . ':80'
                // ],
                'expose' => [
                    '80'
                    // $cachedElement->getDockerPort()
                ]
            ];
        }

        file_put_contents(
            $this->dockerComposeFile,
            Yaml::dump($dockerCompose, 4)
        );

        return array_keys($dockerCompose['services']);
    }

    /**
     * Start docker images with docker-compose
     * @return string
     */
    public function dockerComposeUp(): string
    {
        return $this->execute(sprintf("docker-compose -f %s up -d", $this->dockerComposeFile));
    }

    /**
     * Execute shell command
     * @param  string $command Command to execute
     * @return string
     */
    public function execute(string $command): string
    {
        return shell_exec(sprintf('RET=`%s`;echo $RET', $command));
    }
}
