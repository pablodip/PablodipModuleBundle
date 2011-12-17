<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Action;

use Pablodip\ModuleBundle\Module\ModuleInterface;

interface ActionInterface
{
    /**
     * Sets the module.
     *
     * @param ModuleInterface $module A module.
     */
    function setModule(ModuleInterface $module);

    /**
     * Returns the module.
     *
     * @return ModuleInterface The module.
     */
    function getModule();

    /**
     * Returns the name.
     *
     * @return string The name.
     */
    function getName();

    /**
     * Returns the route name suffix.
     *
     * @return string The route name suffix.
     */
    function getRouteNameSuffix();

    /**
     * Returns the route pattern suffix.
     *
     * @return string The route pattern suffix.
     */
    function getRoutePatternSuffix();

    /**
     * Returns the route defaults.
     *
     * @return array The route defaults.
     */
    function getRouteDefaults();

    /**
     * Returns the route requirements.
     *
     * @return array The route requirements.
     */
    function getRouteRequirements();

    /**
     * Returns where an option exists or not.
     *
     * @param string $name The name.
     *
     * @return Boolean Where the option exists or not.
     */
    function hasOption($name);

    /**
     * Returns an option value.
     *
     * @param string $name The name.
     *
     * @return mixed The value.
     *
     * @throws \InvalidArgumentException If the option does not exist.
     */
    function getOption($name);

    /**
     * Returns the options.
     *
     * @return array The options.
     */
    function getOptions();

    /**
     * Executes the controller.
     *
     * @return Response A response object.
     */
    function executeController();
}
