<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArtistesController extends AbstractController
{
    #[Route('/artistes', name: 'app_artistes')]
    public function show(EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        return $this->render('artistes/index.html.twig', ['artistes' => $users]);
    }
}
