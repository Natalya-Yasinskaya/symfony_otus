<?php

namespace App\Consumer\PublishPost;

use App\Consumer\PublishPost\Input\Message;
use App\Consumer\PublishPost\Output\UpdateFeedMessage;
use App\DTO\SendNotificationDTO;
use App\Entity\Post;
use App\Entity\User;
use App\Service\AsyncService;
use App\Service\FeedService;
use App\Service\SubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Consumer implements ConsumerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly SubscriptionService $subscriptionService,
        private readonly FeedService $feedService,
        private readonly AsyncService $asyncService,
    )
    {
    }

    public function execute(AMQPMessage $msg): int
    {
        try {
            $message = Message::createFromQueue($msg->getBody());
            $errors = $this->validator->validate($message);
            if ($errors->count() > 0) {
                return $this->reject((string)$errors);
            }
        } catch (JsonException $e) {
            return $this->reject($e->getMessage());
        }

        $postRepository = $this->entityManager->getRepository(Post::class);
        $post = $postRepository->find($message->getPostId());
        if (!($post instanceof Post)) {
            return $this->reject(sprintf('Post ID %s was not found', $message->getPostId()));
        }

        $followerIds = $this->subscriptionService->getFollowerIds($post->getAuthor()->getId());

        foreach ($followerIds as $followerId) {
            $message = (new UpdateFeedMessage($post->getId(), $followerId))->toAMQPMessage();
            $this->asyncService->publishToExchange(AsyncService::UPDATE_FEED, $message, (string)$followerId);
        }

        $this->entityManager->clear();
        $this->entityManager->getConnection()->close();

        return self::MSG_ACK;
    }

    private function reject(string $error): int
    {
        echo "Incorrect message: $error";

        return self::MSG_REJECT;
    }
}