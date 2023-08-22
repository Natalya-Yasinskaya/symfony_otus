<?php

namespace App\Manager;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class PostManager
{
    private const CACHE_TAG = 'posts';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TagAwareCacheInterface $cache,
    )
    {
    }

    public function postPost(User $author, string $text): void
    {
        $post = new Post();
        $post->setAuthor($author);
        $post->setText($text);
        $post->setCreatedAt();
        $post->setUpdatedAt();
        $author->addPost($post);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    /**
     * @return Post[]
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPosts(int $page, int $perPage): array
    {
        /** @var PostRepository $postRepository */
        $postRepository = $this->entityManager->getRepository(Post::class);

        /** @var ItemInterface $organizationsItem */
        return $this->cache->get(
            "posts_{$page}_{$perPage}",
            function(ItemInterface $item) use ($postRepository, $page, $perPage) {
                $posts = $postRepository->getPosts($page, $perPage);
                $postsSerialized = array_map(static fn(Post $post) => $post->toArray(), $posts);
                $item->set($postsSerialized);
                $item->tag(self::CACHE_TAG);

                return $postsSerialized;
            }
        );
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function savePost(int $authorId, string $text): ?Post {
        $post = new Post();
        $userRepository = $this->entityManager->getRepository(User::class);
        $author = $userRepository->find($authorId);
        if (!($author instanceof User)) {
            return null;
        }
        $post->setAuthor($author);
        $post->setText($text);
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $this->cache->invalidateTags([self::CACHE_TAG]);

        return $post;
    }

    /**
     * @param int[] $authorIds
     *
     * @return Post[]
     */
    public function getFeed(array $authorIds, int $count): array {
        /** @var PostRepository $postRepository */
        $postRepository = $this->entityManager->getRepository(Post::class);

        return $postRepository->getByAuthorIds($authorIds, $count);
    }
}
