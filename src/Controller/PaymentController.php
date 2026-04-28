<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Controller handling e-commerce features and Stripe payment gateway integrations.
 */
class PaymentController extends AbstractController
{
    /**
     * Initiates a Stripe Checkout session for a specific lesson purchase.
     * Creates a pending Purchase entity before redirecting to Stripe.
     *
     * @param Lesson $lesson The lesson being purchased
     * @param EntityManagerInterface $entityManager The entity manager
     * @param UrlGeneratorInterface $urlGenerator Generates absolute URLs for Stripe callbacks
     * @return Response Redirects the user to the Stripe payment page (HTTP 303)
     */
    #[Route('/checkout/lesson/{id}', name: 'app_checkout_lesson')]
    public function checkoutLesson(Lesson $lesson, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $purchase = new Purchase();
        $purchase->setUser($user);
        $purchase->setLesson($lesson);
        $purchase->setIsPaid(false);

        $entityManager->persist($purchase);
        $entityManager->flush();

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Lesson: ' . $lesson->getTitle(),
                    ],
                    'unit_amount' => $lesson->getPrice() * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $urlGenerator->generate('app_payment_success', ['id' => $purchase->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $urlGenerator->generate('app_payment_cancel', ['id' => $purchase->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $purchase->setStripeSessionId($session->id);
        $entityManager->flush();

        return $this->redirect($session->url, 303);
    }

    /**
     * Initiates a Stripe Checkout session for an entire course.
     *
     * @param Course $course The course being purchased
     * @param EntityManagerInterface $entityManager The entity manager
     * @param UrlGeneratorInterface $urlGenerator Generates absolute URLs for Stripe callbacks
     * @return Response Redirects the user to the Stripe payment page (HTTP 303)
     */
    #[Route('/checkout/course/{id}', name: 'app_checkout_course')]
    public function checkoutCourse(Course $course, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $purchase = new Purchase();
        $purchase->setUser($user);
        $purchase->setCourse($course);
        $purchase->setIsPaid(false);

        $entityManager->persist($purchase);
        $entityManager->flush();

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Course: ' . $course->getTitle(),
                    ],
                    'unit_amount' => $course->getPrice() * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $urlGenerator->generate('app_payment_success', ['id' => $purchase->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $urlGenerator->generate('app_payment_cancel', ['id' => $purchase->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $purchase->setStripeSessionId($session->id);
        $entityManager->flush();

        return $this->redirect($session->url, 303);
    }

    /**
     * Handles the successful payment callback from Stripe.
     * Updates the purchase status to paid in the database.
     *
     * @param Purchase $purchase The pending purchase entity
     * @param EntityManagerInterface $entityManager The entity manager
     * @return Response Renders the success confirmation view
     */
    #[Route('/payment/success/{id}', name: 'app_payment_success')]
    public function success(Purchase $purchase, EntityManagerInterface $entityManager): Response
    {
        $purchase->setIsPaid(true);
        $entityManager->flush();

        return $this->render('payment/success.html.twig', [
            'purchase' => $purchase
        ]);
    }

    #[Route('/payment/cancel/{id}', name: 'app_payment_cancel')]
    public function cancel(Purchase $purchase): Response
    {
        return $this->render('payment/cancel.html.twig', [
            'purchase' => $purchase
        ]);
    }
}