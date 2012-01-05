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
 * The extensions have the same methods to configure the module than the modules:
 *
 *  * defineConfiguration
 *  * configure
 *  * parseConfiguration
 *
 * The extension methods are called before, thus the module can always modify
 * things later.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface ExtensionInterface
{
    /**
     * Sets the module.
     *
     * @param ModuleInterface $module A module.
     *
     * @throws \LogicException If the module has already been set.
     */
    function setModule(ModuleInterface $module);

    /**
     * Returns the module.
     *
     * @return Module The module.
     *
     * @throws \LogicException If the module has not been set yet.
     */
    function getModule();

    /**
     * Returns the extension name.
     *
     * @return string The extension name.
     */
    function getName();

    /**
     * @see Module::defineConfiguration()
     */
    function defineConfiguration();

    /**
     * @see Module::configure()
     */
    function configure();

    /**
     * @see Module::parseConfiguration()
     */
    function parseConfiguration();
}
