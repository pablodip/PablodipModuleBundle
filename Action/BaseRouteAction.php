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

use Symfony\Component\HttpFoundation\Request;

/**
 * BaseRouteAction
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseRouteAction extends BaseAction implements RouteActionInterface
{
    private $routeName;
    private $routePattern;
    private $routeDefaults;
    private $routeRequirements;
    private $routeOptions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->routeDefaults = array();
        $this->routeRequirements = array();
        $this->routeOptions = array();
    }

    protected function initialize()
    {
        parent::initialize();

        if (!$this->getRouteName()) {
            throw new \RuntimeException('An action must have route name.');
        }
        if (!$this->getRoutePattern()) {
            throw new \RuntimeException('An action must have route pattern.');
        }

        // if there is no name the route name is the name
        if (!$this->getName()) {
            $this->setName($this->getRouteName());
        }
    }

    /**
     * Sets the route name.
     *
     * @param string $routeName The route name.
     *
     * @return AbstractAction The action (fluent interface).
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * Sets the route pattern.
     *
     * @param string $routePattern The route pattern.
     *
     * @return AbstractAction The action (fluent interface).
     */
    public function setRoutePattern($routePattern)
    {
        $this->routePattern = $routePattern;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutePattern()
    {
        return $this->routePattern;
    }

    /**
     * Sets the route defaults.
     *
     * @param array $routeDefaults The route defaults.
     *
     * @return AbstractAction The action (fluent interface).
     */
    public function setRouteDefaults(array $routeDefaults)
    {
        $this->routeDefaults = $routeDefaults;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDefaults()
    {
        return $this->routeDefaults;
    }

    /**
     * Sets a route default.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return AbstractAction The action (fluent interface).
     */
    public function setRouteDefault($name, $value)
    {
        $this->routeDefaults[$name] = $value;

        return $this;
    }

    /**
     * Sets the route requirements.
     *
     * @param array $routeRequirements The route requirements.
     *
     * @return AbstractAction The action (fluent interface).
     */
    public function setRouteRequirements(array $routeRequirements)
    {
        $this->routeRequirements = $routeRequirements;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteRequirements()
    {
        return $this->routeRequirements;
    }

    /**
     * Sets a route requirement.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return AbstractAction The action (fluent interface).
     */
    public function setRouteRequirement($name, $value)
    {
        $this->routeRequirements[$name] = $value;

        return $this;
    }

    /**
     * Sets the route options.
     *
     * @param array $routeOptions The route options.
     *
     * @return AbstractAction The action (fluent interface).
     */
    public function setRouteOptions(array $routeOptions)
    {
        $this->routeOptions = $routeOptions;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteOptions()
    {
        return $this->routeOptions;
    }

    /**
     * Sets a route option.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return AbstractAction The action (fluent interface).
     */
    public function setRouteOption($name, $value)
    {
        $this->routeOptions[$name] = $value;

        return $this;
    }

    /**
     * Set the route (less verbose than to use all the methods).
     *
     * @param string      $name    The route name.
     * @param string      $pattern The route pattern.
     * @param string|null $method  The method ('ANY' for any).
     *
     * @return AbstractAction The action (fluent interface).
     */
    public function setRoute($name, $pattern, $method)
    {
        $this->setRouteName($name);
        $this->setRoutePattern($pattern);
        $this->setRouteRequirements('ANY' === $method ? array() : array('_method' => $method));
        $this->setRouteDefaults(array());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function executeController()
    {
        $arguments = $this->getArguments($this->getContainer()->get('request'), $this->getController());

        return call_user_func_array($this->getController(), $arguments);
    }

    /*
     * Code from Symfony ControllerResolver.
     */
    private function getArguments(Request $request, $controller)
    {
        if (is_array($controller)) {
            $r = new \ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && !$controller instanceof \Closure) {
            $r = new \ReflectionObject($controller);
            $r = $r->getMethod('__invoke');
        } else {
            $r = new \ReflectionFunction($controller);
        }

        return $this->doGetArguments($request, $controller, $r->getParameters());
    }

    private function doGetArguments(Request $request, $controller, array $parameters)
    {
        $attributes = $request->attributes->all();
        $arguments = array();
        foreach ($parameters as $param) {
            if (array_key_exists($param->getName(), $attributes)) {
                $arguments[] = $attributes[$param->getName()];
            } elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
                $arguments[] = $request;
            } elseif ($param->getClass() && $param->getClass()->isInstance($this)) {
                $arguments[] = $this;
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } else {
                if (is_array($controller)) {
                    $repr = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
                } elseif (is_object($controller)) {
                    $repr = get_class($controller);
                } else {
                    $repr = $controller;
                }

                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->getName()));
            }
        }

        return $arguments;
    }
}
