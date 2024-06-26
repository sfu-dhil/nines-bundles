<?php

declare(strict_types=1);

namespace Nines\DublinCoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Nines\DublinCoreBundle\Entity\Element;

class ElementFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        for ($i = 1; $i <= 5; $i++) {
            $fixture = new Element();
            $fixture->setLabel('Label ' . $i);
            $fixture->setDescription("<p>This is paragraph {$i}</p>");
            $fixture->setUri('https://example.com/' . $i);
            $fixture->setComment("<p>This is paragraph {$i}</p>");
            $manager->persist($fixture);
            $this->setReference('element.' . $i, $fixture);
        }
        $manager->flush();
    }
}
