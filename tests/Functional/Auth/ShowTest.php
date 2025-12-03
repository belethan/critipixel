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
        // Connexion d'un utilisateur présent dans les fixtures
        $user = $this->em->getRepository(User::class)->findOneBy([
            'email' => 'user+2@email.com',
        ]);
        self::assertNotNull($user);
        $this->client->loginUser($user);

        // Récupération d’un jeu réel
        $game = $this->em->getRepository(VideoGame::class)->findOneBy([]);
        self::assertNotNull($game);

        // Aller sur la page du jeu
        $this->get('/'.$game->getSlug());
        self::assertResponseIsSuccessful();

        // Soumission du formulaire
        $this->submit('Poster', [
            'review[rating]' => 4,
            'review[comment]' => 'Mon commentaire',
        ]);

        // Redirection après succès
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        // Vérification affichage dans les avis
        self::assertSelectorTextContains('div.list-group-item:last-child p', 'Mon commentaire');
        self::assertSelectorTextContains('div.list-group-item:last-child span.value', '4');
    }
}
