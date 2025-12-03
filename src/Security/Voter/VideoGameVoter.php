<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VideoGameVoter extends Voter
{
    public const REVIEW = 'review';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::REVIEW === $attribute && $subject instanceof VideoGame;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // On doit être connecté
        if (!$user instanceof User) {
            return false;
        }

        /** @var VideoGame $game */
        $game = $subject;

        // Si l'utilisateur a déjà laissé un avis → refus
        if ($game->hasAlreadyReview($user)) {
            return false;
        }

        // Autoriser si c'est bien un User authentifié
        return true;
    }
}
