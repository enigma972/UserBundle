<?php
namespace Enigma972\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('user');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
                ->children()
                    ->booleanNode('check_mail')
                    ->isRequired()
                    ->defaultFalse()
                    ->end()
                ->end()
            ;

        return $treeBuilder;
    }
}
