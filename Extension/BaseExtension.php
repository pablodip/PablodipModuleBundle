<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Extension;

use Pablodip\ModuleBundle\Module\ModuleInterface;

/**
 * BaseExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseExtension implements ExtensionInterface
{
    private $module;

    /**
     * {@inheritdoc}
     */
    public function setModule(ModuleInterface $module)
    {
        if (null !== $this->module) {
            throw new \LogicException('The module has already been set.');
        }

        $this->module = $module;
    }

    /**
     * {@inheritdoc}
     */
    public function getModule()
    {
        if (null === $this->module) {
            throw new \LogicException('The module has not been set yet.');
        }

        return $this->module;
    }
}
