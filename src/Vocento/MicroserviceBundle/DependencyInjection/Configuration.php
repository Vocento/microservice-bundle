<?php
/*
 * This file is part of the Vocento Software.
 *
 * (c) Vocento S.A., <desarrollo.dts@vocento.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Vocento\MicroserviceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('microservice');

        $rootNode
            ->children()
                ->scalarNode('name')
                    ->info('Service name')
                    ->isRequired()
                ->end()
            ->end()
        ;

        $this->addVersionSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addVersionSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('versions')
                ->info('versions configuration')
                ->isRequired()
                ->children()
                    ->arrayNode('list')
                        ->prototype('scalar')->end()
                        ->isRequired()
                    ->end()
                    ->scalarNode('current')->defaultValue('latest')->end()
                ->end()
            ->end()
        ;
    }
}
