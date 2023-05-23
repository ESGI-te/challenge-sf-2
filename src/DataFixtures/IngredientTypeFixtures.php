<?php

namespace App\DataFixtures;

use App\Entity\IngredientType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $types = [
            ['name' => 'Vegetables'],
            ['name' => 'Fruits'],
            ['name' => 'Meat'],
            ['name' => 'Fish'],
            ['name' => 'Spices'],
            ['name' => 'Herbs'],
            ['name' => 'Grains'],
            ['name' => 'Sweets']
        ];
        foreach ($types as $type) {
            $ingredientTypeEntity = new IngredientType();
            $ingredientTypeEntity->setName($type['name']);
            $manager->persist($ingredientTypeEntity);
            $ingredientTypeEntityRef = 'ingredient_type_' . $type['name'];
            $this->addReference($ingredientTypeEntityRef, $ingredientTypeEntity);
        }
        $manager->flush();
    }
}
