<?php

namespace App\Service;

use App\Entity\Recipe;

class RecipeGenerationService
{
    public function __construct(OpenAIService $openAIService) {
        $this->openAiService = $openAIService;
    }

    public function generateContent(Recipe $recipe): string {
        if (!$recipe->getIngredients()) {
            throw new \InvalidArgumentException("No Ingredients provided");
        }
        $ingredients = $this->getIngredientNamesFormated($recipe->getIngredients()->toArray());
        $duration = $recipe->getDuration()->getName() ?? 'undefined';
        $difficulty = $recipe->getDifficulty()->getName() ?? 'undefined';
        $nb_people = $recipe->getNbPeople() ?? 2;

        $prompt = "
            Créé une recette de cuisine d'une difficulté $difficulty, pour $nb_people personne(s).
            Celle-ci doit être adaptée à une durée $duration et doit être basée sur les ingrédients suivants : $ingredients.
            Ne rajoute pas de titre. J'aimerais que tu ne me renvoie que la recette et pas d'autre texte et que ton retour soit en format html et insère 2 balises <br> entre les différentes étapes.
        ";

        return $this->openAiService->generateText($prompt);
    }

    public function generateImage(array $ingredients, string $title): string {
        if (!isset($ingredients)) throw new \InvalidArgumentException("Le paramètre 'ingredients' est manquant ou n'est pas un tableau.");
        if (!isset($title)) throw new \InvalidArgumentException("Le paramètre 'title' est manquant ou n'est pas un chaîne de caractères.");

        $recipeIngredients = $this->getIngredientNamesFormated($ingredients);

        $prompt = "Crée une image de présentation pour une recette intitulée $title et composée des ingrédients suivants : $recipeIngredients.";

        return $this->openAiService->generateImage($prompt);
    }

    public function generateTitle(array $ingredients): string {
        if (!isset($ingredients)) throw new \InvalidArgumentException("Le paramètre 'ingredients' est manquant ou n'est pas un tableau.");

        $recipeIngredients = $this->getIngredientNamesFormated($ingredients);

        $prompt = "Crée un titre élaboré mais court pour une recette de cuisine composée des ingrédients suivants : $recipeIngredients. Celui-ci ne doit pas excéder 80 caractères";

        return $this->openAiService->generateText($prompt);
    }

    private function getIngredientNamesFormated(array $ingredients): string {
        $ingredientNames = array_map(fn($ingredient) => $ingredient->getName(), $ingredients);
        return implode(', ', $ingredientNames);
    }
}