<?php

namespace App\Controller;

use App\Service\SteamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Serializer\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Service\DockerService;

class SteamController extends AbstractController
{
    /**
     * @Route("/steam", name="steam")
     */
    public function index(DockerService $docker)
    {
        // var_dump($docker->getContainers());
        // var_dump($docker->stopContainers());
        // var_dump($docker->removeContainers());
        // var_dump($docker->dockerComposeUp());
        // die;

        return $this->render('steam/index.html.twig', [
            'controller_name' => 'SteamController',
        ]);
    }

    /**
     * @todo CRUD ME SENPAI ?
     * @Route("/steam/add-by-id", name="steam_add")
     */
    public function addById(Request $request, SteamService $steamService)
    {
        $status = $steamService->addGameBySteamId($request->get("id"));

        return new JsonResponse([
            $status
        ]);
    }
}
