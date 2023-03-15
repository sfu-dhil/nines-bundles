<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Nines\UtilBundle\Entity\AbstractEntityInterface;

interface AudioContainerInterface extends AbstractEntityInterface {
    public function addAudio(Audio $audio) : self;

    public function removeAudio(Audio $audio) : self;

    public function containsAudio(Audio $audio) : bool;

    public function setAudios(array|Collection $audios) : self;

    public function getAudios() : array;

    public function getAudioByChecksum(string $checksum) : ?Audio;

    public function getAudioBySourceUrl(string $sourceUrl) : ?Audio;
}
