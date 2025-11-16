<?php

declare(strict_types=1);

namespace App\Tests\Unit\Stats;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AverageRatingCalculatorTest extends TestCase
{
    #[DataProvider('provideVideoGame')]
    public function testShouldCalculateAverageRating(VideoGame $videoGame, ?int $expectedAverageRating): void
    {
        $ratingHandler = new RatingHandler();
        $ratingHandler->calculateAverage($videoGame);

        self::assertSame($expectedAverageRating, $videoGame->getAverageRating());
    }

    /**
     * @return iterable<array{0: VideoGame, 1: ?int}>
     */
    public static function provideVideoGame(): iterable
    {
        // Cas 1 : aucun avis → moyenne null
        yield 'No review' => [
            new VideoGame(),
            null,
        ];

        // Cas 2 : un seul avis → moyenne = note unique
        yield 'One review' => [
            self::createVideoGame(5),
            5,
        ];

        // Cas 3 : plusieurs avis → moyenne arrondie à 4
        yield 'Multiple reviews' => [
            self::createVideoGame(1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5),
            4,
        ];
    }

    private static function createVideoGame(int ...$ratings): VideoGame
    {
        $videoGame = new VideoGame();

        foreach ($ratings as $rating) {
            $review = (new Review())->setRating($rating);
            $videoGame->getReviews()->add($review);
        }

        return $videoGame;
    }
}
