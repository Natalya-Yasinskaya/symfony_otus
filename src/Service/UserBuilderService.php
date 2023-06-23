<?php

namespace App\Service;

use App\Entity\User;
use App\Manager\SubscriptionManager;
use App\Manager\PostManager;
use App\Manager\UserManager;


class UserBuilderService
{
    public function __construct(
        private readonly PostManager $postManager,
        private readonly UserManager $userManager,
        private readonly SubscriptionManager $subscriptionManager,
    )
    {
    }

   /**
     * @param string[] $texts
     */
    public function createUserWithPosts(string $login, array $posts): User
    {
        $user = $this->userManager->create($login);
        foreach ($texts as $text) {
            $this->postManager->createPost($user, $post);
        }

        return $user;
    }

    /**
     * @return User[]
     */
    public function createUserWithFollower(string $login, string $followerLogin): array
    {
        $user = $this->userManager->create($login);
        $follower = $this->userManager->create($followerLogin);
        $this->userManager->subscribeUser($user, $follower);

        return [$user, $follower];
    }
}