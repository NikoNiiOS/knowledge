<?php

namespace App\Tests;

use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PurchaseTest extends WebTestCase
{
    private function createTestData(): array
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        // Generate a fake user
        $user = new User();
        $user->setEmail('test_purchase_' . uniqid() . '@example.com');
        $user->setPassword($passwordHasher->hashPassword($user, 'Password123!'));
        $user->setIsVerified(true);
        $entityManager->persist($user);

        // Generate hierarchy
        $theme = new Theme();
        $theme->setName('Test Theme');
        $entityManager->persist($theme);

        $course = new Course();
        $course->setTitle('Test Course');
        $course->setPrice(50);
        $course->setTheme($theme);
        $entityManager->persist($course);

        $lesson = new Lesson();
        $lesson->setTitle('Test Lesson');
        $lesson->setPrice(20);
        $lesson->setCourse($course);
        $lesson->setContent('<p>Lorem ipsum dolor sit amet.</p>');
        $lesson->setVideo('https://www.youtube.com/embed/dQw4w9WgXcQ');
        
        $entityManager->persist($lesson);

        // Save in database(test)
        $entityManager->flush();

        return ['user' => $user, 'lesson' => $lesson];
    }

    public function testPurchaseRequiresLogin(): void
    {
        $client = static::createClient();
        
        $data = $this->createTestData();
        $lesson = $data['lesson'];

        $client->request('GET', '/checkout/lesson/' . $lesson->getId());
        
        $this->assertResponseRedirects('/login');
    }

    public function testPurchaseRedirectsToStripeForAuthenticatedUser(): void
    {
        $client = static::createClient();
        
        $data = $this->createTestData();
        $user = $data['user'];
        $lesson = $data['lesson'];

        $client->loginUser($user);

        $client->request('GET', '/checkout/lesson/' . $lesson->getId());
        
        $this->assertResponseStatusCodeSame(303); 
    }
}