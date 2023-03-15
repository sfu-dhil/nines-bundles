<?php

declare(strict_types=1);

namespace Nines\BlogBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Nines\BlogBundle\Entity\PostStatus;
use Nines\UtilBundle\Repository\TermRepository;

/**
 * @method null|PostStatus find($id, $lockMode = null, $lockVersion = null)
 * @method PostStatus[] findAll()
 * @method PostStatus[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|PostStatus findOneBy(array $criteria, array $orderBy = null)
 */
class PostStatusRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, PostStatus::class);
    }
}
