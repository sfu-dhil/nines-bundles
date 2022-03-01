<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace Nines\UtilBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait ContributorTrait {
    /**
     * @ORM\Column(type="array", nullable=false)
     *
     * @var array<int,array<string,string>>
     */
    private array $contributions;

    public function __construct() {
        $this->contributions = [];
    }

    /**
     * @return null|array<string,mixed>
     */
    public function getContributions() : ?array {
        if ( ! $this->contributions) {
            return [];
        }
        usort($this->contributions, function($a, $b) {
            $d = $b['date'] <=> $a['date'];
            if ($d) {
                return $d;
            }

            return $a['name'] <=> $b['name'];
        });

        return $this->contributions;
    }

    /**
     * @param array<string,mixed> $contributions
     */
    public function setContributions(array $contributions) : self {
        $this->contributions = $contributions;

        return $this;
    }

    public function addContribution(DateTimeImmutable $date, string $name) : self {
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