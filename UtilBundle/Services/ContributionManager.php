<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Services;

use DateTimeImmutable;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Nines\UserBundle\Entity\User;
use Nines\UtilBundle\Entity\ContributorInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Contribution service for Symfony.
 */
class ContributionManager implements EventSubscriber {
    private ?Security $security = null;

    /**
     * @param mixed $entity
     */
    public function addContributor($entity) : void {
        if ( ! $entity instanceof ContributorInterface) {
            return;
        }

        /** @var ?User $user */
        $user = $this->security->getUser();
        if ( ! $user) {
            return;
        }
        $entity->addContribution(new DateTimeImmutable(), $user->getFullname());
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setSecurity(Security $security) : void {
        $this->security = $security;
    }

    public function getSubscribedEvents() : array {
        return [
            Events::preUpdate,
            Events::prePersist,
        ];
    }

    public function preUpdate(LifecycleEventArgs $args) : void {
        $this->addContributor($args->getEntity());
    }

    public function prePersist(LifecycleEventArgs $args) : void {
        $this->addContributor($args->getEntity());
    }
}
