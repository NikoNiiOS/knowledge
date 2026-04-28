<?php

namespace App\Controller;

use App\Repository\ThemeRepository;
use App\Entity\LessonValidation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller responsible for the user's personal dashboard and progress tracking.
 */
class DashboardController extends AbstractController
{
    /**
     * Calculates and displays the user's progress across all themes and courses.
     * Determines if a course is completed and if a certification is acquired.
     *
     * @param ThemeRepository $themeRepository Repository to fetch all educational content
     * @param EntityManagerInterface $entityManager Repository to fetch user validations
     * @return Response Renders the dashboard view with calculated statistics
     */
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ThemeRepository $themeRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $validations = $entityManager->getRepository(LessonValidation::class)->findBy(['user' => $user]);
        
        $validatedLessonIds = [];
        foreach ($validations as $v) {
            $validatedLessonIds[] = $v->getLesson()->getId();
        }

        $allThemes = $themeRepository->findAll();
        $userProgress = [];

        foreach ($allThemes as $theme) {
            $themeLessonsCount = 0;
            $themeValidatedCount = 0;
            $coursesData = [];

            foreach ($theme->getCourses() as $course) {
                $courseLessonsCount = count($course->getLessons());
                $courseValidatedCount = 0;

                foreach ($course->getLessons() as $lesson) {
                    $themeLessonsCount++;
                    if (in_array($lesson->getId(), $validatedLessonIds)) {
                        $courseValidatedCount++;
                        $themeValidatedCount++;
                    }
                }

                $coursesData[] = [
                    'title' => $course->getTitle(),
                    'isCompleted' => ($courseLessonsCount > 0 && $courseLessonsCount === $courseValidatedCount),
                    'progress' => $courseLessonsCount > 0 ? round(($courseValidatedCount / $courseLessonsCount) * 100) : 0
                ];
            }

            $userProgress[] = [
                'themeName' => $theme->getName(),
                'isCertified' => ($themeLessonsCount > 0 && $themeLessonsCount === $themeValidatedCount),
                'courses' => $coursesData
            ];
        }

        return $this->render('dashboard/index.html.twig', [
            'userProgress' => $userProgress,
        ]);
    }
}