<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Tests\Entity;

use Nines\MediaBundle\Entity\Link;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase {
    public function testToString() : void {
        $link = new Link();
        $link->setUrl('https://user:password@example.com:8080/path/to/thing?param=1&param=2#frag');
        $this->assertSame("<a href='https://user:password@example.com:8080/path/to/thing?param=1&param=2#frag'>example.com</a>", $link->__toString());
        $this->assertSame('https://user:password@example.com:8080/path/to/thing?param=1&param=2#frag', $link->getUrl());
        $this->assertSame('https', $link->getScheme());
        $this->assertSame(8080, $link->getPort());
        $this->assertSame('user', $link->getUser());
        $this->assertSame('password', $link->getPass());
        $this->assertSame('/path/to/thing', $link->getPath());
        $this->assertSame('param=1&param=2', $link->getQuery());
        $this->assertSame('frag', $link->getFragment());
    }
}
