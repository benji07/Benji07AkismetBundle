<?php

namespace Benji\Bundle\AkismetBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * AkismetBundle configuration structure.
 *
 * @author Benjamin Lévêque <benjamin@leveque.me>
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return Symfony\Component\Config\Definition\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('benji_akismet', 'array');

        $rootNode
            ->children()
                ->scalarNode('key')->end()
                ->scalarNode('blog')->end()
            ->end()
        ;

        return $treeBuilder->buildTree();
    }
}
