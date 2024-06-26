<?php

declare(strict_types=1);

namespace Nines\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    /**
     * Generates the configuration tree builder.
     */
    public function getConfigTreeBuilder() : TreeBuilder {
        $builder = new TreeBuilder('nines_user');
        $builder
            ->getRootNode()
            ->children()
            ->arrayNode('roles')
            ->prototype('scalar')->end()
            ->end()
            ->scalarNode('after_login_route')->defaultValue('')->end()
            ->scalarNode('after_request_route')->defaultValue('')->end()
            ->scalarNode('after_reset_route')->defaultValue('')->end()
            ->scalarNode('after_logout_route')->defaultValue('')->end()
            ->end()
            ->end()
        ;

        return $builder;
    }
}
