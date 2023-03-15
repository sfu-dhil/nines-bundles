<?php

declare(strict_types=1);

namespace Nines\SolrBundle\Command;

use Exception;
use Nines\SolrBundle\Services\SolrManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete all content from the index.
 */
#[AsCommand(name: 'nines:solr:clear')]
class ClearCommand extends Command {
    private ?SolrManager $manager = null;

    /**
     * Configure the command.
     */
    protected function configure() : void {
        $this->setDescription('Clear the index.');
    }

    /**
     * Execute the command. Returns 0 for success.
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int {
        try {
            $this->manager->clear();
        } catch (Exception $e) {
            $output->writeln('Clear failed: ' . $e->getMessage());

            return $e->getCode();
        }

        return 0;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setSolrManager(SolrManager $manager) : void {
        $this->manager = $manager;
    }
}
