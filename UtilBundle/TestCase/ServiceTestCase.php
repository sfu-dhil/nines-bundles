<?php

declare(strict_types=1);

namespace Nines\UtilBundle\TestCase;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ServiceTestCase extends KernelTestCase {
    protected ?EntityManagerInterface $em = null;

    protected function setUp() : void {
        parent::setUp();
        if (getenv('TEST_DISABLE_DEBUG')) {
            self::bootKernel(['debug' => false]);
        } else {
            self::bootKernel();
        }
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }
}
