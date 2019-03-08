<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class DockerController extends AbstractController
{
    /**
     * @Route("/docker", name="docker_index")
     */
    public function index()
    {
        return $this->render('docker/index.html.twig', [
            'controller_name' => 'DockerController',
        ]);
    }

    /**
     * @Route("/docker/generate", name="docker_generate")
     */
    public function generate(Request $request)
    {
        return new JsonResponse([
                shell_exec('RET=`docker ps -aql`;echo $RET'),
                $request->query->all()
            ]
        );
    }

    /**
     * @Route("/docker/regenerate-docker-compose", name="docker_regenerate-docker-compose")
     */
    public function regenerateDockerComposeFile()
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

        $dockerVM = array_filter(
            explode(" ", shell_exec('RET=`docker ps -aq `;echo $RET')),
            function($a) {
                return empty($a);
            }
        );

        foreach ($dockerVM as $key => $vm) {
            $vmId = uniqid();

            $dockerCompose['volumes']['vol_' . $vmId] = [];
            $dockerCompose['services']['ser_' . $vmId] = [
                'image' => 'steamcache/monolithic:latest',
                'container_name' => 'game-cache-' . $vmId,
                'environment' => [
                    // 'PUID=1000',
                    // 'PGID=1000',
                    // 'TZ=Europe/Paris',
                ],
                'volumes' => ['vol_' . $vmId],
                'depends_on' => ['game-cache-steamcache-dns'],
                'restart' => 'unless-stopped',
            ];
        }

        file_put_contents(
            "/var/www/steamcache/generated/docker-compose.yml",
            Yaml::dump($dockerCompose)
        );

        return new JsonResponse(
            $dockerCompose
        );
    }
}
