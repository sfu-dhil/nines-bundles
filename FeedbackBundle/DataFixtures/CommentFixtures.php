<?php

declare(strict_types=1);

namespace Nines\FeedbackBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Nines\FeedbackBundle\Entity\Comment;
use stdClass;

class CommentFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        for ($i = 1; $i <= 5; $i++) {
            $fixture = new Comment();
            $fixture->setFullname('Fullname ' . $i);
            $fixture->setEmail('email_' . $i . '@example.com');
            $fixture->setFollowUp(0 === $i % 2);
            $fixture->setEntity(stdClass::class . ':' . $i);
            $fixture->setContent("This is paragraph {$i}");
            $fixture->setStatus($this->getReference('commentstatus.' . $i));
            $manager->persist($fixture);
            $this->setReference('comment.' . $i, $fixture);
        }
        $manager->flush();
    }

    /**
     * @return array<string>
     */
    public function getDependencies() : array {
        return [
            CommentStatusFixtures::class,
        ];
    }
}
