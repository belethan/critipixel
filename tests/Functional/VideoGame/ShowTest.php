<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\VideoGame;
use App\Tests\Functional\DatabaseTestCase;

final class ShowTest extends DatabaseTestCase
{
    public function testShouldShowVideoGame(): void
    {
        $game = $this->getEntityManager()->getRepository(VideoGame::class)->findOneBy([]);
        self::assertNotNull($game, 'Aucun jeu en base de test.');

        $this->get('/' . $game->getSlug());

        self::assertResponseIsSuccessful();

        self::assertSelectorTextContains('h1', $game->getTitle());
    }
}
