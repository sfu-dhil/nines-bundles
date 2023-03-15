<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Nines\UtilBundle\Entity\AbstractEntityInterface;

interface PdfContainerInterface extends AbstractEntityInterface {
    public function addPdf(Pdf $pdf) : self;

    public function removePdf(Pdf $pdf) : self;

    public function setPdfs(array|Collection $pdfs) : self;

    public function containsPdf(Pdf $pdf) : bool;

    public function getPdfs() : array;

    public function getPdfByChecksum(string $checksum) : ?Pdf;

    public function getPdfBySourceUrl(string $sourceUrl) : ?Pdf;
}
