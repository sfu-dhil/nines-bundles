<?php

declare(strict_types=1);

namespace Nines\EditorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    /**
     * Generates the configuration tree builder.
     */
    public function getConfigTreeBuilder() : TreeBuilder {
        $builder = new TreeBuilder('nines_editor');
        $builder->getRootNode()
            ->children()
            ->scalarNode('upload_dir')->defaultNull()->end()
            ->end()
            ->end()
        ;

        return $builder;
    }
}
