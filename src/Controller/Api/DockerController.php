<?php
namespace App\Controller\Api;

use App\Service\SteamCmdService;
use App\Service\DockerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DockerController extends AbstractController
{
    /**
     * @Route("/api/docker/", name="api_docker_index", methods="get")
     */
    public function index(DockerService $dockerService)
    {
        return new JsonResponse($dockerService->getContainers());
    }

    /**
     * @Route("/api/docker/{name}", name="api_docker_by_name", methods="get")
     */
    public function byName(DockerService $dockerService, string $name)
    {
        return new JsonResponse($dockerService->getContainerInfo($name));
    }

}
