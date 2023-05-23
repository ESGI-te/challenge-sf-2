<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\Comment1Type;
use App\Repository\CommentRepository;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository, CommentService $commentService): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentService->getCommentsWithDetails(),
        ]);
    }


    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment, CommentService $commentService): Response
    {
        $commentId = $comment->getId();
        return $this->render('comment/show.html.twig', [
            'comment' => array_slice($commentService->getCommentWithDetails($commentId),0,1)[0],
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
