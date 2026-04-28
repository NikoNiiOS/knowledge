<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\LessonValidation;
use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller responsible for displaying and validating lessons.
 */
class LessonController extends AbstractController
{
    /**
     * Displays a specific lesson and verifies if the user has purchased or validated it.
     *
     * @param Lesson $lesson The lesson entity to display
     * @param EntityManagerInterface $entityManager The entity manager for database operations
     * @return Response Renders the lesson view
     */
    #[Route('/lesson/{id}', name: 'app_lesson_show')]
    public function show(Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $isValidated = false;
        $hasPurchased = false;

        if ($user) {
            $validation = $entityManager->getRepository(LessonValidation::class)->findOneBy([
                'user' => $user,
                'lesson' => $lesson
            ]);
            if ($validation) {
                $isValidated = true;
            }

            $lessonPurchase = $entityManager->getRepository(Purchase::class)->findOneBy([
                'user' => $user,
                'lesson' => $lesson,
                'isPaid' => true
            ]);

            $coursePurchase = $entityManager->getRepository(Purchase::class)->findOneBy([
                'user' => $user,
                'course' => $lesson->getCourse(),
                'isPaid' => true
            ]);

            if ($lessonPurchase || $coursePurchase) {
                $hasPurchased = true;
            }
        }

        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'isValidated' => $isValidated,
            'hasPurchased' => $hasPurchased,
        ]);
    }

    /**
     * Handles the validation process of a lesson for the currently authenticated user.
     * Prevents duplicate validations.
     *
     * @param Lesson $lesson The lesson to validate
     * @param EntityManagerInterface $entityManager The entity manager to save the validation
     * @return Response Redirects back to the lesson display page
     */
    #[Route('/lesson/{id}/validate', name: 'app_lesson_validate')]
    public function validate(Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $existingValidation = $entityManager->getRepository(LessonValidation::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson
        ]);

        if (!$existingValidation) {
            $validation = new LessonValidation();
            $validation->setUser($user);
            $validation->setLesson($lesson);

            $entityManager->persist($validation);
            $entityManager->flush();

            $this->addFlash('success', 'Congratulations! You have validated this lesson.');
        }

        return $this->redirectToRoute('app_lesson_show', ['id' => $lesson->getId()]);
    }
}