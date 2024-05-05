<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BdController extends AbstractController
{
    #[Route('/bd', name: 'app_bd')]
    public function index(): Response
    {
        return $this->render('bd/index.html.twig', [
            'controller_name' => 'BdController',
        ]);
    }
}
