<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Service;
use App\Entity\CachedElement;
use App\Service\DockerService;
use Symfony\Component\HttpFoundation\Response;

class DockerController extends AbstractController
{
    /**
     * @Route("/docker", name="docker_index")
     */
    public function index(DockerService $dockerService)
    {
        return new JsonResponse(
            $dockerService->getContainers()
        );
    }
    /**
     * @Route("/docker/health", name="docker_status")
     * @param  DockerService $dockerService
     * @return JsonResponse
     */
    public function health(DockerService $dockerService)
    {
        $containers = $dockerService->getContainers();

        return new JsonResponse(
            [
                'docker_status' => $dockerService->execute("docker info"),
                'containers_names' => $containers,
                'container_count' => count($containers),
            ]
        );
    }

    /**
     * @Route("/docker/regenerate-docker-compose", name="docker_regenerate-docker-compose")
     */
    public function regenerateDockerComposeFile(DockerService $docker)
    {
        $docker->removeContainers();
        $elements = $docker->generateDockerCompose();
        $docker->dockerComposeUp();

        return $this->render('docker/regenerate-docker-compose.html.twig', [
            'docker_services_list' => $elements,
        ]);
    }
}
