<?php

declare(strict_types=1);

namespace Nines\DublinCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\DublinCoreBundle\Repository\ValueRepository;
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\UtilBundle\Entity\LinkedEntityInterface;
use Nines\UtilBundle\Entity\LinkedEntityTrait;

#[ORM\Table(name: 'nines_dc_value')]
#[ORM\Index(name: 'nines_dc_value_ft', columns: ['data'], flags: ['fulltext'])]
#[ORM\Index(name: 'nines_dc_value_entity', columns: ['entity'])]
#[ORM\Entity(repositoryClass: ValueRepository::class)]
class Value extends AbstractEntity implements LinkedEntityInterface {
    use LinkedEntityTrait;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $data = null;

    #[ORM\ManyToOne(targetEntity: 'Element', inversedBy: 'values')]
    private ?Element $element = null;

    public function __construct() {
        parent::__construct();
    }

    public function __toString() : string {
        if ($this->data) {
            return $this->data;
        }

        return '';
    }

    public function getData() : ?string {
        return $this->data;
    }

    /**
     * @param ?string $data
     */
    public function setData(?string $data) : self {
        $this->data = $data;

        return $this;
    }

    public function getElement() : ?Element {
        return $this->element;
    }

    /**
     * @param ?Element $element
     */
    public function setElement(?Element $element) : self {
        $this->element = $element;

        return $this;
    }
}
