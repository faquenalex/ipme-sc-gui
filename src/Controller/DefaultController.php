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
            '<iframe src="https://linuk.github.io/Mario.Run/index.html" width="1500px" height="500px"></iframe>',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }
}
