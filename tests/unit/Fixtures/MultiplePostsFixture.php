<?php

namespace UnitTests\Fixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MultiplePostsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $pratchett */
        $pratchett = $this->getReference(MultipleUsersFixture::PRATCHETT);
        /** @var User $tolkien */
        $tolkien = $this->getReference(MultipleUsersFixture::TOLKIEN);
        /** @var User $carroll */
        $carroll = $this->getReference(MultipleUsersFixture::CARROLL);
        $this->makePost($manager, $tolkien, 'Hobbit');
        $this->makePost($manager, $pratchett, 'Colours of Magic');
        $this->makePost($manager, $tolkien, 'Lords of the Rings');
        $this->makePost($manager, $pratchett, 'Soul Music');
        $this->makePost($manager, $carroll, 'Alice in Wonderland');
        $this->makePost($manager, $pratchett, 'Through the Looking-Glass');
        $manager->flush();
    }

    private function makePost(ObjectManager $manager, User $author, string $text): void
    {
        $post = new Post();
        $post->setAuthor($author);
        $post->setText($text);
        $manager->persist($post);
        sleep(1);
    }
}