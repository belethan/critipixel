<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Tests\Functional\VideoGame\WebTestBase;
use Symfony\Component\HttpFoundation\Response;

final class ShowTest extends WebTestBase
{
    public function testAfficheJeuVideo(): void
    {
        // Récupération d’un jeu réel de la base de test
        $game = $this->em->getRepository(VideoGame::class)->findOneBy([]);
        self::assertNotNull($game, 'Aucun jeu vidéo trouvé en base test.');

        // Accès via son SLUG
        $this->get('/'.$game->getSlug());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', $game->getTitle());
    }

    // attention un avis par user par jeu
    public function testPosterUnAvis(): void
    {
        // Utilisateur connu
        $user = $this->em->getRepository(User::class)->findOneBy([
            'email' => 'user+2@email.com',
        ]);
        self::assertNotNull($user);
        $this->client->loginUser($user);

        // Jeu existant
        $game = $this->em->getRepository(VideoGame::class)->findOneBy([]);
        self::assertNotNull($game);

        // Page du jeu
        $this->get('/'.$game->getSlug());
        self::assertResponseIsSuccessful();

        // Soumission de l'avis
        $this->submit('Poster', [
            'review[rating]' => 4,
            'review[comment]' => 'Mon commentaire',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        // 🔥 Vérification PROPRE : en base
        $review = $this->em->getRepository(\App\Model\Entity\Review::class)->findOneBy([
            'user' => $user,
            'videoGame' => $game,
        ]);

        self::assertNotNull($review);
        self::assertSame(4, $review->getRating());
        self::assertSame('Mon commentaire', $review->getComment());
    }

}
