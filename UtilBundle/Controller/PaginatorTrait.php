<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;

/**
 * Convienence trait for the use of paginators.
 */
trait PaginatorTrait {
    protected ?PaginatorInterface $paginator = null;

    /**
     * Set the paginator service.
     */
    public function setPaginator(PaginatorInterface $paginator) : self {
        $this->paginator = $paginator;

        return $this;
    }
}
