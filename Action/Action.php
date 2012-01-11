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
 * Action.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Action extends AbstractAction
{
    /**
     * Constructor.
     *
     * The name is the route name by default.
     *
     * @param string      $routeName    The route name.
     * @param string      $routePattern The route pattern.
     * @param string|null $method       The method (null or 'ANY' for any).
     * @param mixed       $controller   The controller (a callback).
     *
     * @return Action The action (fluent interface).
     */
    public function __construct($routeName, $routePattern, $method, $controller)
    {
        parent::__construct();

        $this->setName($routeName);
        $this->setRoute($routeName, $routePattern, $method);
        $this->setController($controller);

        return $this;
    }
}
