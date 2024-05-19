<?php

namespace App\Controller;

use App\Form\HomeSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    public function header(): Response
    {
        $form = $this->createForm(HomeSearchType::class);
        return $this->render('header.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
