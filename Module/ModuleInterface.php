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

/**
 * ModuleInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface ModuleInterface
{
    /**
     * Constructor.
     *
     * @param ContainerInterface $container A container.
     */
    function __construct(ContainerInterface $container);

    /**
     * Returns the route name prefix.
     *
     * @return string The route name prefix.
     */
    function getRouteNamePrefix();

    /**
     * Returns the route pattern prefix.
     *
     * @return string The route pattern prefix.
     */
    function getRoutePatternPrefix();

    /**
     * Returns the parameters to propagate.
     *
     * @return array The parameters to propagate.
     */
    function getParametersToPropagate();

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
     * Returns whether an action exists.
     *
     * @param string $name The name.
     *
     * @return Boolean Whether the action exists.
     */
    function hasAction($name);

    /**
     * Returns an action.
     *
     * @param string $name The name.
     *
     * @return ActionInterface The action.
     *
     * @throws \InvalidArgumentException If the action does not exist.
     */
    function getAction($name);

    /**
     * Returns the actions.
     *
     * @return array An array of actions.
     */
    function getActions();

    /**
     * Returns the controller pre executes.
     *
     * @return array The controller pre executes.
     */
    function getControllerPreExecutes();

    /**
     * Generated an admin url.
     *
     * @param string  $routeNameSuffix The route name suffix.
     * @param array   $parameters      An array of parameters.
     * @param Boolean $absolute        Whether to generate an absolute url or not.
     *
     * @return string The url.
     */
    function generateUrl($routeNameSuffix, array $parameters = array(), $absolute = false);
}
