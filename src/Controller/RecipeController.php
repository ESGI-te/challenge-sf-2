<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Service\CommentService;
use App\Service\RecipeService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/recipes', name: 'recipe_')]
class RecipeController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CommentService $commentService;

    public function __construct(EntityManagerInterface $entityManager, CommentService $commentService, RecipeService $recipeService)
    {
        $this->entityManager = $entityManager;
        $this->commentService = $commentService;
        $this->recipeService = $recipeService;
    }



    #[Route('/show/{id}', name: 'show')]
    public function index($id): Response
    {
        $recipe = $this->entityManager->getRepository(Recipe::class)->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('Recipe not found');
        }

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipe,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(Recipe $recipe, Request $request): Response
    {
        $commentForm = $this->createForm(CommentType::class);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $content = $commentForm->get('content')->getData();
            $this->commentService->add($recipe, $content);
            $this->addFlash('success', 'Comment added successfully!');
        }

        return $this->render('recipe/index.html.twig', [
            'recipe' => $recipe,
            'comment_form' => $commentForm->createView(),
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/generate', name: 'generate', priority: 2)]
    public function generate(Request $request): Response
    {
        $user = $this->entityManager->find(User::class, $this->getUser());
        if ($user && $user->getNbToke() < 1) {
            return $this->render('recipe/insuficient-credit.html.twig');
        }
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $generatedRecipe = $this->recipeService->create($recipe);
            if ($generatedRecipe !== null)
            {
                return $this->redirectToRoute('recipe_show', ['id' => $generatedRecipe->getId()]);
            }
            else
            {
                return $this->redirectToRoute('stripe_max', []);
            }

        }

        return $this->render('recipe/generate.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/comment/delete', name: 'comment_delete', methods: ['DELETE'])]
    public function commentDelete(Request $request, Recipe $recipe): Response
    {
        $commentId = $request->query->get('commentId');
        if ($this->isCsrfTokenValid('delete'.$commentId, $request->request->get('_token'))) {
            $this->commentService->delete($commentId);
            $this->addFlash('success', 'Comment deleted successfully!');
        }
        return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
    }
    
    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
            $recipeRepository->remove($recipe, true);
        }
        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/search', name: 'search', priority: 1)]
    public function search(Request $request)
    {
        $query = $request->get('query');
        $recipes = $this->recipeService->searchByTitle($query);

        return $this->render('search-list.html.twig', [
            'recipes' => $recipes,
            'sort' => $request->query->get('sort', 'created_at')
        ]);
    }
}
