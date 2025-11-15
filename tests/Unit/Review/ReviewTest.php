<?php

namespace App\Tests\Entity;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReviewTest extends KernelTestCase
{
    private function getValidator(): ValidatorInterface
    {
        self::bootKernel();
        return static::getContainer()->get('validator');
    }

    private function createValidReview(): Review
    {
        $review = new Review();
        $review->setRating(4);
        $review->setComment("Super jeu !");
        $review->setUser(new User());
        $review->setVideoGame(new VideoGame());

        return $review;
    }

    public function testValidReview(): void
    {
        $validator = $this->getValidator();
        $review = $this->createValidReview();

        $errors = $validator->validate($review);
        $this->assertCount(0, $errors);
    }

    public function testRatingIsRequired(): void
    {
        $validator = $this->getValidator();
        $review = $this->createValidReview();
        $review->setRating(null);

        $errors = $validator->validate($review);
        $this->assertGreaterThan(0, count($errors));
    }

    public function testRatingMustBeBetween1And5(): void
    {
        $validator = $this->getValidator();
        $review = $this->createValidReview();

        // Note trop basse
        $review->setRating(0);
        $errors = $validator->validate($review);
        $this->assertGreaterThan(0, count($errors));

        // Note trop haute
        $review->setRating(6);
        $errors = $validator->validate($review);
        $this->assertGreaterThan(0, count($errors));
    }

    public function testCommentIsOptional(): void
    {
        $validator = $this->getValidator();
        $review = $this->createValidReview();
        $review->setComment(null);

        $errors = $validator->validate($review);
        $this->assertCount(0, $errors);
    }

    public function testGetterSetter(): void
    {
        $review = new Review();

        $review
            ->setRating(5)
            ->setComment("Excellent");

        $this->assertSame(5, $review->getRating());
        $this->assertSame("Excellent", $review->getComment());
    }
}
