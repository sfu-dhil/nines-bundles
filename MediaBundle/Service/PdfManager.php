<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Service;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;
use Nines\MediaBundle\Entity\Pdf;
use Nines\MediaBundle\Entity\PdfContainerInterface;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Pdf Manager service that handles file uploads, thumbnailing, and database
 * stuff.
 */
class PdfManager extends AbstractFileManager implements EventSubscriber {
    private ?Thumbnailer $thumbnailer = null;

    /**
     * Store the pdf file, extracta  little metadata, and generate a thumbnail.
     *
     * @throws Exception
     */
    protected function uploadFile(Pdf $pdf) : void {
        $file = $pdf->getFile();
        if ( ! $file instanceof UploadedFile) {
            return;
        }
        $pdf->setOriginalName($file->getClientOriginalName());

        $filename = $this->upload($file);
        $path = $this->uploadDir . '/' . $filename;

        $pdfFile = new File($path);
        $pdf->setPath($filename);
        $pdf->setFile($pdfFile);
        $pdf->setFileSize($pdfFile->getSize());
        $pdf->setChecksum(md5_file($path));
        $pdf->setMimeType($pdfFile->getMimeType());
        $thumbPath = $this->thumbnailer->thumbnail($pdf);
        $pdf->setThumbPath($thumbPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents() : array {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
            Events::preRemove,
            Events::postRemove,
        ];
    }

    /**
     * Event subscriber action, called before saving an pdf to the database.
     *
     * @throws Exception
     */
    public function prePersist(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        if ($entity instanceof Pdf) {
            $this->uploadFile($entity);
        }
    }

    /**
     * Event subscriber action, called before updating an pdf in the database.
     *
     * @throws Exception
     */
    public function preUpdate(PreUpdateEventArgs $args) : void {
        $entity = $args->getObject();
        if ($entity instanceof Pdf) {
            $this->uploadFile($entity);
        }
    }

    /**
     * Event subscriber action. After loading an pdf entity from the database,
     * add the file object to the entity.
     */
    public function postLoad(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        $class = ClassUtils::getClass($entity);
        if ($entity instanceof Pdf) {
            $filePath = $this->uploadDir . '/' . $entity->getPath();
            if (file_exists($filePath)) {
                $entity->setFile(new File($filePath));
            } else {
                $this->logger->error('Cannot find pdf file ' . $this->uploadDir . '/' . $entity->getPath());
            }
            $thumbPath = $this->uploadDir . '/' . $entity->getThumbPath();
            if (file_exists($thumbPath)) {
                $entity->setThumbFile(new File($thumbPath));
            } else {
                $this->logger->error('Cannot find thumbnail file ' . $this->uploadDir . '/' . $entity->getPath());
            }
        }
        if ($entity instanceof PdfContainerInterface) {
            $repo = $this->em->getRepository(Pdf::class);

            /** @var Pdf[] $pdfs */
            $pdfs = $repo->findBy([
                'entity' => $class . ':' . $entity->getId(),
            ]);
            $entity->setPdfs($pdfs);
        }
    }

    /**
     * Event subscriber action. After removing an pdf entity from the database
     * remove the pdf and thumbnail files.'.
     */
    public function postRemove(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();
        if ($entity instanceof Pdf && $entity->getFile()) {
            $fs = new Filesystem();

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
        if ($entity instanceof PdfContainerInterface) {
            foreach ($entity->getPdfs() as $pdf) {
                $this->em->remove($pdf);
            }
        }
    }

    public function acceptsPdfs(AbstractEntity $entity) : bool {
        return $entity instanceof PdfContainerInterface;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setThumbnailer(Thumbnailer $thumbnailer) : void {
        $this->thumbnailer = $thumbnailer;
    }
}
