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
     * Returns the data class.
     *
     * @return string The data class.
     */
    function getDataClass();

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
     * Returns the fields.
     *
     * @return array An array of fields.
     */
    function getFields();

    /**
     * Returns whether a field exists or not.
     *
     * @param string $name The field name.
     *
     * @return Boolean Whether the field exists or not.
     */
    function hasField($name);

    /**
     * Returns a field.
     *
     * @param string $name The field name.
     *
     * @return Field The field.
     *
     * @throws \InvalidArgumentException If the field does not exist.
     */
    function getField($name);

    /**
     * Returns the field guessers.
     *
     * @return array The field guessers.
     */
    function getFieldGuessers();

    /**
     * Returns the actions.
     *
     * @return array An array of actions.
     */
    function getActions();

    /**
     * Returns an action option.
     *
     * @param string $actionName The action name.
     * @param string $optionName The option name.
     *
     * @return mixed The option value.
     */
    function getActionOption($actionName, $optionName);

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

    /**
     * Returns a field value for a data.
     *
     * @param mixed  $data      The data.
     * @param string $fieldName The field name.
     *
     * @return The value.
     */
    function getDataFieldValue($data, $fieldName);
}
