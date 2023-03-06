<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Nines\MediaBundle\Entity\Audio;
use Nines\MediaBundle\Entity\Image;
use Nines\MediaBundle\Entity\Pdf;
use Nines\MediaBundle\Entity\StoredFileInterface;
use Nines\MediaBundle\Repository\AudioRepository;
use Nines\MediaBundle\Repository\ImageRepository;
use Nines\MediaBundle\Repository\PdfRepository;

class ChecksumCommand extends Command
{
    private ?EntityManagerInterface $em = null;

    private ?AudioRepository $audioRepository = null;
    private ?ImageRepository $imageRepository = null;
    private ?PdfRepository $pdfRepository = null;

    protected static $defaultName = 'nines:media:checksum';

    protected function configure(): void {
        $this->setDescription('Generate media file checksums');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $audios = $this->audioRepository->findBy([
            'checksum' => null,
        ]);
        $images = $this->imageRepository->findBy([
            'checksum' => null,
        ]);
        $pdfs = $this->pdfRepository->findBy([
            'checksum' => null,
        ]);

        $medias = array_merge($audios, $images, $pdfs);
        foreach ($medias as $media) {
            $class = get_class($media);
            $file = $media->getFile();
            if ($file) {
                $checksum = md5_file($file->getRealPath());
                $output->writeln("Media {$class} ID {$media->getId()} checksum {$checksum}");
                $media->setChecksum($checksum);
                $this->em->persist($media);
            } else {
                $output->writeln("Media {$class} ID {$media->getId()} no file found");
            }
        }
        $this->em->flush();

        return 1;
    }

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }

    /**
     * @required
     */
    public function setAudioRepository(AudioRepository $audioRepository) : void {
        $this->audioRepository = $audioRepository;
    }

    /**
     * @required
     */
    public function setImageRepository(ImageRepository $imageRepository) : void {
        $this->imageRepository = $imageRepository;
    }

    /**
     * @required
     */
    public function setPdfRepository(PdfRepository $pdfRepository) : void {
        $this->pdfRepository = $pdfRepository;
    }
}
