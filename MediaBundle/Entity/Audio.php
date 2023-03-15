<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\MediaBundle\Repository\AudioRepository;
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\UtilBundle\Entity\LinkedEntityInterface;
use Nines\UtilBundle\Entity\LinkedEntityTrait;

#[ORM\Table(name: 'nines_media_audio')]
#[ORM\Index(name: 'nines_media_audio_ft', columns: ['original_name', 'description'], flags: ['fulltext'])]
#[ORM\Index(columns: ['entity'])]
#[ORM\Index(columns: ['checksum'])]
#[ORM\Index(columns: ['source_url'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: AudioRepository::class)]
class Audio extends AbstractEntity implements LinkedEntityInterface, StoredFileInterface {
    use LinkedEntityTrait;
    use StoredFileTrait;

    public function __construct() {
        parent::__construct();
    }
}
