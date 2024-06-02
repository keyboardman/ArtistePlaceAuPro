<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\HomeSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PublicController extends AbstractController
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
    #[Route('/profile/{artiste}', name: 'app_profile')]
    public function profile(EntityManagerInterface $entityManager, string $artiste): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $artiste]);

        if (!$user) {
            throw $this->createNotFoundException('No user found for username ' . $artiste);
        }
        $artiste = $user->getUsername(); // Get the username
        return $this->render('profile/index.html.twig', ['artiste' => $artiste]);
    }
    #[Route('/creations', name: 'app_creations')]
    public function creations(): Response
    {
        return $this->render('creations/index.html.twig', [
            'controller_name' => 'CreationsController',
        ]);
    }
    #[Route('/bd', name: 'app_bd')]
    public function bd(): Response
    {
        return $this->render('bd/index.html.twig', [
            'controller_name' => 'BdController',
        ]);
    }
    #[Route('/settings', name: 'app_settings')]
    public function settings(Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(UserProfileType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $plainPassword = $form->get("plainPassword")->getData();
            if (!empty($plainPassword)){
                $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);
                $user->setPassword($hashed);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Your profile has been updated.');

            return $this->redirectToRoute('app_settings');
        }

        return $this->render('settings/index.html.twig', [
            'controller_name' => 'SettingsController',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/artistes', name: 'app_artistes')]
    public function artistes(EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        return $this->render('artistes/index.html.twig', ['artistes' => $users]);
    }
}
