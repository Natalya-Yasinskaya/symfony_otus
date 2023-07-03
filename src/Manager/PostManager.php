<?php

namespace App\Manager;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PostManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createPost(User $author, string $content): void
    {
        $post = new Post();
        $post->setAuthor($author);
        $post->setContent($content);
        $post->setCreatedAt();
        $post->setUpdatedAt();
        $author->addPost($post);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }
}
