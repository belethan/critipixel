<?php

declare(strict_types=1);

namespace App\Rating;

use App\Model\Entity\VideoGame;

final readonly class RatingHandler implements CalculateAverageRating, CountRatingsPerValue
{
    public function calculateAverage(VideoGame $videoGame): void
    {
        $reviews = $videoGame->getReviews();

        if ($reviews->isEmpty()) {
            $videoGame->setAverageRating(null);

            return;
        }

        $sum = 0;
        $count = 0;

        foreach ($reviews as $review) {
            $sum += $review->getRating();
            ++$count;
        }

        // Moyenne arrondie à l'entier
        $average = (int) round($sum / $count);

        $videoGame->setAverageRating($average);
    }

    public function countRatingsPerValue(VideoGame $videoGame): void
    {
        $videoGame->getNumberOfRatingsPerValue()->clear();

        if (0 === \count($videoGame->getReviews())) {
            return;
        }

        foreach ($videoGame->getReviews() as $review) {
            match ($review->getRating()) {
                1 => $videoGame->getNumberOfRatingsPerValue()->increaseOne(),
                2 => $videoGame->getNumberOfRatingsPerValue()->increaseTwo(),
                3 => $videoGame->getNumberOfRatingsPerValue()->increaseThree(),
                4 => $videoGame->getNumberOfRatingsPerValue()->increaseFour(),
                default => $videoGame->getNumberOfRatingsPerValue()->increaseFive(),
            };
        }
    }
}
