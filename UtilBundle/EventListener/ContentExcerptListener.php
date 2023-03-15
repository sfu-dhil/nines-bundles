<?php

declare(strict_types=1);

namespace Nines\UtilBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Nines\UtilBundle\Entity\ContentEntityInterface;
use Nines\UtilBundle\Services\Text;

class ContentExcerptListener {
    private ?Text $text = null;

    public function __construct(Text $text) {
        $this->text = $text;
    }

    private function generateExcerpt(ContentEntityInterface $entity) : void {
        if ($entity->getExcerpt()) {
            return;
        }
        $content = $entity->getContent();
        $plain = $this->text->plain($content);
        $entity->setExcerpt($this->text->trim($plain));
    }

    public function prePersist(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        if ( ! $entity instanceof ContentEntityInterface) {
            return;
        }
        $this->generateExcerpt($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args) : void {
        $entity = $args->getObject();
        if ( ! $entity instanceof ContentEntityInterface) {
            return;
        }
        $this->generateExcerpt($entity);
    }
}
