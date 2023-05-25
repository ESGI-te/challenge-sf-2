<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user',name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'profile_private', methods: ['GET'])]
    public function show(Security $security): Response
    {
        $user = $security->getUser();
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Security $security,Request $request,UserService $userService): Response
    {
        $user = $security->getUser();
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userService->update($user);

            return $this->redirectToRoute('user_profile_private', [
                'id' => $user->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/recipes', name: 'recipes', methods: ['GET'])]
    public function recipes(Security $security): Response
    {
        $user = $security->getUser();
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }
        return $this->render('user/recipes.html.twig', [
            'user' => $user,
            'recipes' => $user->getRecipes()
        ]);
    }


}
