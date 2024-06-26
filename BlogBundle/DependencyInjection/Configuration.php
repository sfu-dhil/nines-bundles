<?php

declare(strict_types=1);

namespace Nines\BlogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface {
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('nines_blog');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('excerpt_length')->end()
            ->scalarNode('homepage_posts')->end()
            ->scalarNode('menu_posts')->end()
            ->scalarNode('default_status')->end()
            ->scalarNode('default_category')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
