<?php

declare(strict_types=1);

namespace Nines\SolrBundle\DependencyInjection;

use Exception;
use InvalidArgumentException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class NinesSolrExtension extends Extension {
    /**
     * Loads a specific configuration.
     *
     * @param array<mixed> $configs
     *
     * @throws Exception
     * @throws InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container) : void {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nines_solr.url', $config['url']);
        $container->setParameter('nines_solr.enabled', $config['enabled']);
        $container->setParameter('nines_solr.page_size', $config['page_size']);
    }
}
