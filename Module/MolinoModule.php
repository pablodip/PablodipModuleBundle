<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Module;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Pablodip\ModuleBundle\Action\ActionInterface;
use Pablodip\ModuleBundle\Action\ActionCollectionInterface;
use Pablodip\ModuleBundle\Extension\ExtensionInterface;
use Molino\MolinoInterface;

/**
 * MolinoModule.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class MolinoModule extends Module
{
    private $molino;

    protected function registerExtensions()
    {
        $molino = $this->registerMolino();
        if (!$molino instanceof MolinoInterface) {
            throw new \RuntimeException('The molino must be an instance of MolinoInterface.');
        }
        $this->molino = $molino;

        return parent::registerExtensions();
    }

    /**
     * Returns the molino.
     *
     * @return MolinoInterface The molino.
     */
    public function getMolino()
    {
        return $this->molino;
    }

    /**
     * Returns the molino that will be used in the module.
     *
     * @return MolinoInterface A molino.
     */
    abstract protected function registerMolino();
}
