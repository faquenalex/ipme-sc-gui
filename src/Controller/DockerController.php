<?php
namespace App\Controller;

use App\Entity\Service;
use App\Service\DockerService;
use App\Service\ShellService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DockerController extends AbstractController
{
    /**
     * @Route("/docker", name="docker_index")
     */
    public function index(DockerService $dockerService)
    {
        return new JsonResponse($dockerService->getContainers());
    }

    /**
     * @Route("/docker/regenerate-docker-compose", name="docker_regenerate-docker-compose")
     */
    public function regenerateDockerComposeFile(DockerService $dockerService, Request $request)
    {
        $services = $dockerService->generateDockerCompose($request->get('dry_run') !== null);

        return new JsonResponse($services);
    }

    /**
     * @Route("/docker/docker-compose-up", name="docker-compose-up")
     */
    public function dockerComposeUp(DockerService $dockerService)
    {
        // $dockerService->removeContainers();
        $dockerService->dockerComposeUp();

        return new JsonResponse($dockerService->getContainers());
    }

    /**
     * @Route("/docker/health", name="docker_status")
     * @param  DockerService  $dockerService
     * @return JsonResponse
     */
    public function health(ShellService $shellService, DockerService $dockerService)
    {
        $containers = $dockerService->getContainers();

        return new JsonResponse([
            'docker_status'   => $shellService->execute("docker", ["info"]),
            'containers'      => $containers,
            'container_count' => count($containers),
        ]);
    }
}
