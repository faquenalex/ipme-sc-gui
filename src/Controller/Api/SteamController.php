<?php
namespace App\Controller\Api;

use App\Service\SteamCmdService;
use App\Service\SteamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SteamController extends AbstractController
{
    /**
     * @Route("/api/steam", name="api_steam_index", methods="get")
     */
    public function index(SteamService $steamService)
    {
        return new JsonResponse($steamService->getCachedElements());
    }

    /**
     *
     * @Route("/api/steam/{id}", name="api_steam_post", methods="post")
     * @param  Request        $request
     * @param  SteamService   $steamService
     * @param  int            $id   Steam id
     * @return JsonResponse
     */
    public function post(Request $request, SteamService $steamService, int $id)
    {
        return new JsonResponse([
            'status' => $steamService->addGameBySteamId($request->get("id")),
        ]);
    }

    /**
     *
     * @Route("/api/steam/{id}", name="api_steam_delete", methods="delete")
     * @param  int            $id
     * @param  SteamService   $steamService
     * @return JsonResponse
     */
    public function delete(int $id, SteamService $steamService)
    {
        return new JsonResponse([
            'status' => $steamService->removeGameById($id),
        ]);
    }

    /**
     * @Route("/api/steam/search", name="api_steam_search", methods="get")
     * @param  Request         $request
     * @param  SteamCmdService $steamCmdService
     * @return JsonResponse
     */
    public function search(Request $request, SteamCmdService $steamCmdService)
    {
        $result = $steamCmdService->searchApps($request->get("q"));

        return new JsonResponse([
            'query'         => (string) $request->get("q"),
            'results'       => $result,
            'count_results' => count($result),
        ]);
    }
}
