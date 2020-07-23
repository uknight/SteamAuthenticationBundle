<?php

namespace Uknight\SteamAuthenticationBundle\DependencyInjection;

use Uknight\SteamAuthenticationBundle\Security\Authentication\Validator\RequestValidator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Knojector <dev@knojector.xyz>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('uknight_steam_authentication');

        $rootNode
            ->children()
                ->scalarNode('api_key')->end()
                ->scalarNode('login_route')->end()
                ->scalarNode('login_redirect')->end()
                ->scalarNode('use_fos')->end()
                ->scalarNode('user_class')->end()
                ->scalarNode('request_validator_class')->defaultValue(RequestValidator::class)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
