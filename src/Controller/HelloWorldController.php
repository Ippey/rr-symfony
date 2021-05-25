<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloWorldController extends AbstractController
{
    #[Route('/', name: 'hello_world')]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Hello World!',
            'path' => 'src/Controller/HelloWorldController.php',
        ]);
    }
}
