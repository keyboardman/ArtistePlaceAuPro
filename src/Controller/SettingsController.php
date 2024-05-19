<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function index(Request $request, EntityManagerInterface $em): Response
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
}
