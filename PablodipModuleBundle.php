<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Pablodip\ModuleBundle\DependencyInjection\Compiler\AddActionsPass;
use Pablodip\ModuleBundle\DependencyInjection\Compiler\AddActionCollectionsPass;
use Pablodip\ModuleBundle\DependencyInjection\Compiler\AddFieldGuessersPass;

/**
 * PablodipModuleBundle.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class PablodipModuleBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddActionsPass());
        $container->addCompilerPass(new AddActionCollectionsPass());
        $container->addCompilerPass(new AddFieldGuessersPass());
    }
}
