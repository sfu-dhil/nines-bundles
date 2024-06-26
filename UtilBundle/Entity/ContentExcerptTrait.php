<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ContentExcerptTrait {
    #[ORM\Column(type: 'text')]
    protected ?string $excerpt = null;

    #[ORM\Column(type: 'text')]
    protected ?string $content = null;

    /**
     * Set excerpt.
     */
    public function setExcerpt(string $excerpt) : self {
        $this->excerpt = $excerpt;

        return $this;
    }

    /**
     * Get excerpt.
     */
    public function getExcerpt() : string {
        if ( ! $this->excerpt) {
            return '';
        }

        return $this->excerpt;
    }

    /**
     * Set content.
     */
    public function setContent(string $content) : self {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     */
    public function getContent() : string {
        if ( ! $this->content) {
            return '';
        }

        return $this->content;
    }
}
