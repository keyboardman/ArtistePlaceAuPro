<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        // Fetch all courses
        $CourseRepository = $entityManager->getRepository(Course::class);
        $courses = $CourseRepository->findAll();

        if (!$courses) {
            throw $this->createNotFoundException('Not Course found');
        }

        return $this->render('student/home.html.twig', [
            'courses' => $courses,
            'controller_name' => 'StudentController',
        ]);
    }

    #[\Symfony\Component\Routing\Annotation\Route('/course/{token}', name: 'app_studentCourse')]
    public function course(EntityManagerInterface $entityManager, string $token): Response
    {
        return $this->render('student/course.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }

}
