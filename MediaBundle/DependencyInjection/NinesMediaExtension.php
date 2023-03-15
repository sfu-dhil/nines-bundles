<?php

declare(strict_types=1);

namespace Nines\MediaBundle\DependencyInjection;

use Exception;
use InvalidArgumentException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class NinesMediaExtension extends Extension {
    /**
     * Loads a specific configuration.
     *
     * @param array<mixed> $configs
     *
     * @throws Exception
     * @throws InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container) : void {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nines_media.root', $config['root']);
        $container->setParameter('nines_media.thumb.width', $config['thumb_width']);
        $container->setParameter('nines_media.thumb.height', $config['thumb_height']);
    }
}
