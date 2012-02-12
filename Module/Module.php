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
use Pablodip\ModuleBundle\Action\ActionInterface;
use Pablodip\ModuleBundle\Action\RouteActionInterface;
use Pablodip\ModuleBundle\Action\ActionCollectionInterface;
use Pablodip\ModuleBundle\Extension\ExtensionInterface;

/**
 * Module.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class Module implements ModuleInterface
{
    private $container;
    private $extensions;

    private $routeNamePrefix;
    private $routePatternPrefix;
    private $parametersToPropagate;

    private $options;
    private $actions;
    private $controllerPreExecutes;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container A container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->extensions = array();

        $this->routeNamePrefix = '';
        $this->routePatternPrefix = '';
        $this->parametersToPropagate = array();

        $this->options = array();
        $this->actions = array();
        $this->controllerPreExecutes = array();

        // register extensions
        foreach ($this->registerExtensions() as $extension) {
            if (!$extension instanceof ExtensionInterface) {
                throw new \LogicException('The extensions must be an instance of ExtensionInterface.');
            }

            $name = $extension->getName();
            if (isset($this->extensions[$name])) {
                throw new \LogicException(sprintf('Trying to register two extensions with the same name: "%s".', $name));
            }

            $extension->setModule($this);
            $this->extensions[$name] = $extension;
        }

        // define configuration
        foreach ($this->extensions as $extension) {
            $extension->defineConfiguration();
        }
        $this->defineConfiguration();

        // configure
        foreach ($this->extensions as $extension) {
            $extension->configure();
        }
        $this->configure();

        // parse configuration
        foreach ($this->extensions as $extension) {
            $extension->parseConfiguration();
        }
        $this->parseConfiguration();
    }

    /**
     * Here you should register the extensions.
     *
     * Be aware to continue registering the parent extensions.
     */
    protected function registerExtensions()
    {
        return array();
    }

    /**
     * Here is where you should define your configuration.
     *
     * Defining configuration is add options, actions, parameters to propagate.
     * These things can be modified later in the configure method by other users
     * in reusable modules.
     */
    abstract protected function defineConfiguration();

    /**
     * Here is where users can configure reusable modules.
     */
    protected function configure()
    {
    }

    /**
     * Here is where you should check and/or parse the configuration for reusable modules.
     *
     * This is mostly for checking and parsing option values.
     */
    protected function parseConfiguration()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension($name)
    {
        if (!isset($this->extensions[$name])) {
            throw new \InvalidArgumentException(sprintf('The extension "%s" does not exist.', $name));
        }

        return $this->extensions[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Sets the route name prefix.
     *
     * @param string $routeNamePrefix The route name prefix.
     *
     * @return Module The module (fluent interface).
     */
    public function setRouteNamePrefix($routeNamePrefix)
    {
        $this->routeNamePrefix = $routeNamePrefix;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteNamePrefix()
    {
        return $this->routeNamePrefix;
    }

    /**
     * Sets the route pattern prefix.
     *
     * @param string $routePatternPrefix The route pattern preffix.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function setRoutePatternPrefix($routePatternPrefix)
    {
        $this->routePatternPrefix = $routePatternPrefix;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutePatternPrefix()
    {
        return $this->routePatternPrefix;
    }

    /**
     * Sets the route name and pattern prefixes.
     *
     * @param string $routeNamePrefix    The route name prefix.
     * @param string $routePatternPrefix The route pattern prefix.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function setRoutePrefixes($routeNamePrefix, $routePatternPrefix)
    {
        $this->setRouteNamePrefix($routeNamePrefix);
        $this->setRoutePatternPrefix($routePatternPrefix);

        return $this;
    }

    /**
     * Adds a parameter to propagate.
     *
     * @param string $parameter The parameter.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addParameterToPropagate($parameter)
    {
        $this->parametersToPropagate[] = $parameter;

        return $this;
    }

    /**
     * Adds parameters to propagate.
     *
     * @param array $parameters The parameters.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addParametersToPropagate(array $parameters)
    {
        foreach ($parameters as $parameter) {
            $this->addParameterToPropagate($parameter);
        }

        return $this;
    }

    /**
     * Sets the parameters to propagate.
     *
     * @param array $parameters The parameters.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function setParametersToPropagate(array $parameters)
    {
        $this->parametersToPropagate = array();
        $this->addParametersToPropagate($parameters);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParametersToPropagate()
    {
        return $this->parametersToPropagate;
    }

    /**
     * Adds an option.
     *
     * @param string $name         The name.
     * @param mixed  $defaultValue The default value.
     *
     * @return ModuleInterface The module (fluent interface).
     *
     * @throws \LogicException If the option already exists.
     */
    public function addOption($name, $defaultValue)
    {
        if ($this->hasOption($name)) {
            throw new \LogicException(sprintf('The option "%s" already exists.', $name));
        }

        $this->options[$name] = $defaultValue;

        return $this;
    }

    /**
     * Adds options.
     *
     * @param array $options The options as an array (the name as the key and the default value as the value).
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addOptions(array $options)
    {
        foreach ($options as $name => $defaultValue) {
            $this->addOption($name, $defaultValue);
        }

        return $this;
    }

    /**
     * Sets an option.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return ModuleInterface The module (fluent interface).
     *
     * @throws \InvalidArgumentException If the option does not exist.
     */
    public function setOption($name, $value)
    {
        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        $this->options[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        return $this->options[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Adds an action.
     *
     * @param ActionInterface $action An action.
     *
     * @return ModuleInterface The module (fluent interface).
     *
     * @throws \InvalidArgumentException If the action already exists.
     */
    public function addAction(ActionInterface $action)
    {
        if (isset($this->actions[$action->getName()])) {
            throw new \InvalidArgumentException(sprintf('The action "%s" already exists.', $action->getName()));
        }

        $action->setModule($this);
        $this->actions[$action->getName()] = $action;

        return $this;
    }

    /**
     * Adds actions.
     *
     * @param array $actions An array of actions.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction($name)
    {
        return isset($this->actions[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction($name)
    {
        if (!isset($this->actions[$name])) {
            throw new \InvalidArgumentException(sprintf('The action "%s" does not exist.', $name));
        }

        return $this->actions[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteActions()
    {
        $routeActions = array();
        foreach ($this->actions as $name => $action) {
            if ($action instanceof RouteActionInterface) {
                $routeActions[$name] = $action;
            }
        }

        return $routeActions;
    }

    /**
     * Shortcut for setting options to actions.
     *
     * @param string $actionName  The action name.
     * @param string $optionName  The option name.
     * @param mixed  $optionValue The option value.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function setActionOption($actionName, $optionName, $optionValue)
    {
        $this->getAction($actionName)->setOption($optionName, $optionValue);

        return $this;
    }

    /**
     * Adds a controller pre execute.
     *
     * @param \Closure $controllerPreExecute A controller pre execute.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addControllerPreExecute(\Closure $controllerPreExecute)
    {
        $this->controllerPreExecutes[] = $controllerPreExecute;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getControllerPreExecutes()
    {
        return $this->controllerPreExecutes;
    }

    /**
     * {@inheritdoc}
     */
    public function generateModuleUrl($actionName, array $parameters = array(), $absolute = false)
    {
        $routeName = $this->getRouteNamePrefix().$this->getAction($actionName)->getRouteName();

        if ($this->parametersToPropagate) {
            $request = $this->container->get('request');
            foreach ($this->parametersToPropagate as $parameter) {
                if (!isset($parameters[$parameter]) && $value = $request->get($parameter)) {
                    $parameters[$parameter] = $value;
                }
            }
        }

        return $this->container->get('router')->generate($routeName, $parameters, $absolute);
    }

    /**
     * {@inheritdoc}
     */
    public function forward($actionName, array $attributes = array(), array $query = array())
    {
        $controller = 'PablodipModuleBundle:Module:execute';
        $attributes['_module.module'] = get_class($this);
        $attributes['_module.action'] = $actionName;

        return $this->container->get('http_kernel')->forward($controller, $attributes, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function createView()
    {
        return new ModuleView($this);
    }
}
