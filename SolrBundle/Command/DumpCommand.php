<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace Nines\SolrBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Nines\SolrBundle\Mapper\EntityMapper;
use Nines\SolrBundle\Mapper\EntityMapperFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EntityMapper
     */
    private $mapper;

    protected static $defaultName = 'nines:solr:dump';

    /**
     * @param EntityMapper $mapper
     * @required
     */
    public function setEntityMapper(EntityMapper $mapper) {
        $this->mapper = $mapper;
    }

    protected function configure() : void {
        $this->setDescription('Show the solr schema.');
        $this->addArgument('class', InputArgument::REQUIRED, 'Class of the entity to dump');
        $this->addArgument('id', InputArgument::REQUIRED, 'Id of the entity to dump');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $class = $input->getArgument('class');
        if (false === mb_strpos($class, '\\')) {
            $class = 'App\\Entity\\' . $class;
        }
        $id = $input->getArgument('id');
        $entity = $this->em->find($class, $id);
        if ( ! $entity) {
            $output->writeln('Entity not found.');
        }
        dump($this->mapper->toDocument($entity)->getFields());

        return 0;
    }

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }

    public function setCopyFields($copyFields) : void {
        $this->copyFields = $copyFields;
    }
}
