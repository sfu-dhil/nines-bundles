<?php

declare(strict_types=1);

namespace Nines\MediaBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Nines\MediaBundle\Entity\Pdf;
use Nines\MediaBundle\Service\PdfManager;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PdfFixtures extends Fixture implements FixtureGroupInterface {
    public const FILES = [
        'holmes_1.pdf',
        'holmes_2.pdf',
        'holmes_3.pdf',
        'holmes_4.pdf',
        'holmes_5.pdf',
    ];

    private ?PdfManager $manager = null;

    public static function getGroups() : array {
        return ['test', 'dev'];
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager) : void {
        $this->manager->setCopy(true);
        for ($i = 1; $i <= 5; $i++) {
            $file = self::FILES[$i - 1];
            $upload = new UploadedFile(dirname(__DIR__, 2) . '/MediaBundle/Tests/data/pdf/' . $file, $file, 'application/pdf', null, true);
            $pdf = new Pdf();
            $pdf->setFile($upload);
            $pdf->setOriginalName($file);
            $pdf->setDescription("<p>This is paragraph {$i}</p>");
            $pdf->setLicense("<p>This is paragraph {$i}</p>");
            $pdf->setEntity(stdClass::class . ':' . $i);

            $manager->persist($pdf);
            $manager->flush();
        }
        $this->manager->setCopy(false);
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setManager(PdfManager $manager) : void {
        $this->manager = $manager;
    }
}
