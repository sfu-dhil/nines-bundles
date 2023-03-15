<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\MediaBundle\Repository\PdfRepository;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Table]
#[ORM\Index(columns: ['original_name', 'description'], flags: ['fulltext'])]
#[ORM\MappedSuperclass(repositoryClass: PdfRepository::class)]
abstract class AbstractPdf extends AbstractEntity implements StoredFileInterface {
    use StoredFileTrait;

    protected ?File $thumbFile = null;

    #[ORM\Column(type: 'string', length: 128, nullable: false)]
    protected ?string $thumbPath = null;

    public function getThumbFile() : ?File {
        return $this->thumbFile;
    }

    public function setThumbFile(File $thumbFile) : self {
        $this->thumbFile = $thumbFile;

        return $this;
    }

    public function getThumbPath() : string {
        return $this->thumbPath;
    }

    public function setThumbPath(string $thumbPath) : self {
        $this->thumbPath = $thumbPath;

        return $this;
    }
}
