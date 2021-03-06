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

/**
 * ModuleManagerInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface ModuleManagerInterface
{
    /**
     * Returns a module.
     *
     * @param The module.
     *
     * @return ModuleInterface A module.
     */
    function get($module);
}
