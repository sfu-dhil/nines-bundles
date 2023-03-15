<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Tests\Repository;

use Nines\MediaBundle\Repository\AudioRepository;
use Nines\UtilBundle\TestCase\ServiceTestCase;

class AudioRepositoryTest extends ServiceTestCase {
    private ?AudioRepository $repo = null;

    public function testSetUp() : void {
        $this->assertInstanceOf(AudioRepository::class, $this->repo);
    }

    public function testIndexQuery() : void {
        $this->assertCount(10, $this->repo->indexQuery()->execute());
    }

    public function testSearchQuery() : void {
        $this->assertCount(2, $this->repo->searchQuery('santur')->execute());
    }

    protected function setUp() : void {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->repo = static::getContainer()->get(AudioRepository::class);
    }
}
