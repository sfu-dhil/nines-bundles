<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Nines\MediaBundle\Repository\AudioRepository;
use Nines\MediaBundle\Repository\ImageRepository;
use Nines\MediaBundle\Repository\PdfRepository;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'nines:media:checksum')]
class ChecksumCommand extends Command {
    private ?EntityManagerInterface $em = null;

    private ?AudioRepository $audioRepository = null;

    private ?ImageRepository $imageRepository = null;

    private ?PdfRepository $pdfRepository = null;

    protected function configure() : void {
        $this->setDescription('Generate media file checksums');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
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
            $class = $media::class;
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

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setAudioRepository(AudioRepository $audioRepository) : void {
        $this->audioRepository = $audioRepository;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setImageRepository(ImageRepository $imageRepository) : void {
        $this->imageRepository = $imageRepository;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setPdfRepository(PdfRepository $pdfRepository) : void {
        $this->pdfRepository = $pdfRepository;
    }
}
