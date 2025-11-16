<?php

namespace App\Tests\Unit\VideoGame;

use App\Model\Entity\VideoGame;
use App\Model\Entity\Review;
use PHPUnit\Framework\TestCase;

class VideoGameRatingTest extends TestCase
{
    private function createReview(int $rating): Review
    {
        $review = new Review();
        $review->setRating($rating);
        return $review;
    }

    public function testRatingDistribution(): void
    {
        $game = new VideoGame();

        $game->addReview($this->createReview(3));
        $game->addReview($this->createReview(4));
        $game->addReview($this->createReview(5));

        $stats = $game->getNumberOfRatingsPerValue();

        $this->assertSame(0, $stats->getNumberOfOne());
        $this->assertSame(0, $stats->getNumberOfTwo());
        $this->assertSame(1, $stats->getNumberOfThree());
        $this->assertSame(1, $stats->getNumberOfFour());
        $this->assertSame(1, $stats->getNumberOfFive());
    }

    public function testAverageIsNullBecauseItIsNotCalculatedInEntity(): void
    {
        $game = new VideoGame();

        $game->addReview($this->createReview(3));
        $game->addReview($this->createReview(5));

        // La moyenne n'est PLUS calculée automatiquement dans l'entité
        $this->assertNull(
            $game->getAverageRating(),
            'La moyenne doit être NULL tant que RatingHandler ne l’a pas calculée.'
        );
    }

    public function testDistributionWithNoRating(): void
    {
        $game = new VideoGame();
        $stats = $game->getNumberOfRatingsPerValue();

        $this->assertSame(0, $stats->getNumberOfOne());
        $this->assertSame(0, $stats->getNumberOfTwo());
        $this->assertSame(0, $stats->getNumberOfThree());
        $this->assertSame(0, $stats->getNumberOfFour());
        $this->assertSame(0, $stats->getNumberOfFive());

        // Toujours null car non calculé par l'entité
        $this->assertNull($game->getAverageRating());
    }
}
