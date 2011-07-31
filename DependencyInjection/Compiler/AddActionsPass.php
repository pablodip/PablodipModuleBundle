<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds tagged pablodip_module.action services to the admin factory.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class AddActionsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('pablodip_module.action_factory')) {
            return;
        }

        $actions = array();
        foreach ($container->findTaggedServiceIds('pablodip_module.action') as $serviceId => $arguments) {
            $actions[] = new Reference($serviceId);
        }

        $container->getDefinition('pablodip_module.action_factory')->addMethodCall('addActions', array($actions));
    }
}
