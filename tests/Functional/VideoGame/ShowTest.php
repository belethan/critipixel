<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\VideoGame;

final class ShowTest extends WebTestBase
{
    public function testShouldShowVideoGame(): void
    {
        // Récupérer un jeu existant
        $game = $this->em->getRepository(VideoGame::class)->findOneBy([]);
        self::assertNotNull($game, 'Aucun jeu en base de test.');

        // Accéder à la page via le slug réel
        $this->get('/'.$game->getSlug());

        // Vérifier que la page charge correctement
        self::assertResponseIsSuccessful();

        // Vérifier que <h1> contient le vrai titre
        self::assertSelectorTextContains('h1', $game->getTitle());
    }
}
