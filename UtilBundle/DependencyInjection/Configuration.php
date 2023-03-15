<?php

declare(strict_types=1);

namespace Nines\UtilBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    /**
     * Generates the configuration tree builder.
     */
    public function getConfigTreeBuilder() : TreeBuilder {
        $builder = new TreeBuilder('nines_util');
        $builder
            ->getRootNode()
            ->children()
            ->scalarNode('trim_length')->defaultValue(50)->end()
            ->scalarNode('sender')->end()
            ->arrayNode('routing')
            ->prototype('array')
            ->children()
            ->scalarNode('class')->end()
            ->scalarNode('route')->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $builder;
    }
}
