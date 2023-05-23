<?php

namespace App\Controller\BO;

use App\Entity\RecipeDuration;
use App\Form\RecipeDurationType;
use App\Repository\RecipeDurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/recipe/duration')]
class RecipeDurationController extends AbstractController
{
    #[Route('/', name: 'app_recipe_duration_index', methods: ['GET'])]
    public function index(RecipeDurationRepository $recipeDurationRepository): Response
    {
        return $this->render('recipe_duration/index.html.twig', [
            'recipe_durations' => $recipeDurationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recipe_duration_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RecipeDurationRepository $recipeDurationRepository): Response
    {
        $recipeDuration = new RecipeDuration();
        $form = $this->createForm(RecipeDurationType::class, $recipeDuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeDurationRepository->save($recipeDuration, true);

            return $this->redirectToRoute('app_recipe_duration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recipe_duration/new.html.twig', [
            'recipe_duration' => $recipeDuration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_duration_show', methods: ['GET'])]
    public function show(RecipeDuration $recipeDuration): Response
    {
        return $this->render('recipe_duration/show.html.twig', [
            'recipe_duration' => $recipeDuration,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recipe_duration_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RecipeDuration $recipeDuration, RecipeDurationRepository $recipeDurationRepository): Response
    {
        $form = $this->createForm(RecipeDurationType::class, $recipeDuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeDurationRepository->save($recipeDuration, true);

            return $this->redirectToRoute('app_recipe_duration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recipe_duration/edit.html.twig', [
            'recipe_duration' => $recipeDuration,
            'form' => $form,
        ]);
    }

}
