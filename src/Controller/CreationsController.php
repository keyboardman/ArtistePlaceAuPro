<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreationsController extends AbstractController
{
    #[Route('/creations', name: 'app_creations')]
    public function index(): Response
    {
        return $this->render('creations/index.html.twig', [
            'controller_name' => 'CreationsController',
        ]);
    }
}
