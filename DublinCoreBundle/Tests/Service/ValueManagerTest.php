<?php

declare(strict_types=1);

namespace Nines\DublinCoreBundle\Tests\Service;

use Doctrine\Common\Collections\Collection;
use Exception;
use Nines\DublinCoreBundle\Entity\Value;
use Nines\DublinCoreBundle\Entity\ValueInterface;
use Nines\DublinCoreBundle\Entity\ValueTrait;
use Nines\DublinCoreBundle\Service\ValueManager;
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\UtilBundle\TestCase\ServiceTestCase;

class ValueManagerTest extends ServiceTestCase {
    private ?ValueManager $manager = null;

    private ?ValueInterface $entity = null;

    public function testSetUp() : void {
        $this->assertInstanceOf(ValueManager::class, $this->manager);
    }

    public function testSetValuesWrongClass() : void {
        $this->expectException(Exception::class);
        $entity = new class() extends AbstractEntity {
            public function __toString() : string {
                return '';
            }
        };
        $this->manager->setValues($entity, []);
    }

    protected function setUp() : void {
        parent::setUp();
        $this->manager = static::getContainer()->get(ValueManager::class);
        $this->entity = new class() implements ValueInterface {
            use ValueTrait;

            /**
             * @param null|array<Value>|Collection<int,Value> $values
             *
             * @return null|Collection<int,Value>|Value[]
             */
            public function rawData($values = null) {
                if ($values) {
                    $this->values = $values;
                }

                return $this->values;
            }
        };
    }
}
