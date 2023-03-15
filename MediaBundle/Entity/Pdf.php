<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\MediaBundle\Repository\PdfRepository;
use Nines\UtilBundle\Entity\LinkedEntityInterface;
use Nines\UtilBundle\Entity\LinkedEntityTrait;

#[ORM\Table(name: 'nines_media_pdf')]
#[ORM\Index(name: 'nines_media_pdf_ft', columns: ['original_name', 'description'], flags: ['fulltext'])]
#[ORM\Index(columns: ['entity'])]
#[ORM\Index(columns: ['checksum'])]
#[ORM\Index(columns: ['source_url'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: PdfRepository::class)]
class Pdf extends AbstractPdf implements LinkedEntityInterface, StoredFileInterface {
    use LinkedEntityTrait;
}
