<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Entity\RecipeRequest;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\RouterInterface;

class RecipeService
{
    public function __construct(
        RecipeGenerationService $recipeGenerationService,
        RecipeRequestService $recipeRequestService,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        Security $security,
        RecipeRepository $recipeRepository,
        RouterInterface $router
    ) {
        $this->generationService = $recipeGenerationService;
        $this->em = $em;
        $this->security = $security;
        $this->recipeRepository = $recipeRepository;
        $this->recipeRequestService = $recipeRequestService;
        $this->router = $router;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function create(Recipe $recipe): ?Recipe
    {
        $user = $this->security->getUser();
        $identifier = $user->getUserIdentifier();
        $userObj = $this->userRepository->findOneBy(["email" => $identifier]);
        $request_per_day = $this->recipeRequestService->getRequestNumber($user);
        $plan = $userObj->getPlan();
        if($request_per_day >= $plan->getNbRecipe())
        {
            return null;
        }
        else
        {
            $content = $this->generationService->generateContent($recipe);
            $title = $this->generationService->generateTitle($recipe->getIngredients()->toArray());
            $image = $this->generationService->generateImage($recipe->getIngredients()->toArray(), $title);
            $date = new \DateTimeImmutable('now');

            $recipe->setContent($content);
            $recipe->setTitle($title);
            $recipe->setImage($image);
            $recipe->setUserId($user);
            $recipe->setCreatedAt($date);


            $rq = new RecipeRequest();
            $rq->setUserId($user);
            $rq->setCreatedAt($date);

            $this->em->persist($recipe);
            $this->em->persist($rq);
            $this->em->flush();

            return $recipe;
        }

    }

    public function searchByTitle(string $query): array
    {
        return $this->recipeRepository->findBySubstring('title', $query);
    }

    public function checkPremium($array):bool
    {
        if(in_array("PREMIUM",$array))
        {
            return true;
        }
        return false;
    }

    public function getRecipesWithDetails(): array
    {
        $query = $this->em->createQuery('
        SELECT r.id, d.name AS difficulty, dur.name AS duration, u.email AS user_email, r.content, r.nb_people, r.createdAt
        FROM App\Entity\Recipe r
        LEFT JOIN App\Entity\RecipeDifficulty d WITH r.difficulty = d
        LEFT JOIN App\Entity\RecipeDuration dur WITH r.duration = dur
        LEFT JOIN App\Entity\User u WITH r.user_id = u.id
        ');
        return $query->getResult();
    }
}