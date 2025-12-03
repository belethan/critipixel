<?php

declare(strict_types=1);

namespace App\Tests\Unit\Stats;

use App\Model\Entity\NumberOfRatingPerValue;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CalculNoteParValeurTest extends TestCase
{
    #[DataProvider('fournirJeuxVideos')]
    public function testDoitCompterLesNotesParValeur(
        VideoGame $videoGame,
        NumberOfRatingPerValue $expected
    ): void {
        $handler = new RatingHandler();
        $handler->countRatingsPerValue($videoGame);

        self::assertEquals($expected, $videoGame->getNumberOfRatingsPerValue());
    }

    /**
     * Fournit les cas de test.
     *
     * @return iterable<array{0: VideoGame, 1: NumberOfRatingPerValue}>
     */
    public static function fournirJeuxVideos(): iterable
    {
        yield 'Aucun avis' => [
            new VideoGame(),
            new NumberOfRatingPerValue(),
        ];

        yield 'Un avis' => [
            self::creerJeuVideo(5),
            self::etatAttendu(cinq: 1),
        ];

        yield 'Plusieurs avis' => [
            self::creerJeuVideo(1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5),
            self::etatAttendu(1, 2, 3, 4, 5),
        ];
    }

    private static function creerJeuVideo(int ...$notes): VideoGame
    {
        $jeu = new VideoGame();

        foreach ($notes as $note) {
            $review = (new Review())->setRating($note);
            $jeu->getReviews()->add($review);
        }

        return $jeu;
    }

    private static function etatAttendu(
        int $un = 0,
        int $deux = 0,
        int $trois = 0,
        int $quatre = 0,
        int $cinq = 0
    ): NumberOfRatingPerValue {
        $etat = new NumberOfRatingPerValue();

        for ($i = 0; $i < $un; ++$i) {
            $etat->increaseOne();
        }
        for ($i = 0; $i < $deux; ++$i) {
            $etat->increaseTwo();
        }
        for ($i = 0; $i < $trois; ++$i) {
            $etat->increaseThree();
        }
        for ($i = 0; $i < $quatre; ++$i) {
            $etat->increaseFour();
        }
        for ($i = 0; $i < $cinq; ++$i) {
            $etat->increaseFive();
        }

        return $etat;
    }
}
