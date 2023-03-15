<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

trait StoredFileTrait {
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $license = null;

    private ?File $file = null;

    #[ORM\Column(type: 'string', length: 128, nullable: false)]
    private ?string $originalName = null;

    #[ORM\Column(type: 'string', length: 128, nullable: false)]
    private ?string $path = null;

    #[ORM\Column(type: 'string', length: 64, nullable: false)]
    private ?string $mimeType = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $fileSize = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $checksum = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $sourceUrl = null;

    public function __toString() : string {
        if ($this->file) {
            return $this->file->getFilename();
        }
        if ($this->originalName) {
            return $this->originalName;
        }
        if ($this->path) {
            return $this->path;
        }

        return '';
    }

    public function getDescription() : ?string {
        return $this->description;
    }

    /**
     * @param ?string $description
     */
    public function setDescription(?string $description) : self {
        $this->description = $description;

        return $this;
    }

    public function getLicense() : ?string {
        return $this->license;
    }

    /**
     * @param ?string $license
     */
    public function setLicense(?string $license) : self {
        $this->license = $license;

        return $this;
    }

    public function getFile() : ?File {
        return $this->file;
    }

    /**
     * @param ?File $file
     */
    public function setFile(?File $file) : self {
        $this->file = $file;

        return $this;
    }

    public function getExtension() : ?string {
        if ( ! $this->file) {
            return null;
        }

        return $this->file->getExtension();
    }

    public function getOriginalName() : ?string {
        return $this->originalName;
    }

    /**
     * @param ?string $originalName
     */
    public function setOriginalName(?string $originalName) : self {
        $this->originalName = $originalName;

        return $this;
    }

    public function getPath() : ?string {
        return $this->path;
    }

    /**
     * @param ?string $path
     */
    public function setPath(?string $path) : self {
        $this->path = $path;

        return $this;
    }

    public function getMimeType() : ?string {
        return $this->mimeType;
    }

    /**
     * @param ?string $mimeType
     */
    public function setMimeType(?string $mimeType) : self {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getFileSize() : ?int {
        return $this->fileSize;
    }

    /**
     * @param ?int $fileSize
     */
    public function setFileSize(?int $fileSize) : self {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getChecksum() : ?string {
        return $this->checksum;
    }

    /**
     * @param ?string $checksum
     */
    public function setChecksum(?string $checksum) : self {
        $this->checksum = $checksum;

        return $this;
    }

    public function getSourceUrl() : ?string {
        return $this->sourceUrl;
    }

    /**
     * @param ?string $sourceUrl
     */
    public function setSourceUrl(?string $sourceUrl) : self {
        $this->sourceUrl = $sourceUrl;

        return $this;
    }
}
