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
 * Adds tagged pablodip_module.field_guesser services to the admin factory.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class AddFieldGuessersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('pablodip_module.field_guesser_factory')) {
            return;
        }

        $fieldGuessers = array();
        foreach ($container->findTaggedServiceIds('pablodip_module.field_guesser') as $serviceId => $arguments) {
            $fieldGuessers[isset($arguments[0]['alias']) ? $arguments[0]['alias'] : $serviceId] = new Reference($serviceId);
        }

        $container->getDefinition('pablodip_module.field_guesser_factory')->addMethodCall('addFieldGuessers', array($fieldGuessers));
    }
}
