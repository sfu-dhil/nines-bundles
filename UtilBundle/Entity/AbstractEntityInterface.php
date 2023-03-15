<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Entity;

use DateTimeInterface;

interface AbstractEntityInterface {
    public function getId() : ?int;

    public function getCreated() : DateTimeInterface;

    public function getUpdated() : DateTimeInterface;

    public function prePersist() : void;

    public function preUpdate() : void;
}
