<?php
namespace App\Service;

use App\Entity\CachedElement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

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
     * @var Logger
     */
    private $logger;

    /**
     * @var Shell
     */
    private $shell;

    /**
     * @param EntityManager   $entityManager
     * @param KernelInterface $appKernel
     */
    public function __construct(EntityManagerInterface $entityManager, KernelInterface $appKernel)
    {
        $this->entityManager = $entityManager;
        $this->appKernel = $appKernel;
        $this->dockerComposeFile = $this->appKernel->getProjectDir() . self::GENERATION_DIR . "docker-compose.yml";

        $this->shell = new ShellService();

        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new StreamHandler('php://stderr'));
    }

    /**
     * Get all containers infos
     * @return array[string]
     */
    public function getContainers()
    {
        $containers = [];

        foreach ($this->getContainersID() as $key => $value) {
            $containers[$key] = $this->getContainerInfo($value, 'id');
        }

        return $containers;
    }

    /**
     * @return array[string]
     */
    public function getContainersID(): array
    {
        return array_filter(preg_split(
            "/[\s]+/",
            $this->shell->execute(
                "docker",
                [
                    "ps",
                    "-aq",
                    "--filter",
                    "name=cache-",
                ]
            )
        ));
    }

    /**
     * @todo find a way to remove last \n at end of $cmd / Trim won't work
     * @param  string          $identifier container identifier
     * @return array[string]
     */
    public function getContainerInfo(string $identifier, string $key = "name")
    {
        $keys = [
            'ID',
            'Image',
            'Mounts',
            'Networks',
            'Size',
            'Ports',
            'CreatedAt',
            'Status',
        ];
        $cmd = $this->shell->execute(
            "docker",
            [
                'ps',
                '-aq',
                '--filter',
                $key . '=' . $identifier,
                '--format',
                "{{ " . implode("}}---{{", array_map(function ($b) {return "." . $b;}, $keys)) . " }}",
            ]
        );

        $cmdResult = preg_split(
            "/\-{3}+/",
            $cmd
        );

        return array_combine(
            array_map("strtolower", $keys),
            $cmdResult
        );
    }

    /**
     * Start ONE containers
     * @return string
     */
    public function startContainer($name): string
    {
        if (empty($name)) {
            $this->logger->error("No container name provided");

            return "";
        }

        return $this->shell->execute("docker", ['start', $name]);
    }

    /**
     * Stop ONE containers
     * @return string
     */
    public function stopContainer($name): string
    {
        if (empty($name)) {
            $this->logger->error("No container name provided");

            return "";
        }

        return $this->shell->execute("docker", ['stop', $name]);
    }

    /**
     * Start ALL containers
     * @return
     */
    public function startContainers()
    {
        foreach ($this->getContainersID() as $key => $containerName) {
            $this->startContainer($containerName);
        }
    }

    /**
     * Stop ALL containers
     * @return
     */
    public function stopContainers()
    {
        foreach ($this->getContainersID() as $key => $containerName) {
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
     * @return string
     */
    public function flushContainerCache(string $name): string
    {
        return $this->shell->execute("docker", ['exec', '-it', $name, 'rm -Rf /cache/data/']);
    }

    /**
     * Delete cache from ALL containers
     * @return
     */
    public function flushContainersCaches()
    {
        foreach ($this->getContainersID() as $key => $containerName) {
            $this->flushContainerCache($containerName);
        }
    }

    /**
     * return ONE container by name
     * @param    string $name Container to remove
     * @return
     */
    public function removeContainer(string $name)
    {
        $this->stopContainer($name);

        return $this->shell->execute("docker", ['rm', $name]);
    }

    /**
     * Remove ALL containers
     * @return
     */
    public function removeContainers()
    {
        foreach ($this->getContainersID() as $key => $containerName) {
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
            'version'  => '3.3',
            'services' => [
                'lancache-autofill'   => [
                    'image'          => 'ubuntu:18.10',
                    'container_name' => 'cache-lancache-autofill',
                    'environment'    => [
                        'PUID=1000',
                        'PGID=1000',
                        'TZ=' . date_default_timezone_get(),
                        'DOWNLOADS_DIRECTORY=/data',
                        'STEAMCMD_PATH=/usr/games/steam/steamcmd.sh',
                        'DEFAULT_STEAM_USER=steamIPME',
                        'DEBIAN_FRONTEND=noninteractive',
                        'DEBCONF_NONINTERACTIVE_SEEN=true',
                    ],
                    'command'        => 'bash /scripts/bootstrap.sh',
                    'volumes'        => [
                        'lancache-autofill:/data',
                        './overlays/lancache/scripts:/scripts',
                    ],
                    'ports'          => [
                        '8000:80',
                    ],
                ],
                'cache-proxy-service' => [
                    'image'          => 'nginx',
                    // 'image' => 'jwilder/nginx-proxy',
                    'container_name' => 'cache-proxy-service',
                    // 'environment' => [],
                    'ports'          => [
                        '80:80',
                    ],
                    'volumes'        => [
                        './overlays/cache-proxy-service/etc/nginx/conf.d/default.conf:/etc/nginx/conf.d/default.conf',
                        // '/var/run/docker.sock:/tmp/docker.sock:ro'
                    ],
                ],
                'cache-dns-01'        => [
                    'image'          => 'steamcache/steamcache-dns:latest',
                    'container_name' => 'cache-dns-01',
                    'environment'    => [
                        // 'USE_GENERIC_CACHE=true',
                        // 'LANCACHE_IP=192.168.42.224',
                        // 'LANCACHE_IP=cache-proxy-service',
                        // 'STEAMCACHE_IP=172.19.0.2',
                        'STEAMCACHE_IP=192.168.1.34',
                        // 'STEAMCACHE_IP=cache-proxy-service',
                    ],
                    'depends_on'     => [
                        'cache-proxy-service',
                        'lancache-autofill',
                    ],
                    'ports'          => [
                        '53:53/udp',
                    ],
                    // 'expose' => ['53']
                ],
            ],
            'volumes'  => [
                'lancache-autofill' => [],
            ],
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
                'image'          => 'steamcache/monolithic:latest',
                'container_name' => $cachedElement->getDockerName(),
                'environment'    => [
                    'PUID=1000',
                    'PGID=1000',
                    'TZ=' . date_default_timezone_get(),
                ],
                'volumes'        => [
                    'vol_' . $vmId . ':/data',
                ],
                'depends_on'     => [
                    'cache-proxy-service',
                    'lancache-autofill',
                ],
                'restart'        => 'unless-stopped',
                // 'ports' => [
                //     $cachedElement->getDockerPort()  . ':80'
                // ],
                'expose'         => [
                    '80',
                    // $cachedElement->getDockerPort()
                ],
            ];
        }

        file_put_contents(
            $this->dockerComposeFile,
            Yaml::dump($dockerCompose, Yaml::PARSE_CONSTANT)
        );

        return array_keys($dockerCompose['services']);
    }

    /**
     * Start docker images with docker-compose
     * @return
     */
    public function dockerComposeUp()
    {
        $this->logger->info("Regenerate docker-compose");

        $this->shell->execute(
            "docker-compose",
            [
                '-f',
                $this->dockerComposeFile,
                'up',
                '-d',
            ]
        );
    }
}
