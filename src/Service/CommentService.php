<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CommentService
{
    private EntityManagerInterface $entityManager;
    private CommentRepository $commentRepository;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, CommentRepository $commentRepository,Security $security)
    {
        $this->entityManager = $entityManager;
        $this->commentRepository = $commentRepository;
        $this->security = $security;

    }

    public function add(Recipe $recipe,string $content):Comment
    {
        $date = new \DateTimeImmutable('now');
        $comment = new Comment();
        $comment->setUserId($this->security->getUser());
        $comment->setRecipe($recipe);
        $comment->setContent($content);
        $comment->setCreatedAt($date);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }

    public function delete($commentId): void
    {
        $comment = $this->entityManager->getRepository(Comment::class)->find($commentId);
        if (!$comment) {
            throw new \InvalidArgumentException('Comment not found');
        }
        if ($comment->getUserId() !== $this->security->getUser()) {
            throw new \InvalidArgumentException('You are not authorized to delete this comment');
        }
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
    }

    public function getCommentsWithDetails(): array
    {
        $query = $this->entityManager->createQuery('
        SELECT c.id, u.email AS user_email, c.content, r.title AS recipeTitle, c.created_at
        FROM App\Entity\Comment c
        LEFT JOIN App\Entity\Recipe r WITH c.recipe = r
        LEFT JOIN App\Entity\User u WITH c.user_id = u.id
        ');
        return $query->getResult();
    }

    public function getCommentWithDetails($commentId): array
    {
        $query = $this->entityManager->createQuery('
        SELECT c.id, u.email AS user_email, c.content, r.title AS recipeTitle, c.created_at
        FROM App\Entity\Comment c
        LEFT JOIN App\Entity\Recipe r WITH c.recipe = r
        LEFT JOIN App\Entity\User u WITH c.user_id = u.id
        WHERE c.id = :commentId
        ');
        $query->setParameter('commentId', $commentId);
        return $query->getResult();
    }

}

