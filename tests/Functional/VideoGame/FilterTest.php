<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\DatabaseTestCase;

final class FilterTest extends DatabaseTestCase
{
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');

        $this->client->submitForm('Filtrer', [
            'filter[search]' => 'Jeu vidéo 49',
        ], 'GET');

        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }
}
