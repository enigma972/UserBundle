<?php
namespace Enigma972\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('user');

        /** @var NodeDefinition|ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
                ->children()
                    ->booleanNode('check_mail')
                    ->isRequired()
                    ->defaultFalse()
                ->end();

        $rootNode
                ->children()
                    ->scalarNode('target')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end();

        $rootNode
            ->children()
                ->scalarNode('no_reply_mail')
                ->isRequired()
                ->cannotBeEmpty()
            ->end();


        return $treeBuilder;
    }
}
