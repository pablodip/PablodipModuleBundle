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

/**
 * RouteActionInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface RouteActionInterface extends ActionInterface
{
    /**
     * Returns the route name.
     *
     * @return string The route name.
     */
    function getRouteName();

    /**
     * Returns the route pattern.
     *
     * @return string The route pattern.
     */
    function getRoutePattern();

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
}
