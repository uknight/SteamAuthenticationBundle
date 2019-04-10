<?php

namespace UKnight\SteamAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Knojector <dev@knojector.xyz>
 */
class UKnightSteamAuthenticationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('UKnight.steam_authentication.api_key', $config['api_key']);
        $container->setParameter('UKnight.steam_authentication.login_route', $config['login_route']);
        $container->setParameter('UKnight.steam_authentication.login_redirect', $config['login_redirect']);
        $container->setParameter('UKnight.steam_authentication.user_class', $config['user_class']);
    }
}