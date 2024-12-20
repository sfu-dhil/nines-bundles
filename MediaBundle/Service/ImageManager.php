<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Service;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Nines\MediaBundle\Entity\Image;
use Nines\MediaBundle\Entity\ImageContainerInterface;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image Manager service that handles file uploads, thumbnailing, and database
 * stuff.
 */
class ImageManager extends AbstractFileManager implements EventSubscriber {
    private ?Thumbnailer $thumbnailer = null;

    /**
     * Store the image file, extracta  little metadata, and generate a thumbnail.
     */
    protected function uploadFile(Image $image) : void {
        $file = $image->getFile();
        if ( ! $file instanceof UploadedFile) {
            return;
        }
        $image->setOriginalName($file->getClientOriginalName());

        $filename = $this->upload($file);
        $path = $this->uploadDir . '/' . $filename;

        $imageFile = new File($path);
        $image->setPath($filename);
        $image->setFile($imageFile);
        $image->setFileSize($imageFile->getSize());
        $image->setChecksum(md5_file($path));
        $image->setMimeType($imageFile->getMimeType());
        $dimensions = getimagesize($path);
        $image->setImageWidth($dimensions[0]);
        $image->setImageHeight($dimensions[1]);
        $thumbPath = $this->thumbnailer->thumbnail($image);
        $image->setThumbPath($thumbPath);
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
        if ($entity instanceof Image) {
            $this->uploadFile($entity);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args) : void {
        $entity = $args->getObject();
        if ($entity instanceof Image) {
            $this->uploadFile($entity);
        }
    }

    /**
     * Event subscriber action. After loading an image entity from the database,
     * add the file object to the entity.
     */
    public function postLoad(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        $class = ClassUtils::getClass($entity);
        if ($entity instanceof Image) {
            $filePath = $this->uploadDir . '/' . $entity->getPath();
            if (file_exists($filePath)) {
                $entity->setFile(new File($filePath));
            } else {
                $this->logger->error('Cannot find image file ' . $this->uploadDir . '/' . $entity->getPath());
            }
            $thumbPath = $this->uploadDir . '/' . $entity->getThumbPath();
            if (file_exists($thumbPath)) {
                $entity->setThumbFile(new File($thumbPath));
            } else {
                $this->logger->error('Cannot find thumbnail file ' . $this->uploadDir . '/' . $entity->getPath());
            }
        }
        if ($entity instanceof ImageContainerInterface) {
            $repo = $this->em->getRepository(Image::class);

            $images = $repo->findBy([
                'entity' => $class . ':' . $entity->getId(),
            ]);
            $entity->setImages($images);
        }
    }

    /**
     * Event subscriber action. After removing an image entity from the database
     * remove the image and thumbnail files.'.
     */
    public function postRemove(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        if ($entity instanceof Image && $entity->getFile()) {
            try {
                $this->remove($entity->getFile());
                $this->remove($entity->getThumbFile());
            } catch (IOExceptionInterface $ex) {
                $this->logger->error("An error occurred removing {$ex->getPath()}: {$ex->getMessage()}");
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        if ($entity instanceof ImageContainerInterface) {
            foreach ($entity->getImages() as $image) {
                $this->em->remove($image);
            }
        }
    }

    public function acceptsImages(AbstractEntity $entity) : bool {
        return $entity instanceof ImageContainerInterface;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setThumbnailer(Thumbnailer $thumbnailer) : void {
        $this->thumbnailer = $thumbnailer;
    }
}
