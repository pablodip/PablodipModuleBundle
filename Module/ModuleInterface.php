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
     * Returns the container.
     *
     * @return ContainerInterface The container.
     */
    function getContainer();

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
     * Returns whether an option exists or not.
     *
     * @param string $name The name.
     *
     * @return Boolean Whether the option exists or not.
     */
    function hasOption($name);

    /**
     * Returns an option value by name.
     *
     * @param string $name The name.
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
     * Returns the route actions.
     *
     * @return array The route actions;
     */
    function getRouteActions();

    /**
     * Returns the controller pre executes.
     *
     * @return array The controller pre executes.
     */
    function getControllerPreExecutes();

    /**
     * Generates a module url.
     *
     * @param string  $routeNameSuffix The routeNameSuffix.
     * @param array   $parameters      An array of parameters.
     * @param Boolean $absolute        Whether to generate an absolute url or not.
     *
     * @return string The url.
     */
    function generateModuleUrl($routeNameSuffix, array $parameters = array(), $absolute = false);

    /**
     * Forwards the request to an action.
     *
     * @param string $actionName The action name.
     * @param array  $attributes An array of attributes (optional).
     * @param array  $query      An array of query parameters (optional).
     *
     * @return Response A response.
     */
    function forward($actionName, array $attributes = array(), array $query = array());

    /**
     * Returns a module view with the module.
     *
     * @return ModuleView A module view.
     */
    function createView();
}
