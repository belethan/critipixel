<?php

namespace App\DataFixtures;

use App\Model\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Faker\FrenchGeneratorFactory;

class UserFixtures extends Fixture
{
    public const USER_REF = 'user-';
    public const ADMIN_REF = 'admin';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = FrenchGeneratorFactory::create();

        // 2 utilisateurs
        for ($i = 1; $i <= 2; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setUsername($faker->unique()->userName());
            $manager->persist($user);
            $this->addReference(self::USER_REF.$i, $user);
        }

        // 1 administrateur
        $admin = new User();
        $admin->setUsername('admin');    // ✔ AJOUT CRUCIAL
        $admin->setEmail('admin@mail.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));

        $manager->persist($admin);
        $this->addReference(self::ADMIN_REF, $admin);

        $manager->flush();
    }
}

