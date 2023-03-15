<?php

declare(strict_types=1);

namespace Nines\DublinCoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Exception;

interface ValueInterface {
    /**
     * @param ?string $name
     *
     * @return Collection<int,Value>|Value[]
     */
    public function getValues(?string $name = null);

    /**
     * @param null|array<Value>|Collection<int,Value> $values
     *
     * @throws Exception
     */
    public function setValues($values = []) : self;

    /**
     * @throws Exception
     */
    public function addValue(Value $value) : self;

    public function removeValue(Value $value) : self;
}
