<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Nines\UtilBundle\Entity\AbstractEntityInterface;

interface LinkableInterface extends AbstractEntityInterface {
    /**
     * @return array<Link>
     */
    public function getLinks() : array;

    /**
     * @param array<Link>|Collection<int,Link> $links
     */
    public function setLinks($links) : self;

    public function addLink(Link $link) : self;

    public function removeLink(Link $link) : self;
}
