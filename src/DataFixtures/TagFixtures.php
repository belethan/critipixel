<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public const TAG_REF = 'tag-';

    public function load(ObjectManager $manager): void
    {
        $tags = ['Aventure', 'RPG', 'Action', 'Survie', 'Multijoueur'];

        foreach ($tags as $i => $name) {
            $tag = new Tag();
            $tag->setName($name);

            $manager->persist($tag);
            $this->addReference(self::TAG_REF.($i + 1), $tag);
        }

        $manager->flush();
    }
}
