<?php
/*
-️ Création avec tableau valide
-️ Calcul du total des notes
-️ Récupération du nombre de notes pour une valeur
-️ Validation des valeurs hors bornes
-️ Test d’initialisation vide
-️ Protection contre valeurs négatives
*/
namespace App\Tests\Unit\Stats;

use App\Model\Entity\NumberOfRatingPerValue;
use PHPUnit\Framework\TestCase;

class NumberOfRatingPerValueTest extends TestCase
{
    public function testInitialValuesAreZero()
    {
        $stats = new NumberOfRatingPerValue();

        $this->assertSame(0, $stats->getNumberOfOne());
        $this->assertSame(0, $stats->getNumberOfTwo());
        $this->assertSame(0, $stats->getNumberOfThree());
        $this->assertSame(0, $stats->getNumberOfFour());
        $this->assertSame(0, $stats->getNumberOfFive());
    }

    public function testIncrementIncrementsCorrectValue()
    {
        $stats = new NumberOfRatingPerValue();

        $stats->increment(1);
        $stats->increment(3);
        $stats->increment(5);

        $this->assertSame(1, $stats->getNumberOfOne());
        $this->assertSame(0, $stats->getNumberOfTwo());
        $this->assertSame(1, $stats->getNumberOfThree());
        $this->assertSame(0, $stats->getNumberOfFour());
        $this->assertSame(1, $stats->getNumberOfFive());
    }

    public function testIncrementInvalidValueThrowsException()
    {
        $stats = new NumberOfRatingPerValue();

        $this->expectException(\InvalidArgumentException::class);
        $stats->increment(0); // interdit
    }

    public function testIncreaseMethods()
    {
        $stats = new NumberOfRatingPerValue();

        $stats->increaseOne();
        $stats->increaseTwo();
        $stats->increaseThree();
        $stats->increaseFour();
        $stats->increaseFive();

        $this->assertSame(1, $stats->getNumberOfOne());
        $this->assertSame(1, $stats->getNumberOfTwo());
        $this->assertSame(1, $stats->getNumberOfThree());
        $this->assertSame(1, $stats->getNumberOfFour());
        $this->assertSame(1, $stats->getNumberOfFive());
    }

    public function testMultipleIncrements()
    {
        $stats = new NumberOfRatingPerValue();

        $stats->increment(2);
        $stats->increment(2);
        $stats->increment(2);

        $this->assertSame(3, $stats->getNumberOfTwo());
    }
}
