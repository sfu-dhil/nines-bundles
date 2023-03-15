<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Entity;

interface LinkedEntityInterface {
    public function getEntity() : ?string;

    public function setEntity(string $entity) : self;
}
