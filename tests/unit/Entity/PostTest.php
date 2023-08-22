<?php

namespace UnitTests\Entity;

use App\Entity\Post;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

class PostTest extends TestCase
{
    private const NOW_TIME = '@now';

    public function postDataProvider(): array
    {
        $expectedPositive = [
            'id' => 5,
            'author' => 'Terry Pratchett',
            'text' => 'The Colour of Magic',
            'createdAt' => self::NOW_TIME,
        ];
        $positivePost = $this->addAuthor($this->makePost($expectedPositive), $expectedPositive);
        $expectedNoAuthor = [
            'id' => 30,
            'author' => null,
            'text' => 'Unknown book',
            'createdAt' => self::NOW_TIME,
        ];
        $expectedNoCreatedAt = [
            'id' => 42,
            'author' => 'Douglas Adams',
            'text' => 'The Hitchhiker\'s Guide to the Galaxy',
            'createdAt' => '',
        ];
        return [
            'positive' => [
                $positivePost,
                $expectedPositive,
                0,
            ],
            'no author' => [
                $this->makePost($expectedNoAuthor),
                $expectedNoAuthor,
                0
            ],
            'no createdAt' => [
                $this->addAuthor($this->makePost($expectedNoCreatedAt), $expectedNoCreatedAt),
                $expectedNoCreatedAt,
                null
            ],
            'positive with delay' => [
                $positivePost,
                $expectedPositive,
                2
            ],
        ];
    }

    /**
     * @dataProvider postDataProvider
     * @group time-sensitive
     */
    public function testToFeedReturnsCorrectValues(Post $post, array $expected, ?int $delay = null): void
    {
        ClockMock::register(Post::class);
        if ($expected['createdAt'] === self::NOW_TIME) {
            $expected['createdAt'] = DateTime::createFromFormat('U',(string)time())->format('Y-m-d h:i:s');
        }
        $post = $this->setCreatedAtWithDelay($post, $delay);
        $actual = $post->toFeed();

        static::assertSame($expected, $actual, 'Post::toFeed should return correct result');
    }

    private function makePost(array $data): Post
    {
        $post = new Post();
        $post->setId($data['id']);
        $post->setText($data['text']);

        return $post;
    }

    private function addAuthor(Post $post, array $data): Post
    {
        $author = new User();
        $author->setLogin($data['author']);
        $post->setAuthor($author);

        return $post;
    }

    private function setCreatedAtWithDelay(Post $post, ?int $delay = null): Post
    {
        if ($delay !== null) {
            \sleep($delay);
            $post->setCreatedAt();
        }

        return $post;
    }
}