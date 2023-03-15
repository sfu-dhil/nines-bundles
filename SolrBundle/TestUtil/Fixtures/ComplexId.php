<?php

declare(strict_types=1);

namespace Nines\SolrBundle\TestUtil\Fixtures;

use Doctrine\ORM\Mapping as ORM;
use Nines\SolrBundle\Annotation as Solr;

/**
 * @Solr\Document
 */
#[ORM\Entity]
class ComplexId {
    /**
     * @Solr\Id(name="idname", getter="idGetter('abc', 1, true)")
     */
    #[ORM\Id]
    private ?int $id = 7;

    public function __construct() {
    }

    public function getId() : int {
        return $this->id;
    }

    public function idGetter() : int {
        return $this->id;
    }
}
