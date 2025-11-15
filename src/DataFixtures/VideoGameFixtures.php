<?php

namespace App\DataFixtures;

use App\Model\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Faker\FrenchGeneratorFactory;
use Symfony\Component\String\Slugger\SluggerInterface;

class VideoGameFixtures extends Fixture
{
    public const GAME_REF = 'game-';

    public function __construct(
        private SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FrenchGeneratorFactory::create();

        $games = [
            'Zelda Breath of the Wild',
            'Horizon Zero Dawn',
            'The Witcher 3',
            'Cyberpunk 2077',
            'Super Mario Odyssey',
            'God of War',
            'Elden Ring',
            'Halo Infinite',
            'Minecraft',
            'Fortnite',
        ];

        foreach ($games as $i => $title) {

            $game = new VideoGame();
            $game->setTitle($title);
            $game->setDescription($faker->paragraph(3));

            // Génération du slug OBLIGATOIRE
            $slug = $this->slugger->slug($title)->lower();
            $game->setSlug($slug);

            // Release date IMMUTABLE
            $dateMutable = $faker->dateTimeBetween('-10 years', 'now');
            $releaseDate = new \DateTimeImmutable($dateMutable->format('Y-m-d'));
            $game->setReleaseDate($releaseDate);

            // Stats embeddable → auto initialisé dans l'entité VideoGame
            // Aucune action nécessaire ici

            $manager->persist($game);
            $this->addReference(self::GAME_REF . ($i + 1), $game);
        }

        $manager->flush();
    }
}
