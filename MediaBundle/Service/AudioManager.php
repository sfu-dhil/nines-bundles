<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Service;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Nines\MediaBundle\Entity\Audio;
use Nines\MediaBundle\Entity\AudioContainerInterface;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of FileUploader.
 */
class AudioManager extends AbstractFileManager implements EventSubscriber {
    private function uploadFile(Audio $audio) : void {
        $file = $audio->getFile();
        if ( ! $file instanceof UploadedFile) {
            return;
        }
        $audio->setOriginalName($file->getClientOriginalName());

        $filename = $this->upload($file);
        $path = $this->uploadDir . '/' . $filename;

        $audioFile = new File($path);
        $audio->setFileSize($audioFile->getSize());
        $audio->setChecksum(md5_file($path));
        $audio->setFile($audioFile);
        $audio->setPath($filename);
        $audio->setMimeType($audioFile->getMimeType());
    }

    public function getSubscribedEvents() : array {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
            Events::preRemove,
            Events::postRemove,
        ];
    }

    public function prePersist(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        if ($entity instanceof Audio) {
            $this->uploadFile($entity);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args) : void {
        $entity = $args->getObject();
        if ($entity instanceof Audio) {
            $this->uploadFile($entity);
        }
    }

    public function postLoad(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        $class = ClassUtils::getClass($entity);
        if ($entity instanceof Audio) {
            $filePath = $this->uploadDir . '/' . $entity->getPath();
            if (file_exists($filePath)) {
                $entity->setFile(new File($filePath));
            } else {
                $this->logger->error("Cannot find audio file {$filePath}.");
            }
        }
        if ($entity instanceof AudioContainerInterface) {
            $repo = $this->em->getRepository(Audio::class);

            $audios = $repo->findBy([
                'entity' => $class . ':' . $entity->getId(),
            ]);
            $entity->setAudios($audios);
        }
    }

    public function postRemove(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        if ($entity instanceof Audio && $entity->getFile()) {
            try {
                $this->remove($entity->getFile());
            } catch (IOExceptionInterface $ex) {
                $this->logger->error("An error occured removing {$ex->getPath()}: {$ex->getMessage()}");
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        if ($entity instanceof AudioContainerInterface) {
            foreach ($entity->getAudios() as $audio) {
                $this->em->remove($audio);
            }
        }
    }

    public function acceptsAudios(AbstractEntity $entity) : bool {
        return $entity instanceof AudioContainerInterface;
    }
}
