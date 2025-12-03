<?php

declare(strict_types=1);

namespace App\Doctrine\EntityListener;

use App\Model\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function prePersist(User $user): void
    {
        $plain = $user->getPlainPassword();

        if (!empty($plain)) {
            $hashed = $this->passwordHasher->hashPassword($user, $plain);
            $user->setPassword($hashed);
        }
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        $plain = $user->getPlainPassword();

        if (!empty($plain)) {
            $hashed = $this->passwordHasher->hashPassword($user, $plain);
            $user->setPassword($hashed);

            // force Doctrine à mettre à jour le champ
            $event->setNewValue('password', $hashed);
        }
    }
}
