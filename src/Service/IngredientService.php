<?php

namespace App\Service;

use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class IngredientService
{
    private Security $security;
    private EntityManagerInterface $em;

    public function __construct(
        RecipeGenerationService $recipeGenerationService,
        EntityManagerInterface $em,
        Security $security,
        IngredientRepository $ingredientRepository
    ) {
        $this->em = $em;
        $this->security = $security;
    }

    public function getIngredientsWithDetails(): array
    {
        $query = $this->em->createQuery('
            SELECT i.id, i.name, ti.name AS type_name
            FROM App\Entity\Ingredient i
            JOIN App\Entity\IngredientType ti 
            WITH ti = i.type
        ');
        return $query->getResult();
    }

}