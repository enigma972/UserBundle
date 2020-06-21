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
        $treeBuilder = new TreeBuilder('enigma972_user');

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
                    ->scalarNode('reset_password_code_validity')
                    ->isRequired()
                    // The validity duration in secondes, 4 houres by default (14,400 sec)
                    ->defaultValue(14400)
                    ->cannotBeEmpty()
                ->end();

        $rootNode
                ->children()
                    ->scalarNode('target')
                    ->isRequired()
                    ->defaultValue('home')
                    ->cannotBeEmpty()
                ->end();

        $rootNode
            ->children()
                ->scalarNode('no_reply_mail')
                ->isRequired()
                ->defaultValue('no-reply@example.com')
                ->cannotBeEmpty()
            ->end();

        $rootNode
            ->children()
                ->booleanNode('not_send_welcome_mail')
                ->isRequired()
                ->defaultFalse()
            ->end();


        return $treeBuilder;
    }
}
