<?php

namespace App\Tests\Functional;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class DatabaseTestCase extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->purgeDatabase();
    }

    protected function purgeDatabase(): void
    {
        // Récupère l'EntityManager du conteneur
        $em = static::getContainer()->get('doctrine')->getManager();

        // Purger toutes les tables Doctrine
        $purger = new ORMPurger($em);

        // Obligatoire pour SQLite, sinon TRUNCATE sera rejeté
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);

        $purger->purge();
    }

    protected function getEntityManager()
    {
        return static::getContainer()->get('doctrine')->getManager();
    }
}

