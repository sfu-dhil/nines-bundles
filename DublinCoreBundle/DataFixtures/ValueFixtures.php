<?php

declare(strict_types=1);

namespace Nines\DublinCoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Nines\DublinCoreBundle\Entity\Value;
use stdClass;

class ValueFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        for ($i = 1; $i <= 5; $i++) {
            $fixture = new Value();
            $fixture->setData('Data ' . $i);
            $fixture->setEntity(stdClass::class . ':' . $i);

            $fixture->setElement($this->getReference('element.' . $i));
            $manager->persist($fixture);
            $this->setReference('value.' . $i, $fixture);
        }
        $manager->flush();
    }

    /**
     * @return array<string>
     */
    public function getDependencies() : array {
        return [
            ElementFixtures::class,
        ];
    }
}
