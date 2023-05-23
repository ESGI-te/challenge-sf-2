<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeDifficulty;
use App\Entity\RecipeDuration;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $recipes = Yaml::parseFile(__DIR__ . '/recipes.yaml');
        $users = Yaml::parseFile(__DIR__ . '/users.yaml');
        $nb_users = count($users);

        foreach ($recipes as $recipeData) {
            /** @var User $user */
            $user = $this->getReference('user_' . rand(1, $nb_users));
            /** @var RecipeDuration $duration */
            $duration = $this->getReference('duration_Normal');
            /** @var RecipeDifficulty $difficulty */
            $difficulty = $this->getReference('difficulty_Normal');
            $createdAt = new \DateTimeImmutable($recipeData['created_at']);

            $recipe = new Recipe();
            $recipe->setTitle($recipeData['title']);
            $recipe->setContent($recipeData['content']);
            $recipe->setImage($recipeData['image']);
            $recipe->setCreatedAt($createdAt);
            $recipe->setDifficulty($difficulty);
            $recipe->setDuration($duration);
            $recipe->setNbPeople($recipeData['nb_people']);
            $recipe->setUserId($user);

            foreach ($recipeData['ingredients'] as $ingredient) {
                /** @var Ingredient $ingredientEntity */
                $ingredientEntity = $this->getReference('ingredient_' . $ingredient['name']);
                $recipe->addIngredient($ingredientEntity);
            }
            $manager->persist($recipe);

        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            IngredientFixtures::class,
            RecipeDifficultyFixtures::class,
            RecipeDurationFixtures::class
        ];
    }
}
