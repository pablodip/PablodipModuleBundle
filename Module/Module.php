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
    private $requiredOptions;

    private $callbacks;

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
        $this->parametersToPropagate = array();
        $this->options = array();
        $this->requiredOptions = array();
        $this->callbacks = array();
        $this->actions = array();
        $this->controllerPreExecutes = array();

        foreach ($this->registerExtensions() as $extension) {
            if (!$extension instanceof ExtensionInterface) {
                throw new \RuntimeException('The extensions must be an instance of ExtensionInterface.');
            }
            $this->extensions[] = $extension;
        }

        $this->defineConfiguration();
        $this->configure();
        $this->parseConfiguration();

        if ($diff = array_diff($this->requiredOptions, array_keys($this->options))) {
            throw new \RuntimeException(sprintf('%s requires the options: "%s".', get_class($this), implode(', ', $diff)));
        }

        if (null === $this->routePatternPrefix) {
            $this->routePatternPrefix = '/'.strtolower(str_replace('\\', '-', get_class($this)));
        }

        if (null === $this->routeNamePrefix) {
            $this->routeNamePrefix = strtolower(str_replace('\\', '_', get_class($this)));
        }
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
     * Here is where you should define your configuration for reusable modules.
     *
     * Defining configuration is add options, actions, parameters to propagate.
     * These things can be modified later in the configuration by the final module.
     */
    protected function defineConfiguration()
    {
    }

    /**
     * Here is where you should configure the module.
     */
    abstract protected function configure();

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
     * Returns the extensions.
     *
     * @return array The extensions.
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
        if (!$this->hasOption($name) && !in_array($name, $this->requiredOptions)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Returns the required options.
     *
     * @return array The required options.
     */
    public function getRequiredOptions()
    {
        return $this->requiredOptions;
    }

    /**
     * Adds a required option.
     *
     * @param string $name The name.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addRequiredOption($name)
    {
        if (in_array($name, $this->requiredOptions)) {
            throw new \LogicException(sprintf('The required option "%s" already exists.'));
        }

        $this->requiredOptions[] = $name;

        return $this;
    }

    /**
     * Adds required options.
     *
     * @param array $names An array of names.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addRequiredOptions(array $names)
    {
        foreach ($names as $name) {
            $this->addRequiredOption($name);
        }

        return $this;
    }

    /**
     * Adds a callback.
     *
     * @param string $name     The name.
     * @param mixed  $callback The callback.
     *
     * @return ModuleInterface The module (fluent interface).
     *
     * @throws \LogicException           If the callback already exists.
     * @throws \InvalidArgumentException If the callback is not callable.
     */
    public function addCallback($name, $callback)
    {
        if (array_key_exists($name, $this->callbacks)) {
            throw new \LogicException(sprintf('The callback "%s" already exists.', $name));
        }

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('The callback is not callable.');
        }

        $this->callbacks[$name] = $callback;

        return $this;
    }

    /**
     * Adds an array of callbacks, the key as the name and the value as the callback.
     *
     * @param array $callbacks An array of callbacks.
     *
     * @return ModuleInterface The Module (fluent interface).
     */
    public function addCallbacks(array $callbacks)
    {
        foreach ($callbacks as $name => $callback) {
            $this->addCallback($name, $callback);
        }

        return $this;
    }

    /**
     * Sets a callback.
     *
     * @param string $name     The name.
     * @param mixed  $callback The callback.
     *
     * @return ModuleInterface The module (fluent interface).
     *
     * @throws \InvalidArgumentException If the callback does not exist.
     * @throws \InvalidArgumentException If the callback is not callable.
     */
    public function setCallback($name, $callback)
    {
        if (!array_key_exists($name, $this->callbacks)) {
            throw new \InvalidArgumentException(sprintf('The callback "%s" does not exists.', $name));
        }

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('The callback is not callable.');
        }

        $this->callbacks[$name] = $callback;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCallback($name)
    {
        return array_key_exists($name, $this->callbacks);
    }

    /**
     * {@inheritdoc}
     */
    public function getCallback($name)
    {
        if (!array_key_exists($name, $this->callbacks)) {
            throw new \InvalidArgumentException(sprintf('The callback "%s" does not exist.', $name));
        }

        return $this->callbacks[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * {@inheritdoc}
     */
    public function call($callbackName)
    {
        if (!array_key_exists($callbackName, $this->callbacks)) {
            throw new \InvalidArgumentException(sprintf('The callback "%s" does not exist.', $callbackName));
        }

        $args = func_get_args();
        array_shift($args);

        return call_user_func_array($this->callbacks[$callbackName], $args);
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
     * Adds an option to set in an action.
     *
     * @param string $actionName  The action name.
     * @param string $optionName  The option name.
     * @param mixed  $optionValue The option value.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function setActionOption($actionName, $optionName, $optionValue)
    {
        $this->actionOptionsSets[$actionName][$optionName] = $optionValue;

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
    public function generateModuleUrl($routeNameSuffix, array $parameters = array(), $absolute = false)
    {
        $routeName = $this->getRouteNamePrefix().'_'.$routeNameSuffix;

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
    public function createView()
    {
        return new ModuleView($this);
    }
}
