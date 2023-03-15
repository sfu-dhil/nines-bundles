<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait ImageContainerTrait {
    /**
     * @var Collection<int,Image>
     */
    protected $images;

    protected function __construct() {
        $this->images = new ArrayCollection();
    }

    public function addImage(Image $image) : self {
        if ( ! $this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(Image $image) : self {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }

        return $this;
    }

    public function containsImage(Image $image) : bool {
        return $this->images->contains($image);
    }

    public function setImages(array|Collection $images) : self {
        if (is_array($images)) {
            $this->images = new ArrayCollection($images);
        } else {
            $this->images = $images;
        }

        return $this;
    }

    public function getImages() : array {
        return $this->images->toArray();
    }

    public function getImageByChecksum(string $checksum) : ?Image {
        foreach ($this->getImages() as $image) {
            if ($image->getChecksum() === $checksum) {
                return $image;
            }
        }

        return null;
    }

    public function getImageBySourceUrl(string $sourceUrl) : ?Image {
        foreach ($this->getImages() as $image) {
            if ($image->getSourceUrl() === $sourceUrl) {
                return $image;
            }
        }

        return null;
    }
}
