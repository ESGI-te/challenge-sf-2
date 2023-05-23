<?php

namespace App\Controller\BO;

use App\Entity\RecipeDifficulty;
use App\Form\RecipeDifficultyType;
use App\Repository\RecipeDifficultyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/recipe/difficulty')]
class RecipeDifficultyController extends AbstractController
{
    #[Route('/', name: 'app_recipe_difficulty_index', methods: ['GET'])]
    public function index(RecipeDifficultyRepository $recipeDifficultyRepository): Response
    {
        return $this->render('recipe_difficulty/index.html.twig', [
            'recipe_difficulties' => $recipeDifficultyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recipe_difficulty_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RecipeDifficultyRepository $recipeDifficultyRepository): Response
    {
        $recipeDifficulty = new RecipeDifficulty();
        $form = $this->createForm(RecipeDifficultyType::class, $recipeDifficulty);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeDifficultyRepository->save($recipeDifficulty, true);

            return $this->redirectToRoute('app_recipe_difficulty_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recipe_difficulty/new.html.twig', [
            'recipe_difficulty' => $recipeDifficulty,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_difficulty_show', methods: ['GET'])]
    public function show(RecipeDifficulty $recipeDifficulty): Response
    {
        return $this->render('recipe_difficulty/show.html.twig', [
            'recipe_difficulty' => $recipeDifficulty,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recipe_difficulty_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RecipeDifficulty $recipeDifficulty, RecipeDifficultyRepository $recipeDifficultyRepository): Response
    {
        $form = $this->createForm(RecipeDifficultyType::class, $recipeDifficulty);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeDifficultyRepository->save($recipeDifficulty, true);

            return $this->redirectToRoute('app_recipe_difficulty_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recipe_difficulty/edit.html.twig', [
            'recipe_difficulty' => $recipeDifficulty,
            'form' => $form,
        ]);
    }


}
