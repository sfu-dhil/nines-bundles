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
 * Ping the server.
 */
#[AsCommand(name: 'nines:solr:ping')]
class PingCommand extends Command {
    private ?SolrManager $manager = null;

    /**
     * Configure the command.
     */
    protected function configure() : void {
        $this->setDescription('Ping the solr server.');
    }

    /**
     * Execute the command. Returns 0 for success.
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int {
        try {
            $ping = $this->manager->ping();
            $output->writeln('Solarium library version: ' . $ping['solarium_version']);
            $output->writeln($ping['status_code'] . ' ' . $ping['response_message']);
            $output->writeln('Ping: ' . $ping['request_time'] . 'ms');
        } catch (Exception $e) {
            $output->writeln('Ping failed: ' . $e->getMessage());

            return $e->getCode();
        }

        return 0;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setSolrManager(SolrManager $manager) : void {
        $this->manager = $manager;
    }
}
