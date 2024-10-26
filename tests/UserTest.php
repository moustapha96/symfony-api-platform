<?php


// tests/UserTest.php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testCreateUser()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        // Créez un nouvel utilisateur
        $user = new User();
        $user->setUsername('testuser')
            ->setEmail('testuser@example.com')
            ->setPassword('password'); // Assurez-vous de hasher le mot de passe en production


        // Persistez l'utilisateur
        $entityManager->persist($user);
        $entityManager->flush();
    }
}
