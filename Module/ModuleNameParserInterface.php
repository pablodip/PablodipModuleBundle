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
 * ModuleNameParserInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface ModuleNameParserInterface
{
    /**
     * Converts a notation to a class.
     *
     * @param string $module A notation.
     */
    function parse($module);
}
