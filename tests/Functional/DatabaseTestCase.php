<?php

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Classe de base pour les tests fonctionnels CritiPixel.
 *
 * Elle :
 * - étend FunctionalTestCase pour garder les helpers HTTP (get(), submitForm(), …)
 * - purge UNIQUEMENT la table "user"
 * - évite de casser les tests VideoGame liés aux fixtures
 * - reste compatible MySQL + SQLite (CI GitHub)
 */
abstract class DatabaseTestCase extends FunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->purgeUserTable(); // on purge seulement user, pas toute la base !
        $this->createDefaultUser();
    }

    /**
     * Purge uniquement la table user pour éviter les collisions dans RegisterTest.
     * Compatible SQLite, MySQL et GitHub Actions (coverage).
     */
    private function purgeUserTable(): void
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $platform = $conn->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            // SQLite ne supporte pas TRUNCATE + FK checks
            $conn->executeStatement('DELETE FROM user;');
            $conn->executeStatement('DELETE FROM sqlite_sequence WHERE name="user";');
        } else {
            // MySQL / MariaDB
            $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 0;');
            $conn->executeStatement('TRUNCATE TABLE user;');
            $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }
    private function createDefaultUser(): void
    {
        $em = $this->getEntityManager();

        $user = new User();
        $user->setUsername('default');
        $user->setEmail('default@example.com');
        $user->setPlainPassword('Password123!');

        $em->persist($user);
        $em->flush();
    }
    protected function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }
}
