<?php
namespace App\Controller;

use App\Service\SteamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class SteamController extends AbstractController
{
    /**
     * @Route("/steam", name="steam")
     */
    public function index(SteamService $steamService)
    {
        $result = $steamService->getCachedElements();

        return new JsonResponse($result);
    }
}
