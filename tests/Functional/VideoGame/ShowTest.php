<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Model\Entity\VideoGame;
use App\Model\Entity\User;
use App\Model\Entity\Review;
use App\Tests\Functional\DatabaseTestCase;

final class ShowTest extends DatabaseTestCase
{
    public function testPosterUnAvis(): void
    {
        $em = $this->getEntityManager();

        // Récupération d'un jeu existant
        $game = $em->getRepository(VideoGame::class)->findOneBy([]);
        self::assertNotNull($game, 'Aucun jeu vidéo trouvé en base test.');

        // Simuler un user existant
        $user = new User();
        $user->setUsername('testUser');
        $user->setEmail('testuser@example.com');
        $user->setPlainPassword('Password123!');
        $em->persist($user);
        $em->flush();

        // Aller sur la page du jeu
        $this->get('/' . $game->getSlug());
        self::assertResponseIsSuccessful();

        // Soumettre un avis
        $this->client->submitForm('Poster', [
            'review[content]' => 'Très bon jeu !',
            'review[rating]' => 5,
        ]);

        // Vérifier la redirection
        self::assertResponseRedirects('/' . $game->getSlug());

        // Vérifier que l'avis existe en base
        $review = $em->getRepository(Review::class)->findOneBy([
            'user' => $user,
            'videoGame' => $game
        ]);

        self::assertNotNull($review, 'L\'avis n\'a pas été enregistré.');
    }
}
