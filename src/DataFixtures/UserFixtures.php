<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REF = 'user-';
    public const ADMIN_REF = 'admin';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // ====== UTILISATEURS STANDARD ======
        for ($i = 0; $i < 5; ++$i) {
            $user = new User();

            $username = "user{$i}";
            $email = "user+{$i}@email.com";

            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, 'password')
            );

            $manager->persist($user);

            // Permet d’utiliser les références dans les autres fixtures
            $this->addReference(self::USER_REF.$i, $user);
        }

        // ====== ADMIN ======
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@mail.com');
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'adminpass')
        );

        $manager->persist($admin);
        $this->addReference(self::ADMIN_REF, $admin);

        $manager->flush();
    }
}
