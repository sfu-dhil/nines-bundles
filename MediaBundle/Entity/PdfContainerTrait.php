<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace Nines\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait PdfContainerTrait {
    /**
     * @var Collection<int,Pdf>|Pdf[]
     */
    protected $pdfs;

    protected function __construct() {
        $this->pdfs = new ArrayCollection();
    }

    public function addPdf(Pdf $pdf) : self {
        if ( ! $this->pdfs->contains($pdf)) {
            $this->pdfs[] = $pdf;
        }

        return $this;
    }

    public function removePdf(Pdf $pdf) : self {
        if ($this->pdfs->contains($pdf)) {
            $this->pdfs->removeElement($pdf);
        }

        return $this;
    }

    public function containsPdf(Pdf $pdf) : bool {
        return $this->pdfs->contains($pdf);
    }

    /**
     * @param array<Pdf>|Collection<int,Pdf> $pdfs
     */
    public function setPdfs($pdfs) : self {
        if (is_array($pdfs)) {
            $this->pdfs = new ArrayCollection($pdfs);
        } else {
            $this->pdfs = $pdfs;
        }

        return $this;
    }

    /**
     * @return array<Pdf>
     */
    public function getPdfs() : array {
        return $this->pdfs->toArray();
    }

    /**
     * @param string $checksum
     * @return bool
     */
    public function hasPdfByChecksum(string $checksum) : bool {
        foreach ($this->pdfs as $pdf) {
            if ($pdf->getChecksum() === $checksum) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $sourceUrl
     * @return bool
     */
    public function hasPdfBySourceUrl(string $sourceUrl) : bool {
        foreach ($this->pdfs as $pdf) {
            if ($pdf->getSourceUrl() === $sourceUrl) {
                return true;
            }
        }

        return false;
    }
}
