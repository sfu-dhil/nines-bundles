<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Tests\Entity;

use Nines\MediaBundle\Entity\Pdf;
use PHPUnit\Framework\TestCase;

class PdfTest extends TestCase {
    private ?Pdf $pdf = null;

    public function testSetUp() : void {
        $this->assertInstanceOf(Pdf::class, $this->pdf);
    }

    protected function setUp() : void {
        parent::setUp();
        $this->pdf = new Pdf();
    }
}
