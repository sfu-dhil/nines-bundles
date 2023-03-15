<?php

declare(strict_types=1);

namespace Nines\UserBundle\Tests\Command;

use Nines\UserBundle\Repository\UserRepository;
use Nines\UtilBundle\TestCase\CommandTestCase;

class ActivateUserCommandTest extends CommandTestCase {
    private ?UserRepository $repo = null;

    public function testExecute() : void {
        $output = $this->execute('nines:user:activate', [
            'email' => 'inactive@example.com',
        ]);

        $this->assertSame("Account inactive@example.com is active.\n", $output);
        $user = $this->repo->findOneByEmail('inactive@example.com');
        $this->assertNotNull($user);
        $this->assertTrue($user->isActive());
    }

    protected function setUp() : void {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->repo = static::getContainer()->get(UserRepository::class);
    }
}
