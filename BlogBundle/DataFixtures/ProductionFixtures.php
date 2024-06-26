<?php

declare(strict_types=1);

namespace Nines\BlogBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Nines\BlogBundle\Entity\PostCategory;
use Nines\BlogBundle\Entity\PostStatus;

class ProductionFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return [
            'prod',
        ];
    }

    public function load(ObjectManager $manager) : void {
        $draft = new PostStatus();
        $draft->setLabel('Draft');
        $draft->setDescription('Posts not ready for publication');
        $manager->persist($draft);

        $published = new PostStatus();
        $published->setLabel('Published');
        $published->setDescription('Posts ready for publication');
        $manager->persist($published);

        $category = new PostCategory();
        $category->setLabel('Post');
        $category->setDescription('Catch-all for posts');
        $manager->persist($category);

        $manager->flush();
    }
}
