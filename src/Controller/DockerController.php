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
    public function index()
    {
        $data = shell_exec('RET=`docker ps -a`;echo $RET');
        return new JsonResponse([
                $data
            ]
        );
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
    public function regenerateDockerComposeFile(DockerService $docker)
    {
        $elements = $docker->generateDockerCompose();

        return $this->render('docker/regenerate-docker-compose.html.twig', [
            'docker_services_list' => $elements,
        ]);
    }
}
