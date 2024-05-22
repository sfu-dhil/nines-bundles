<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\MediaBundle\Repository\ImageRepository;
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\UtilBundle\Entity\LinkedEntityInterface;
use Nines\UtilBundle\Entity\LinkedEntityTrait;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Table(name: 'nines_media_image')]
#[ORM\Index(name: 'nines_media_image_ft', columns: ['original_name', 'description'], flags: ['fulltext'])]
#[ORM\Index(columns: ['entity'])]
#[ORM\Index(columns: ['checksum'])]
#[ORM\Index(columns: ['source_url'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image extends AbstractEntity implements LinkedEntityInterface, StoredFileInterface {
    use LinkedEntityTrait;
    use StoredFileTrait;

    protected ?File $thumbFile = null;

    #[ORM\Column(type: 'text', nullable: false)]
    protected ?string $thumbPath = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    protected ?int $imageWidth = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    protected ?int $imageHeight = null;

    public function __construct() {
        parent::__construct();
    }

    public function setThumbFile(File $file) : self {
        $this->thumbFile = $file;

        return $this;
    }

    public function getThumbFile() : ?File {
        return $this->thumbFile;
    }

    public function getThumbPath() : string {
        return $this->thumbPath;
    }

    public function setThumbPath(string $thumbPath) : self {
        $this->thumbPath = $thumbPath;

        return $this;
    }

    public function getImageWidth() : int {
        return $this->imageWidth;
    }

    public function setImageWidth(int $imageWidth) : self {
        $this->imageWidth = $imageWidth;

        return $this;
    }

    public function getImageHeight() : int {
        return $this->imageHeight;
    }

    public function setImageHeight(int $imageHeight) : self {
        $this->imageHeight = $imageHeight;

        return $this;
    }
}
