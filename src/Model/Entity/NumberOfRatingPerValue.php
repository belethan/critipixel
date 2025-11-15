<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class NumberOfRatingPerValue
{
    #[ORM\Column(name: "number_of_ratings_per_value_number_of_one", type: "integer")]
    private int $numberOfOne = 0;

    #[ORM\Column(name: "number_of_ratings_per_value_number_of_two", type: "integer")]
    private int $numberOfTwo = 0;

    #[ORM\Column(name: "number_of_ratings_per_value_number_of_three", type: "integer")]
    private int $numberOfThree = 0;

    #[ORM\Column(name: "number_of_ratings_per_value_number_of_four", type: "integer")]
    private int $numberOfFour = 0;

    #[ORM\Column(name: "number_of_ratings_per_value_number_of_five", type: "integer")]
    private int $numberOfFive = 0;

    /* ============================================================
       MÉTHODE UNIFIÉE POUR INCRÉMENTER LA BONNE VALEUR
       ============================================================ */
    public function increment(int $rating): void
    {
        match ($rating) {
            1 => $this->numberOfOne++,
            2 => $this->numberOfTwo++,
            3 => $this->numberOfThree++,
            4 => $this->numberOfFour++,
            5 => $this->numberOfFive++,
            default => throw new \InvalidArgumentException("Invalid rating value '{$rating}'. Must be 1 to 5.")
        };
    }

    /* ============================================================
       MÉTHODES INDIVIDUELLES (conservées si tu veux les garder)
       ============================================================ */
    public function increaseOne(): void { $this->numberOfOne++; }
    public function increaseTwo(): void { $this->numberOfTwo++; }
    public function increaseThree(): void { $this->numberOfThree++; }
    public function increaseFour(): void { $this->numberOfFour++; }
    public function increaseFive(): void { $this->numberOfFive++; }

    /* ============================================================
       GETTERS
       ============================================================ */
    public function getNumberOfOne(): int { return $this->numberOfOne; }
    public function getNumberOfTwo(): int { return $this->numberOfTwo; }
    public function getNumberOfThree(): int { return $this->numberOfThree; }
    public function getNumberOfFour(): int { return $this->numberOfFour; }
    public function getNumberOfFive(): int { return $this->numberOfFive; }
}
