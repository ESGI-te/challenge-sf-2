<?php

namespace App\DataFixtures;

use App\Entity\Plan;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Yaml\Yaml;

class PlanFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $plans = Yaml::parseFile(__DIR__ . '/plans.yaml');
        foreach ($plans as $planData) {
            $plan = new Plan();
            $plan->setName($planData['name']);
            $plan->setDescription($planData['description']);
            $plan->setNbRecipe($planData['nb_recipe']);
            $plan->setPrice($planData['price']);
            $manager->persist($plan);
        }

        $manager->flush();
    }
}

