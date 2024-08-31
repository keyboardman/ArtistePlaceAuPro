<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[\Symfony\Component\Routing\Annotation\Route('/user/settings', name: 'app_settings')]
    public function settings(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserProfileType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $plainPassword = $form->get("plainPassword")->getData();
            if (!empty($plainPassword)) {
                $hashed = $passwordHasher->hashPassword($user, $plainPassword); // Utilisez le service d'encodage de mot de passe
                $user->setPassword($hashed);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Your profile has been updated.');

            return $this->redirectToRoute('app_settings');
        }

        return $this->render('user/settings.html.twig', [
            'controller_name' => 'SettingsController',
            'form' => $form->createView(),
        ]);
    }
}
