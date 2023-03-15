<?php

declare(strict_types=1);

namespace Nines\DublinCoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface {
    /**
     * Generates the configuration tree builder.
     */
    public function getConfigTreeBuilder() : TreeBuilder {
        return new TreeBuilder('nines_dublin_core');
    }
}
