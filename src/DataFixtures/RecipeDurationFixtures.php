<?php

namespace App\DataFixtures;

use App\Entity\RecipeDuration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecipeDurationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $durations = [
            ['name' => 'Rapide'],
            ['name' => 'Normal'],
            ['name' => 'Long']
        ];
        foreach ($durations as $duration) {
            $recipeDurationEntity = new RecipeDuration();
            $recipeDurationEntity->setName($duration['name']);
            $manager->persist($recipeDurationEntity);
            $durationRef = 'duration_' . $duration['name'];
            $this->addReference($durationRef, $recipeDurationEntity);
        }
        $manager->flush();
    }
}
