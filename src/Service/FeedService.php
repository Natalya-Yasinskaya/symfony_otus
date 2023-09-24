<?php

namespace App\Service;

use App\Entity\Feed;
use App\Entity\POst;
use App\Entity\User;
use App\Manager\PostManager;
use Doctrine\ORM\EntityManagerInterface;

class FeedService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SubscriptionService $subscriptionService,
        private readonly AsyncService $asyncService,
        private readonly PostManager $postManager,
    )
    {
    }

    public function getFeed(int $userId, int $count): array
    {
        $feed = $this->getFeedFromRepository($userId);

        return $feed === null ? [] : array_slice($feed->getPosts(), -$count);
    }

    public function spreadPostAsync(Post $post): void
    {
        $this->asyncService->publishToExchange(AsyncService::PUBLISH_POST, $post->toAMPQMessage());
    }

    public function spreadPostSync(Post $post): void
    {
        $followerIds = $this->subscriptionService->getFollowerIds($post->getAuthor()->getId());

        foreach ($followerIds as $followerId) {
            $this->putPost($post, $followerId);
        }
    }

    public function putPost(Post $post, int $userId): bool
    {
        $feed = $this->getFeedFromRepository($userId);
        if ($feed === null) {
            return false;
        }
        $posts = $feed->getPosts();
        $posts[] = $post->toFeed();
        $feed->setPosts($posts);
        $this->entityManager->persist($feed);
        $this->entityManager->flush();

        return true;
    }

    private function getFeedFromRepository(int $userId): ?Feed
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $reader = $userRepository->find($userId);
        if (!($reader instanceof User)) {
            return null;
        }

        $feedRepository = $this->entityManager->getRepository(Feed::class);
        $feed = $feedRepository->findOneBy(['reader' => $reader]);
        if (!($feed instanceof Feed)) {
            $feed = new Feed();
            $feed->setReader($reader);
            $feed->setPosts([]);
        }

        return $feed;
    }

    public function getFeedFromPosts(int $userId, int $count): array
    {
        return $this->postManager->getFeed($this->subscriptionService->getAuthorIds($userId), $count);
    }
}