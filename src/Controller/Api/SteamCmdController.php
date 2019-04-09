<?php
namespace App\Controller\Api;

use App\Service\SteamCmdService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SteamCmdController extends AbstractController
{
    /**
     * @Route("/api/steamcmd", name="api_steamcmd_index", methods="get")
     */
    public function index(SteamCmdService $steamCmdService)
    {
        return new JsonResponse($steamCmdService->showQueue());
    }
    /**
     * @Route("/api/steamcmd/add", name="api_steamcmd_add", methods="get")
     */
    public function addQueue(SteamCmdService $steamCmdService)
    {
        return new JsonResponse($steamCmdService->queueApp(550));
    }


}
