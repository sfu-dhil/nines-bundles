<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Entity;

use DateTimeInterface;

interface ContributorInterface extends AbstractEntityInterface {
    /**
     * @return null|array<string,mixed>
     */
    public function getContributions() : ?array;

    /**
     * @param array<string,mixed> $contributions
     */
    public function setContributions(array $contributions) : self;

    public function addContribution(DateTimeInterface $date, string $name) : self;
}
