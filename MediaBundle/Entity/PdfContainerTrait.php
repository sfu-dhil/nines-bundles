<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait PdfContainerTrait {
    /**
     * @var Collection<int,Pdf>
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

    public function setPdfs(array|Collection $pdfs) : self {
        if (is_array($pdfs)) {
            $this->pdfs = new ArrayCollection($pdfs);
        } else {
            $this->pdfs = $pdfs;
        }

        return $this;
    }

    public function getPdfs() : array {
        return $this->pdfs->toArray();
    }

    public function getPdfByChecksum(string $checksum) : ?Pdf {
        foreach ($this->getPdfs() as $pdf) {
            if ($pdf->getChecksum() === $checksum) {
                return $pdf;
            }
        }

        return null;
    }

    public function getPdfBySourceUrl(string $sourceUrl) : ?Pdf {
        foreach ($this->getPdfs() as $pdf) {
            if ($pdf->getSourceUrl() === $sourceUrl) {
                return $pdf;
            }
        }

        return null;
    }
}
