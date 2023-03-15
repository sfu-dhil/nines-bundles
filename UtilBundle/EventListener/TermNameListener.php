<?php

declare(strict_types=1);

namespace Nines\UtilBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Nines\UtilBundle\Entity\AbstractTerm;
use Nines\UtilBundle\Services\Text;

class TermNameListener {
    private ?Text $text = null;

    public function __construct(Text $text) {
        $this->text = $text;
    }

    /**
     * Generate a slug for an Abstract Term, based on the entity's label.
     */
    private function generateSlug(AbstractTerm $entity) : void {
        if ($entity->getName()) {
            return;
        }
        $label = $entity->getLabel();
        $slug = $this->text->slug($label);
        $entity->setName($slug);
    }

    /**
     * Automatically generate a slug for an AbstractTerm.
     */
    public function prePersist(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        if ( ! $entity instanceof AbstractTerm) {
            return;
        }
        $this->generateSlug($entity);
    }

    /**
     * Automatically update a slug for an AbstractTerm.
     */
    public function preUpdate(PreUpdateEventArgs $args) : void {
        $entity = $args->getObject();
        if ( ! $entity instanceof AbstractTerm) {
            return;
        }
        $this->generateSlug($entity);
    }
}
