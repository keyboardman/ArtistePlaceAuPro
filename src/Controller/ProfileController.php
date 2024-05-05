<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/{username}', name: 'app_profile')]
    public function show(EntityManagerInterface $entityManager, string $username): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException('No user found for username ' . $username);
        }
        $username = $user->getUsername(); // Get the username
        return $this->render('profile/index.html.twig', ['artiste' => $username]);
    }
}
