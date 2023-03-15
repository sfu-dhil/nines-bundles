<?php

declare(strict_types=1);

namespace Nines\SolrBundle\Hydrator;

use Doctrine\ORM\EntityManagerInterface;
use Solarium\Core\Query\DocumentInterface;
use stdClass;

/**
 * Map a search result document to an ORM entity.
 */
class DoctrineHydrator {
    private ?EntityManagerInterface $em = null;

    /**
     * Fetch an entity from the database from the ID stored in the solr
     * search result.
     *
     * @param DocumentInterface|stdClass $document
     */
    public function hydrate($document) : ?object {
        list($class, $id) = explode(':', $document->id);

        return $this->em->find($class, $id);
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }
}
