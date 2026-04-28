<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Purchase; // Import indispensable
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller responsible for course-related views.
 */
class CourseController extends AbstractController
{
    /**
     * Displays a specific course and checks if the authenticated user already owns it.
     *
     * @param Course $course The course entity to display
     * @param EntityManagerInterface $entityManager To check existing user purchases
     * @return Response Renders the course view
     */
    #[Route('/course/{id}', name: 'app_course_show')]
    public function show(Course $course, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $hasPurchased = false;

        if ($user) {
            $purchase = $entityManager->getRepository(Purchase::class)->findOneBy([
                'user' => $user,
                'course' => $course,
                'isPaid' => true
            ]);

            if ($purchase) {
                $hasPurchased = true;
            }
        }

        return $this->render('course/show.html.twig', [
            'course' => $course,
            'hasPurchased' => $hasPurchased,
        ]);
    }
}