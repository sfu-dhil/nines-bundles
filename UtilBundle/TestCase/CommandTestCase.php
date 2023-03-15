<?php

declare(strict_types=1);

namespace Nines\UtilBundle\TestCase;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

abstract class CommandTestCase extends ServiceTestCase {
    protected ?Application $app = null;

    /**
     * @param ?array<string> $arguments
     */
    protected function execute(string $name, ?array $arguments = []) : string {
        $command = $this->app->find($name);
        $tester = new CommandTester($command);
        $tester->execute($arguments);

        return $tester->getDisplay();
    }

    protected function setUp() : void {
        parent::setUp();
        $this->app = new Application(self::$kernel);
    }
}
