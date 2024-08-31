<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\UserProfileType;
use App\Helper\FileUploadHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Get all users
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        // Create new user form
        $user = new User();
        $form = $this->createForm(UserProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the plain password from the form
            $plainPassword = $form->get('plainPassword')->getData();

            // Hash the password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User created successfully');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/home.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/verification', name: 'app_adminVerification')]
    public function verification(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Get all post
        $posts = $entityManager->getRepository(Post::class)
            ->findBy(['visible' => false]);

        return $this->render('admin/verification.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Verify the CSRF token
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'User deleted successfully');
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/verification/{id}/toggle-visibility', name: 'post_toggle_visibility', methods: ['POST'])]
    public function togglePostVisibility(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');

        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('toggle' . $post->getId(), $token)) {
            // Toggle the visibility
            $post->setVisible(!$post->isVisible());

            // Persist the changes
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Post visibility updated successfully');
        } else {
            $this->addFlash('error', 'Invalid CSRF token');
        }

        return $this->redirectToRoute('app_adminVerification');
    }

    #[Route('/admin/verification/{id}/delete', name: 'post_delete', methods: ['POST'])]
    public function deletePost(Request $request, Post $post, FileUploadHelper $fileUploadHelper, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');

        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $token)) {
            $entityManager->remove($post);
            $entityManager->flush();

            $fileUploadHelper->unlink($post->getUrl());

            $this->addFlash('success', 'Post deleted successfully');
        } else {
            $this->addFlash('error', 'Invalid CSRF token');
        }

        return $this->redirectToRoute('app_adminVerification');
    }

}