<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Faker\FrenchGeneratorFactory;
use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FrenchGeneratorFactory::create();

        for ($i = 1; $i <= 10; ++$i) {
            /** @var VideoGame $game */
            $game = $this->getReference(VideoGameFixtures::GAME_REF.$i, VideoGame::class);

            // Review 1
            $this->createReview($manager, $faker, $game, UserFixtures::USER_REF.'1');

            // Review 2
            $this->createReview($manager, $faker, $game, UserFixtures::USER_REF.'2');
        }

        $manager->flush();
    }

    private function createReview(ObjectManager $manager, $faker, VideoGame $game, string $userRef): void
    {
        /** @var User $user */
        $user = $this->getReference($userRef, User::class);

        $review = new Review();
        $rating = $faker->numberBetween(1, 5);

        $review->setRating($rating);
        $review->setComment($faker->optional()->sentence(15));
        $review->setVideoGame($game);
        $review->setUser($user);

        // ⭐ Mettre à jour l’embeddable NumberOfRatingPerValue
        $stats = $game->getNumberOfRatingsPerValue();

        match ($rating) {
            1 => $stats->increaseOne(),
            2 => $stats->increaseTwo(),
            3 => $stats->increaseThree(),
            4 => $stats->increaseFour(),
            5 => $stats->increaseFive(),
        };

        $manager->persist($review);
        // Pas besoin de persist($game) : Doctrine détecte la modification sur l'objet embeddé
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            VideoGameFixtures::class,
        ];
    }
}
