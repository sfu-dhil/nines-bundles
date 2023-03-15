<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait AudioContainerTrait {
    /**
     * @var Collection<int,Audio>
     */
    protected ?Collection $audios = null;

    protected function __construct() {
        $this->audios = new ArrayCollection();
    }

    public function addAudio(Audio $audio) : self {
        if ( ! $this->audios->contains($audio)) {
            $this->audios[] = $audio;
        }

        return $this;
    }

    public function removeAudio(Audio $audio) : self {
        if ($this->audios->contains($audio)) {
            $this->audios->removeElement($audio);
        }

        return $this;
    }

    public function containsAudio(Audio $audio) : bool {
        return $this->audios->contains($audio);
    }

    public function setAudios(array|Collection $audios) : self {
        if (is_array($audios)) {
            $this->audios = new ArrayCollection($audios);
        } else {
            $this->audios = $audios;
        }

        return $this;
    }

    public function getAudios() : array {
        return $this->audios->toArray();
    }

    public function getAudioByChecksum(string $checksum) : ?Audio {
        foreach ($this->getAudios() as $audio) {
            if ($audio->getChecksum() === $checksum) {
                return $audio;
            }
        }

        return null;
    }

    public function getAudioBySourceUrl(string $sourceUrl) : ?Audio {
        foreach ($this->getAudios() as $audio) {
            if ($audio->getSourceUrl() === $sourceUrl) {
                return $audio;
            }
        }

        return null;
    }
}
