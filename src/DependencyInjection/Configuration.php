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
 * Class Configuration.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('microservice');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('name')
                    ->info('Service name')
                    ->isRequired()
                ->end()
                ->scalarNode('code_version')
                    ->info('Code version')
                    ->defaultValue('unknown')
                ->end()
                ->booleanNode('debug')
                    ->info('Enable debug mode')
                    ->defaultFalse()
                ->end()
                ->booleanNode('manage_exceptions')
                    ->info('Manage exceptions')
                    ->defaultTrue()
                ->end()
            ->end();

        $this->addVersionSection($rootNode);

        return $treeBuilder;
    }

    private function addVersionSection(ArrayNodeDefinition $rootNode): void
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
            ->end();
    }
}
