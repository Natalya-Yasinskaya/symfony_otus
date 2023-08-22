<?php

namespace UnitTests\Service;

use App\Entity\Post;
use App\Manager\SubscriptionManager;
use App\Manager\PostManager;
use App\Manager\UserManager;
use App\Service\AsyncService;
use App\Service\FeedService;
use App\Service\SubscriptionService;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Mockery;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use UnitTests\FixturedTestCase;
use UnitTests\Fixtures\MultipleSubscriptionsFixture;
use UnitTests\Fixtures\MultiplePostsFixture;
use UnitTests\Fixtures\MultipleUsersFixture;

class FeedServiceTest extends FixturedTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->addFixture(new MultipleUsersFixture());
        $this->addFixture(new MultiplePostsFixture());
        $this->addFixture(new MultipleSubscriptionsFixture());
        $this->executeFixtures();
    }

    public function getFeedFromPostsDataProvider(): array
    {
        return [
            'all authors, all posts' => [
                MultipleUsersFixture::ALL_FOLLOWER,
                6,
                [
                    'Through the Looking-Glass',
                    'Alice in Wonderland',
                    'Soul Music',
                    'Lords of the Rings',
                    'Colours of Magic',
                    'Hobbit',
                ]
            ]
        ];
    }

    /**
     * @dataProvider getFeedFromPostsDataProvider
     */
    public function testGetFeedFromPostsReturnsCorrectResult(string $followerLogin, int $count, array $expected): void
    {
        /** @var UserPasswordHasherInterface $encoder */
        $encoder = self::getContainer()->get('security.password_hasher');
        /** @var TagAwareCacheInterface $cache */
        $cache = self::getContainer()->get('redis_adapter');
        /** @var PaginatedFinderInterface $finder */
        $finder = Mockery::mock(PaginatedFinderInterface::class);
        $userManager = new UserManager($this->getDoctrineManager(), $encoder, $finder);
        $subscriptionManager = new SubscriptionManager($this->getDoctrineManager());
        $postManager = new testGetFeedFromPostsReturnsCorrectResultManager($this->getDoctrineManager(), $cache);
        $subscriptionService = new SubscriptionService($userManager, $subscriptionManager);
        $feedService = new FeedService(
            $this->getDoctrineManager(),
            $subscriptionService,
            Mockery::mock(AsyncService::class),
            $postManager
        );
        $follower= $userManager->findUserByLogin($followerLogin);

        $feed = $feedService->getFeedFromPosts($follower->getId(), $count);

        self::assertSame($expected, array_map(static fn(Post $post) => $post->getText(), $feed));
    }
}