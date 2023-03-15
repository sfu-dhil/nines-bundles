<?php

declare(strict_types=1);

namespace Nines\FeedbackBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Nines\FeedbackBundle\Entity\CommentStatus;

/**
 * @method null|CommentStatus find($id, $lockMode = null, $lockVersion = null)
 * @method CommentStatus[] findAll()
 * @method CommentStatus[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|CommentStatus findOneBy(array $criteria, array $orderBy = null)
 *
 * @phpstan-extends ServiceEntityRepository<\Nines\FeedbackBundle\Entity\CommentStatus>
 */
class CommentStatusRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, CommentStatus::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('commentStatus')
            ->orderBy('commentStatus.id')
            ->getQuery()
        ;
    }

    public function typeaheadQuery(string $q) : Query {
        $qb = $this->createQueryBuilder('commentStatus');
        $qb->andWhere('commentStatus.label LIKE :q');
        $qb->orderBy('commentStatus.label', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery();
    }

    public function searchQuery(string $q) : Query {
        $qb = $this->createQueryBuilder('commentStatus');
        $qb->addSelect('MATCH (commentStatus.label, commentStatus.description) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }
}
