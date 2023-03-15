<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Nines\UtilBundle\Entity\AbstractEntityInterface;

interface ImageContainerInterface extends AbstractEntityInterface {
    public function addImage(Image $image) : self;

    public function removeImage(Image $image) : self;

    public function containsImage(Image $image) : bool;

    public function setImages(array|Collection $images) : self;

    public function getImages() : array;

    public function getImageByChecksum(string $checksum) : ?Image;

    public function getImageBySourceUrl(string $sourceUrl) : ?Image;
}
