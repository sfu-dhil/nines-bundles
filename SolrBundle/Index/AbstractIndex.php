<?php

declare(strict_types=1);

namespace Nines\SolrBundle\Index;

use Nines\SolrBundle\Exception\NotConfiguredException;
use Nines\SolrBundle\Query\QueryBuilder;
use Nines\SolrBundle\Services\SolrManager;

/**
 * Generic parent class for index classes. They're like Doctrine repositories,
 * but meant for the solr search index.
 */
abstract class AbstractIndex {
    protected ?SolrManager $manager = null;

    /**
     * Build and return a query builder.
     *
     * @throws NotConfiguredException
     */
    protected function createQueryBuilder() : QueryBuilder {
        return $this->manager->createQueryBuilder();
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setSolrManager(SolrManager $manager) : void {
        $this->manager = $manager;
    }
}
