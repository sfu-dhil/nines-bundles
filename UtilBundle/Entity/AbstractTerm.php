<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * An abstract term has a computer friendly name, a human readable label,
 * and a description.
 */
#[ORM\Table]
#[ORM\Index(columns: ['label'], flags: ['fulltext'])]
#[ORM\Index(columns: ['description'], flags: ['fulltext'])]
#[ORM\Index(columns: ['label', 'description'], flags: ['fulltext'])]
#[ORM\UniqueConstraint(columns: ['name'])]
#[ORM\MappedSuperclass]
abstract class AbstractTerm extends AbstractEntity {
    #[ORM\Column(type: 'string', length: 191)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 200)]
    private ?string $label = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function __construct() {
        parent::__construct();
    }

    public function __toString() : string {
        return $this->label;
    }

    public function setName(string $name) : self {
        $this->name = $name;

        return $this;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function setLabel(string $label) : self {
        $this->label = $label;

        return $this;
    }

    public function getLabel() : ?string {
        return $this->label;
    }

    public function setDescription(string $description) : self {
        $this->description = $description;

        return $this;
    }

    public function getDescription() : ?string {
        return $this->description;
    }
}
