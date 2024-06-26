<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Tests\Services;

use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\UtilBundle\Entity\ContributorInterface;
use Nines\UtilBundle\Entity\ContributorTrait;
use Nines\UtilBundle\Services\ContributionManager;
use Nines\UtilBundle\TestCase\ControllerTestCase;

class ContributionManagerTest extends ControllerTestCase {
    private ?object $entity = null;

    public function testAddContributor() : void {
        $this->login(UserFixtures::ADMIN);
        $manager = static::getContainer()->get(ContributionManager::class);
        $manager->addContributor($this->entity);
        $this->assertSame([['date' => date('Y-m-d'), 'name' => 'Admin user']], $this->entity->rawData());
    }

    protected function setUp() : void {
        parent::setUp();
        $this->entity = new class() extends AbstractEntity implements ContributorInterface {
            use ContributorTrait;

            public function __toString() {
                return 'string';
            }

            /**
             * @return array<int,array<string,string>>
             */
            public function rawData() : array {
                return $this->contributions;
            }
        };
    }
}
