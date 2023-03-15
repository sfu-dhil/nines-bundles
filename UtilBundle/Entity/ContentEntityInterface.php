<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Entity;

interface ContentEntityInterface extends AbstractEntityInterface {
    /**
     * Set the excerpt for an entity.
     */
    public function setExcerpt(string $excerpt) : self;

    /**
     * Get the excerpt for an entity.
     */
    public function getExcerpt() : string;

    /**
     * Set the content of an entity.
     */
    public function setContent(string $content) : self;

    /**
     * Get the content from an entity.
     */
    public function getContent() : string;
}
