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
 * ExtensionInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface ExtensionInterface
{
    /**
     * Applies the extension to a module.
     *
     * @param ModuleInterface $module A module.
     */
    function apply(ModuleInterface $module);
}
