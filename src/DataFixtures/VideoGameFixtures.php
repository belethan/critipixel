<?php

namespace App\DataFixtures;

use App\Model\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VideoGameFixtures extends Fixture
{
    public const GAME_REF = 'game-';

    public function load(ObjectManager $manager): void
    {
        $basePath = __DIR__ . '/../../public/images/video_games';

        for ($i = 0; $i < 50; $i++) {

            $game = new VideoGame();

            $title = "Jeu Vidéo {$i}";
            $slug  = "jeu-video-{$i}";

            // Nom attendu du fichier
            $fileName = "video_game_{$i}.png";
            $filePath = $basePath . '/' . $fileName;

            // Taille du fichier si présent
            $imageSize = file_exists($filePath) ? filesize($filePath) : null;

            $game->setTitle($title);
            $game->setDescription("Description du jeu vidéo numéro {$i}");
            $game->setSlug($slug);

            // Date fictive valide
            $game->setReleaseDate(new \DateTimeImmutable('2020-01-01'));

            //on fixe imageName AVEC l’extension
            $game->setImageName($fileName);
            $game->setImageSize($imageSize);

            $manager->persist($game);

            $this->addReference(self::GAME_REF . $i, $game);
        }

        $manager->flush();
    }
}
