<?php

declare(strict_types=1);

namespace Nines\BlogBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Nines\BlogBundle\Entity\Post;
use Nines\UserBundle\DataFixtures\UserFixtures;

class PostFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        for ($i = 1; $i <= 5; $i++) {
            $fixture = new Post();
            $fixture->setIncludeComments(0 === $i % 2);
            $fixture->setTitle('Title ' . $i);
            $fixture->setExcerpt("<p>This is paragraph {$i}</p>");
            $fixture->setContent("<p>This is paragraph {$i}</p>");
            $fixture->setCategory($this->getReference('postcategory.' . $i));
            $fixture->setStatus($this->getReference('poststatus.' . $i));
            $fixture->setUser($this->getReference('user.inactive'));
            $manager->persist($fixture);
            $this->setReference('post.' . $i, $fixture);
        }
        $manager->flush();
    }

    /**
     * @return array<string>
     */
    public function getDependencies() : array {
        return [
            PostCategoryFixtures::class,
            PostStatusFixtures::class,
            UserFixtures::class,
        ];
    }
}
