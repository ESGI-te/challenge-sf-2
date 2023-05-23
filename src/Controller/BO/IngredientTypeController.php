<?php

namespace App\Controller\BO;

use App\Entity\IngredientType;
use App\Form\IngredientTypeType;
use App\Repository\IngredientTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/ingredientType')]
class IngredientTypeController extends AbstractController
{
    #[Route('/', name: 'app_ingredient_type_index', methods: ['GET'])]
    public function index(IngredientTypeRepository $ingredientTypeRepository): Response
    {
        return $this->render('ingredient_type/index.html.twig', [
            'ingredient_types' => $ingredientTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ingredient_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, IngredientTypeRepository $ingredientTypeRepository): Response
    {
        $ingredientType = new IngredientType();
        $form = $this->createForm(IngredientTypeType::class, $ingredientType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredientTypeRepository->save($ingredientType, true);

            return $this->redirectToRoute('app_ingredient_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ingredient_type/new.html.twig', [
            'ingredient_type' => $ingredientType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ingredient_type_show', methods: ['GET'])]
    public function show(IngredientType $ingredientType): Response
    {
        return $this->render('ingredient_type/show.html.twig', [
            'ingredient_type' => $ingredientType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ingredient_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IngredientType $ingredientType, IngredientTypeRepository $ingredientTypeRepository): Response
    {
        $form = $this->createForm(IngredientTypeType::class, $ingredientType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredientTypeRepository->save($ingredientType, true);

            return $this->redirectToRoute('app_ingredient_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ingredient_type/edit.html.twig', [
            'ingredient_type' => $ingredientType,
            'form' => $form,
        ]);
    }

}
