<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\SubmitPostFormType;
use App\Helper\FileUploadHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PublicController extends AbstractController
{
    #[\Symfony\Component\Routing\Annotation\Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Fetch the last 20 visible posts
        $posts = $entityManager->createQueryBuilder()
            ->select('p.filePath', 'p.titre', 'p.token')
            ->from(Post::class, 'p')
            ->leftJoin('p.user', 'u') // Join the User entity
            ->where('p.visible = :visible')
            ->setParameter('visible', true)
            ->orderBy('p.datePublication', 'DESC') // Order by latest posts
            ->setMaxResults(20) // Limit to 20 posts
            ->getQuery()
            ->getArrayResult();

        // Render the home page with the posts
        return $this->render('public/home.html.twig', [
            'posts' => $posts,
        ]);
    }


    #[\Symfony\Component\Routing\Annotation\Route('/post/{token}', name: 'app_post')]
    public function post(EntityManagerInterface $entityManager, string $token): Response
    {
        // Fetch the post by token and include the user information
        $post = $entityManager->createQueryBuilder()
            ->select('p.filePath', 'p.titre', 'p.datePublication', 'p.description', 'u.id AS userId', 'u.firstname', 'u.lastname', 'u.token AS userToken', 'u.biographie', 'u.avatarUrl')
            ->from(Post::class, 'p')
            ->leftJoin('p.user', 'u') // Join the User entity
            ->where('p.token = :token')
            ->andWhere('p.visible = :visible')
            ->setParameter('token', $token)
            ->setParameter('visible', true)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        // Fetch the last 5 posts by the same user, excluding the current post
        $lastPostsByUser = $entityManager->createQueryBuilder()
            ->select('p.filePath', 'p.titre', 'p.datePublication', 'p.token')
            ->from(Post::class, 'p')
            ->where('p.user = :user')
            ->andWhere('p.visible = :visible')
            ->andWhere('p.token != :currentToken') // Exclude the current post
            ->setParameter('user', $post['userId'])
            ->setParameter('visible', true)
            ->setParameter('currentToken', $token)
            ->orderBy('p.datePublication', 'DESC')
            ->setMaxResults(5) // Limit to the last 5 posts
            ->getQuery()
            ->getArrayResult();

        // Check if we need more posts to make a total of 10
        $totalPostsNeeded = 10;
        $additionalPostsNeeded = $totalPostsNeeded - count($lastPostsByUser);

        // Fetch additional posts from other users if necessary
        $additionalPosts = [];
        if ($additionalPostsNeeded > 0) {
            $additionalPosts = $entityManager->createQueryBuilder()
                ->select('p.filePath', 'p.titre', 'p.token')
                ->from(Post::class, 'p')
                ->where('p.visible = :visible')
                ->andWhere('p.token != :currentToken') // Exclude the current post
                ->andWhere('p.user != :user') // Exclude posts from the same user
                ->setParameter('visible', true)
                ->setParameter('currentToken', $token)
                ->setParameter('user', $post['userId'])
                ->orderBy('p.datePublication', 'DESC')
                ->setMaxResults($additionalPostsNeeded) // Limit to the number of additional posts needed
                ->getQuery()
                ->getArrayResult();
        }

        // Combine the posts by the user and additional posts
        $lastPosts = array_merge($lastPostsByUser, $additionalPosts);

        // Render the post page with the post, user, and last 10 posts
        return $this->render('public/post.html.twig', [
            'post' => $post,
            'lastPosts' => $lastPosts,
            'controller_name' => 'PostController',
        ]);
    }



    #[\Symfony\Component\Routing\Annotation\Route('/profile/{token}', name: 'app_profile')]
    public function profile(
        Request $request,
        FileUploadHelper $fileUploadHelper,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        string $token
    ): Response {
        // Fetch user and posts in a single query
        $user = $entityManager->createQueryBuilder()
            ->select('u.id, u.firstname, u.lastname, u.biographie, u.avatarUrl')
            ->from(User::class, 'u')
            ->where('u.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $posts = $entityManager->createQueryBuilder()
            ->select('p.filePath, p.token, p.titre')
            ->from(Post::class, 'p')
            ->where('p.user = :user')
            ->andWhere('p.visible = :visible')
            ->setParameter('user', $user['id'])
            ->setParameter('visible', true)
            ->getQuery()
            ->getArrayResult();

        // Create a new Post object and form
        $post = new Post();
        $form = $this->createForm(SubmitPostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postFile = $form->get('file_path')->getData();

            if ($postFile) {
                // Handle file upload
                $filePath = $fileUploadHelper->upload($postFile, "/documents");
                $post->setFilePath($filePath);
            }

            // Set the user for the post
            $post->setUser($user);

            // Set default visibility to false
            $post->setVisible(false);

            // Persist the post
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Votre post a été soumis à une vérification.');

            // Redirect to the profile page
            return $this->redirectToRoute('app_profile', ['token' => $token]);
        }

        // Render the profile page with the form and posts
        return $this->render('public/profile.html.twig', [
            'artiste' => $user,
            'form' => $form->createView(),
            'posts' => $posts,
        ]);
    }


}
