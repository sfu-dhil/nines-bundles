<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

/**
 * AbstractEntity adds id, created, and updated fields along with the
 * normal getters. And it sets up automatic callbacks to set the created
 * and updated DateTimes.
 */
#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractEntity implements AbstractEntityInterface, Stringable {
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    protected ?DateTimeInterface $created = null;

    #[ORM\Column(type: 'datetime_immutable')]
    protected ?DateTimeInterface $updated = null;

    public function __construct() {}

    public function getId() : ?int {
        return $this->id;
    }

    public function getCreated() : DateTimeInterface {
        if ( ! $this->created) {
            return new DateTimeImmutable();
        }

        return $this->created;
    }

    public function setCreated(DateTimeInterface $created) : self {
        $this->created = $created;

        return $this;
    }

    public function getUpdated() : DateTimeInterface {
        return $this->updated;
    }

    public function setUpdated(DateTimeInterface $updated) : self {
        $this->updated = $updated;

        return $this;
    }

    #[ORM\PrePersist]
    public function prePersist() : void {
        if ( ! $this->created) {
            $this->created = new DateTimeImmutable();
            $this->updated = new DateTimeImmutable();
        }
    }

    #[ORM\PreUpdate]
    public function preUpdate() : void {
        $this->updated = new DateTimeImmutable();
    }
}
