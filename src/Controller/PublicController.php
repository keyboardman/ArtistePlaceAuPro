<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\SubmitPostFormType;
use App\Form\UserProfileType;
use App\Helper\FileUploadHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PublicController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        return $this->render('public/home.html.twig', [
            'controller_name' => 'HomeController',
            'users' => $users
        ]);
    }

    #[Route('/profile/{artiste}', name: 'app_profile')]
    public function profile(Request $request, FileUploadHelper $fileUploadHelper,EntityManagerInterface $entityManager, SluggerInterface $slugger, string $artiste): Response
    {
        // Fetch the user by username
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $artiste]);

        if (!$user) {
            throw $this->createNotFoundException('No user found for username ' . $artiste);
        }

        // Fetch all posts by the user
        $posts = $entityManager->getRepository(Post::class)->findBy(['user' => $user]);

        $post = new Post();
        $form = $this->createForm(SubmitPostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postFile = $form->get('file')->getData();

            if ($postFile) {
                $postFile = $fileUploadHelper->generateUniqueName($postFile);
                dd($postFile);
                $newPath = $fileUploadHelper->upload($postFile, "/documents");
            }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile', ['artiste' => $artiste]);
        }

        return $this->render('public/profile.html.twig', [
            'artiste' => $user,
            'form' => $form->createView(),
            'posts' => $posts, // Pass the posts to the template
        ]);
    }


    #[Route('/creations', name: 'app_creations')]
    public function creations(): Response
    {
        return $this->render('public/creations.html.twig', [
            'controller_name' => 'CreationsController',
        ]);
    }

    #[Route('/bd', name: 'app_bd')]
    public function bd(): Response
    {
        return $this->render('public/creations.html.twig', [
            'controller_name' => 'BdController',
        ]);
    }

    #[Route('/artistes', name: 'app_artistes')]
    public function artistes(EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        return $this->render('public/artistes.html.twig', ['artistes' => $users]);
    }
}