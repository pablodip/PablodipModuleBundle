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

/**
 * ActionInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface ActionInterface
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
     * @return ModuleInterface The module.
     *
     * @throws \LogicException If the module hasn't been set yet.
     */
    function getModule();

    /**
     * Returns the action name.
     *
     * @return string The action name.
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
     * Sets an option.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return Action The action (fluent interface).
     *
     * @throws \InvalidArgumentException If the option does not exist.
     */
    function setOption($name, $value);

    /**
     * Returns if an option exists.
     *
     * @return Boolean If an option exists.
     */
    function hasOption($name);

    /**
     * Returns an option value.
     *
     * @param string $name The option name.
     *
     * @return mixed The option value.
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
