<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace Nines\SolrBundle\Command;

use Exception;
use Solarium\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete all content from the index.
 */
class ClearCommand extends Command {
    private Client $client;

    protected static $defaultName = 'nines:solr:clear';

    /**
     * Configure the command.
     */
    protected function configure() : void {
        $this->setDescription('Clear the index.');
    }

    /**
     * Execute the command. Returns 0 for success.
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        if( ! $this->client) {
            $output->writeln("No configured Solr client.");
            return 1;
        }
        $update = $this->client->createUpdate();
        $update->addDeleteQuery('*:*');
        $update->addCommit();

        try {
            $result = $this->client->update($update);
            $output->writeln($result->getResponse()->getStatusMessage() . ' all documents deleted in ' . $result->getQueryTime() . 'ms');
        } catch (Exception $e) {
            $output->writeln($e->getMessage());

            return $e->getCode();
        }

        return 0;
    }

    /**
     * @required
     */
    public function setClient(?Client $client) : void {
        $this->client = $client;
    }
}
