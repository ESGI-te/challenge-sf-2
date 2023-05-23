<?php

namespace App\DataFixtures;

use App\Entity\RecipeDifficulty;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecipeDifficultyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $difficulties = [
            ['name' => 'Facile'],
            ['name' => 'Normal'],
            ['name' => 'Difficile'],
        ];

        foreach ($difficulties as $difficulty) {
            $difficultyEntity = new RecipeDifficulty();
            $difficultyEntity->setName($difficulty['name']);
            $manager->persist($difficultyEntity);
            $difficultyRef = 'difficulty_' . $difficulty['name'];
            $this->addReference($difficultyRef, $difficultyEntity);
        }

        $manager->flush();
    }
}
