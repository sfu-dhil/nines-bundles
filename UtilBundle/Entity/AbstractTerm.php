<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\SolrBundle\Annotation as Solr;

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
    /**
     * Name of the term.
     *
     * @Solr\Field(type="string")
     */
    #[ORM\Column(type: 'string', length: 191)]
    private ?string $name = null;

    /**
     * Human readable term label.
     *
     * @Solr\Field(type="string")
     */
    #[ORM\Column(type: 'string', length: 200)]
    private ?string $label = null;

    /**
     * Description of the term.
     *
     * @Solr\Field(type="text")
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the status label.
     */
    public function __toString() : string {
        return $this->label;
    }

    /**
     * Set name.
     *
     * @return $this
     */
    public function setName(string $name) : self {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() : ?string {
        return $this->name;
    }

    /**
     * Set label.
     *
     * @return $this
     */
    public function setLabel(string $label) : self {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel() : ?string {
        return $this->label;
    }

    /**
     * Set description.
     *
     * @return $this
     */
    public function setDescription(string $description) : self {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription() : ?string {
        return $this->description;
    }
}
