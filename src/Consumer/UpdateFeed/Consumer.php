<?php

namespace App\Consumer\UpdateFeed;

use App\Client\StatsdAPIClient;
use App\Consumer\UpdateFeed\Input\Message;
use App\DTO\SendNotificationDTO;
use App\Entity\Post;
use App\Entity\User;
use App\Service\AsyncService;
use App\Service\FeedService;
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
        private readonly FeedService $feedService,
        private readonly AsyncService $asyncService,
        private readonly StatsdAPIClient $statsdAPIClient,
        private readonly string $key,
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
        $userRepository = $this->entityManager->getRepository(User::class);
        $post = $postRepository->find($message->getPostId());
        if (!($post instanceof Post)) {
            return $this->reject(sprintf('Post ID %s was not found', $message->getPostId()));
        }

        $this->feedService->putPost($post, $message->getFollowerId());
        /** @var User $user */
        $user = $userRepository->find($message->getFollowerId());
        if ($user !== null) {
            $message = (new SendNotificationDTO($message->getFollowerId(), $post->getText()))->toAMQPMessage();
            $this->asyncService->publishToExchange(
                AsyncService::SEND_NOTIFICATION,
                $message,
                $user->getPreferred()
            );
        }

        $this->statsdAPIClient->increment($this->key);
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