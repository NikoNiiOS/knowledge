<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationTest extends WebTestCase
{
    public function testRegistrationEmailAndLogin(): void
    {
        // Virtual Client
        $client = static::createClient();

        // Generating dynamic mail
        $testEmail = 'student_' . uniqid() . '@knowledge.com';

        // Registration page
        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();

        // Fill and submit form
        $client->submitForm('Register', [
            'registration_form[email]' => $testEmail,
            'registration_form[plainPassword]' => 'Password123!',
            'registration_form[agreeTerms]' => true,
        ]);

        // --- DEBUGGER ---
        // If the form fails (e.g. validation error) -> won't redirect (HTTP 302).
        // dump page text in the console to read the exact error.
        if ($client->getResponse()->getStatusCode() !== 302) {
            dd(strip_tags($client->getResponse()->getContent()));
        }
        // ----------------

        // Assert infos
        $this->assertEmailCount(1);
        $this->assertResponseRedirects('/home');
        $client->followRedirect();

        // simulate email verification
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => $testEmail]);
        $user->setIsVerified(true);
        static::getContainer()->get('doctrine')->getManager()->flush();

        // Login page
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        // Fill and submit form
        $client->submitForm('Sign in', [
            'email' => $testEmail,
            'password' => 'Password123!',
        ]);

        // Assert redirection
        $this->assertResponseRedirects('/home');
        $client->followRedirect();
        
        // Check security token
        $this->assertNotNull(static::getContainer()->get('security.token_storage')->getToken());
    }
}