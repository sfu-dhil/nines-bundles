<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait ContributorTrait {
    /**
     * @var array<int,array<string,string>>
     */
    #[ORM\Column(type: 'array', nullable: false)]
    private ?array $contributions = null;

    public function __construct() {
        $this->contributions = [];
    }

    /**
     * @return null|array<string,string>
     */
    public function getContributions() : ?array {
        if ( ! $this->contributions) {
            return [];
        }
        usort($this->contributions, function ($a, $b) {
            $d = $b['date'] <=> $a['date'];
            if ($d) {
                return $d;
            }

            return $a['name'] <=> $b['name'];
        });

        return $this->contributions;
    }

    /**
     * @param array<string,string> $contributions
     */
    public function setContributions(array $contributions) : self {
        $this->contributions = $contributions;

        return $this;
    }

    public function addContribution(DateTimeInterface $date, string $name) : self {
        if ( ! $this->contributions) {
            $this->contributions = [];
        }
        $str = $date->format('Y-m-d');

        foreach ($this->contributions as $contribution) {
            if ($contribution['date'] === $str && $contribution['name'] === $name) {
                return $this;
            }
        }
        $this->contributions[] = ['date' => $str, 'name' => $name];

        return $this;
    }
}
