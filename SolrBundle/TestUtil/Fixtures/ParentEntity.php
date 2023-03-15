<?php

declare(strict_types=1);

namespace Nines\SolrBundle\TestUtil\Fixtures;

use Doctrine\ORM\Mapping as ORM;
use Nines\SolrBundle\Annotation as Solr;

#[ORM\Entity]
class ParentEntity {
    /**
     * @Solr\Id
     */
    #[ORM\Id]
    private ?int $id = null;

    /**
     * @Solr\Field(type="string")
     */
    private ?string $something = null;

    public function getId() : ?int {
        return $this->id;
    }

    public function setId(int $id) : self {
        $this->id = $id;

        return $this;
    }

    public function getSomething() : ?string {
        return $this->something;
    }

    public function setSomething(string $something) : self {
        $this->something = $something;

        return $this;
    }
}
