<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="api")
     */
    public function index()
    {
      return new Response(
        'Yolo',
        Response::HTTP_OK,
        ['content-type' => 'text/html']
      );
    }
}
