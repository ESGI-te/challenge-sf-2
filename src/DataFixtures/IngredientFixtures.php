<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class IngredientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $ingredientTypes = [
            'Fish' => $this->getReference('ingredient_type_Fish'),
            'Vegetables' => $this->getReference('ingredient_type_Vegetables'),
            'Meat' => $this->getReference('ingredient_type_Meat'),
            'Fruits' => $this->getReference('ingredient_type_Fruits'),
            'Grains' => $this->getReference('ingredient_type_Grains'),
            'Herbs' => $this->getReference('ingredient_type_Herbs'),
            'Spices' => $this->getReference('ingredient_type_Spices'),
            'Sweets' => $this->getReference('ingredient_type_Sweets'),
        ];

        $ingredients = Yaml::parseFile(__DIR__ . '/ingredients.yaml');

        foreach($ingredients as $ingredient) {
            $ingredientEntity = new Ingredient();
            $ingredientEntity->setName($ingredient['name']);
            $ingredientEntity->setType($ingredientTypes[$ingredient['type']]);
            $manager->persist($ingredientEntity);
            $ingredientEntityRef = 'ingredient_' . $ingredient['name'];
            $this->addReference($ingredientEntityRef, $ingredientEntity);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            IngredientTypeFixtures::class,
        ];
    }

}
