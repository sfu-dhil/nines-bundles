<?php

declare(strict_types=1);

namespace Nines\FeedbackBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Nines\FeedbackBundle\Entity\CommentNote;
use Nines\UserBundle\DataFixtures\UserFixtures;

class CommentNoteFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        for ($i = 1; $i <= 5; $i++) {
            $fixture = new CommentNote();
            $fixture->setContent("<p>This is paragraph {$i}</p>");
            $fixture->setUser($this->getReference('user.user'));
            $fixture->setComment($this->getReference('comment.' . $i));
            $manager->persist($fixture);
            $this->setReference('commentnote.' . $i, $fixture);
        }
        $manager->flush();
    }

    /**
     * @return array<string>
     */
    public function getDependencies() : array {
        return [
            UserFixtures::class,
            CommentFixtures::class,
        ];
    }
}
