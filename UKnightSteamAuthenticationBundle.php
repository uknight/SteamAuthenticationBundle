<?php

namespace UKnight\SteamAuthenticationBundle;

use UKnight\SteamAuthenticationBundle\Security\Factory\SteamFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Knojector <dev@knojector.xyz>
 */
class UKnightSteamAuthenticationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new SteamFactory());
    }
}