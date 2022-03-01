<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace Nines\DublinCoreBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Nines\DublinCoreBundle\Entity\Element;
use Nines\DublinCoreBundle\Entity\Value;
use Nines\DublinCoreBundle\Entity\ValueInterface;
use Nines\DublinCoreBundle\Entity\ValueTrait;
use Nines\UtilBundle\TestCase\ServiceTestCase;

class ValueTraitTest extends ServiceTestCase {
    private $entity;

    public function testGetValues() : void {
        $elements = $this->em->getRepository(Element::class)->findAll();
        $values = new ArrayCollection();
        foreach ($elements as $element) {
            $v = new Value();
            $v->setElement($element);
            $v->setData($element->getLabel());
            $values->add($v);
        }
        $this->entity->rawData($values);
        $this->assertCount(5, $this->entity->getValues());
        $this->assertCount(1, $this->entity->getValues('label-1'));
    }

    public function testSetValues() : void {
        $this->entity->setValues();
        $this->assertCount(0, $this->entity->rawData());
    }

    protected function setUp() : void {
        parent::setUp();
        $this->entity = new class() implements ValueInterface {
            use ValueTrait;

            public function rawData($values = null) {
                if ($values) {
                    $this->values = $values;
                }

                return $this->values;
            }
        };
    }
}