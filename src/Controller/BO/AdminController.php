<?php

namespace App\Controller\BO;

use App\Entity\Recipe;
use App\Entity\User;
use App\Form\UserEditType;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Service\RecipeService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin',name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/users', name: 'users', methods: ['GET'])]
    public function users(UserRepository $userRepository, UserService $userService): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userService->getUsersWithDetails(),
        ]);
    }

    #[Route('/users/edit/{id}', name: 'users_edit', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, User $user, UserRepository $userRepository): Response
    {

        $form = $this->createForm(UserEditType::class, $user, [
            'admin_roles' => $user->getRoles(),
            'user_plan' => $user->getPlan(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/editAdmin.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/users/delete/{id}', name: 'users_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('admin_users', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/recipes', name: 'recipes')]
    public function listAdmin(RecipeService $recipeService): Response
    {
        $recipes = $recipeService->getRecipesWithDetails();

        if (!$recipes) {
            throw $this->createNotFoundException('Recipe not found');
        }

        return $this->render('recipe/listAdmin.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipes/delete/{id}', name: 'recipes_delete', methods: ['POST'])]
    public function deleteRecipe(Request $request, Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
            $recipeRepository->remove($recipe, true);
        }

        return $this->redirectToRoute('admin_recipes', [], Response::HTTP_SEE_OTHER);
    }
}
