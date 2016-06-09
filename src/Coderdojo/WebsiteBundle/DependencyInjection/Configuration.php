<?php

namespace Coderdojo\WebsiteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('coderdojo_website');

        $rootNode
            ->children()
                ->scalarNode('slack_api_token')->defaultNull()->end()
                ->scalarNode('eventbrite_api_token')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
