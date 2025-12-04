<?php

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

abstract class DatabaseTestCase extends FunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Charge automatiquement toutes les fixtures
        static::getContainer()
            ->get(DatabaseToolCollection::class)
            ->get()
            ->loadFixtures([
                \App\DataFixtures\UserFixtures::class,
                \App\DataFixtures\TagFixtures::class,
                \App\DataFixtures\VideoGameFixtures::class,
                \App\DataFixtures\ReviewFixtures::class,
            ]);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }
}
