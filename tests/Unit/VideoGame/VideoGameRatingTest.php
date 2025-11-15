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

    public function testAverageWithMultipleRatings(): void
    {
        $game = new VideoGame();
        $game->addReview($this->createReview(3));
        $game->addReview($this->createReview(4));
        $game->addReview($this->createReview(5));

        // (3 + 4 + 5) / 3 = 4
        $this->assertSame(4.0, $game->getAverageRating());
    }

    public function testAverageWithOneRating(): void
    {
        $game = new VideoGame();
        $game->addReview($this->createReview(2));

        $this->assertSame(2.0, $game->getAverageRating());
    }

    public function testAverageWithNoRating(): void
    {
        $game = new VideoGame();

        // Selon ton implémentation : null, 0.0, ou 0
        // Ici on part du principe que la moyenne = 0.0
        $this->assertSame(0.0, $game->getAverageRating());
    }

    public function testAverageWithExtremeValues(): void
    {
        $game = new VideoGame();
        $game->addReview($this->createReview(1));
        $game->addReview($this->createReview(5));

        // (1 + 5) / 2 = 3
        $this->assertSame(3.0, $game->getAverageRating());
    }
}

