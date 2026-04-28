<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    public function testUserRepositoryFunctionalAndSecurity(): void
    {
        // Starting symfony kernel
        self::bootKernel();
        
        // Gather data access and repo
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $entityManager = $container->get('doctrine')->getManager();

        // Preparing test data
        $email = 'security_test_' . uniqid() . '@example.com';
        
        $user = new User();
        $user->setEmail($email);
        $user->setPassword('HashedPasswordPlaceholder');
        $user->setRoles(['ROLE_ADMIN']); // Only For testing
        $user->setIsVerified(true);

        $entityManager->persist($user);
        $entityManager->flush();

        // Fonctionnal test
        // Did Repo find user ?
        $foundUser = $userRepository->findOneBy(['email' => $email]);
        
        $this->assertNotNull($foundUser, 'test failed : cannnot find user.');
        $this->assertSame($email, $foundUser->getEmail());

        // Security test
        // Verify integrity
        $this->assertContains('ROLE_ADMIN', $foundUser->getRoles(), 'test failed : sensitive role lost.');
    }
}