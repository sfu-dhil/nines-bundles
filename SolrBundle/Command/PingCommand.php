<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace Nines\SolrBundle\Command;

use Nines\SolrBundle\Exception\SolrException;
use Nines\SolrBundle\Services\SolrManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ping the server.
 */
class PingCommand extends Command {
    private SolrManager $manager;

    protected static $defaultName = 'nines:solr:ping';

    /**
     * Configure the command.
     */
    protected function configure() : void {
        $this->setDescription('Ping the solr server.');
    }

    /**
     * Execute the command. Returns 0 for success.
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            $ping = $this->manager->ping();
            $output->writeln('Solarium library version: ' . $ping['solarium_version']);
            $output->writeln($ping['status_code'] . ' ' . $ping['response_message']);
            $output->writeln('Ping: ' . $ping['request_time'] . 'ms');
        } catch (SolrException $e) {
            $output->writeln('Ping failed: ' . $e->getMessage());

            return $e->getCode();
        }

        return 0;
    }

    /**
     * @required
     */
    public function setSolrManager(SolrManager $manager) : void {
        $this->manager = $manager;
    }
}
