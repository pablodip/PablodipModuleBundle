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

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Pablodip\ModuleBundle\Action\ActionInterface;
use Pablodip\ModuleBundle\Action\ActionCollectionInterface;
use Pablodip\ModuleBundle\Field\Field;
use Pablodip\ModuleBundle\Field\FieldConfigurator;

/**
 * Module.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class Module extends ContainerAware implements ModuleInterface
{
    private $dataClass;

    private $routeNamePrefix;
    private $routePatternPrefix;
    private $parametersToPropagate;

    private $options;
    private $requiredOptions;

    private $rawFields;
    private $fields;

    private $rawFieldGuessers;
    private $fieldGuessers;

    private $rawActions;
    private $actionParsers;
    private $actions;
    private $actionOptionsSets;
    private $actionOptionsProcessors;
    private $actionCollectionOptionsSets;

    private $controllerPreExecutes;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container A container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);

        $this->parametersToPropagate = array();

        $this->options = array();
        $this->requiredOptions = array();

        $this->rawFields = array();

        $this->rawFieldGuessers = array();

        $this->rawActions = array();
        $this->actionParsers = array();
        $this->actionOptionsSets = array();
        $this->actionOptionsProcessors = array();
        $this->actionCollectionOptionsSets = array();

        $this->controllerPreExecutes = array();

        $this->preConfigure();
        $this->configure();
        $this->postConfigure();

        if (!$this->dataClass) {
            throw new \RuntimeException('A module must have data class.');
        }

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
     * Pre configures the module.
     */
    protected function preConfigure()
    {
    }

    /**
     * Configures the module.
     */
    abstract protected function configure();

    /**
     * Post configures the module.
     */
    protected function postConfigure()
    {
    }

    /**
     * Returns the container.
     *
     * @return ContainerInterface The container.
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Configures the fields for an action.
     *
     * @param ActionInterface   $action            An action.
     * @param FieldConfigurator $fieldConfigurator A FieldConfigurator instance.
     */
    public function configureFieldsByAction(ActionInterface $action, FieldConfigurator $fieldConfigurator)
    {
    }

    /**
     * Sets the data class.
     *
     * @param string $dataClass The data class.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function setDataClass($dataClass)
    {
        $this->dataClass = $dataClass;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataClass()
    {
        return $this->dataClass;
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
     * Adds a field.
     *
     * @param string $name   The name.
     * @param array  $option An array of options (optional).
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addField($name, array $options = array())
    {
        $this->rawFields[$name] = $options;

        return $this;
    }

    /**
     * Adds fields.
     *
     * You can define the fields in two ways:
     *
     *   * The name as the key and the options as the value.
     *   * The name as the value (without options).
     *
     * @param array $fields An array of fields.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $name => $options) {
            if (is_integer($name) && is_string($options)) {
                $name = $options;
                $options = array();
            }
            $this->addField($name, $options);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        if (null === $this->fields) {
            $this->initializeFields();
        }

        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($name)
    {
        if (null === $this->fields) {
            $this->initializeFields();
        }

        return isset($this->fields[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getField($name)
    {
        if (!$this->hasField($name)) {
            throw new \InvalidArgumentException(sprintf('The field "%s" does not exist.', $name));
        }

        return $this->fields[$name];
    }

    /**
     * Adds a field guesser.
     *
     * @param mixed $fieldGuesser A field guesser.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addFieldGuesser($fieldGuesser)
    {
        $this->rawFieldGuessers[] = $fieldGuesser;

        return $this;
    }

    /**
     * Adds field guessers.
     *
     * @param array $fieldGuessers An array of field guessers.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addFieldGuessers(array $fieldGuessers)
    {
        foreach ($fieldGuessers as $fieldGuesser) {
            $this->addFieldGuesser($fieldGuesser);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldGuessers()
    {
        if (null == $this->fieldGuessers) {
            $this->initializeFieldGuessers();
        }

        return $this->fieldGuessers;
    }

    /**
     * Adds an action.
     *
     * @param string|Action|ActionCollection $action An action.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addAction($action)
    {
        if (!is_string($action) && !$action instanceof ActionInterface && !$action instanceof ActionCollectionInterface) {
            throw new \InvalidArgumentException('Some action is not an string nor an instance of ActionInterface nor ActionCollectionInterface.');
        }

        $this->rawActions[is_string($action) ? $action : $action->getFullName()] = $action;

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
    public function getActions()
    {
        if (null === $this->actions) {
            $this->initializeActions();
        }

        return $this->actions;
    }

    /**
     * Returns whether an action exists or not.
     *
     * @param string $fullName The action's full name.
     *
     * @return Boolean Whether the action exists or not.
     */
    public function hasAction($fullName)
    {
        if (null === $this->actions) {
            $this->initializeActions();
        }

        return isset($this->actions[$fullName]);
    }

    /**
     * Returns an action by full name.
     *
     * @param string $fullName The action's full name.
     *
     * @return Action The action.
     *
     * @throws \InvalidArgumentException If the action does not exist.
     */
    public function getAction($fullName)
    {
        if (!$this->hasAction($fullName)) {
            throw new \InvalidArgumentException(sprintf('The action "%s" does not exist.', $fullName));
        }

        return $this->actions[$fullName];
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
     * Adds an option processor to an action.
     *
     * @param string   $actionName The action name.
     * @param string   $optionName The option name.
     * @param \Closure $processor  The processor.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addActionOptionProcessor($actionName, $optionName, \Closure $processor)
    {
        $this->actionOptionsProcessors[$actionName][$optionName][] = $processor;

        return $this;
    }

    /**
     * Adds an option to set in an action collection.
     *
     * @param string $actionCollectionName  The action collection name.
     * @param string $optionName  The option name.
     * @param mixed  $optionValue The option value.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function setActionCollectionOption($actionCollectionName, $optionName, $optionValue)
    {
        $this->actionCollectionOptionsSets[$actionCollectionName][$optionName] = $optionValue;

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
     * Returns the controller pre executes.
     *
     * @return array The controller pre executes.
     */
    public function getControllerPreExecutes()
    {
        return $this->controllerPreExecutes;
    }

    /**
     * {@inheritdoc}
     */
    public function generateUrl($routeNameSuffix, array $parameters = array(), $absolute = false)
    {
        if ($this->parametersToPropagate) {
            $request = $this->container->get('request');
            foreach ($this->parametersToPropagate as $parameter) {
                if (!isset($parameters[$parameter]) && $value = $request->get($parameter)) {
                    $parameters[$parameter] = $value;
                }
            }
        }

        return $this->container->get('router')->generate($this->getRouteNamePrefix().'_'.$routeNameSuffix, $parameters, $absolute);
    }

    /**
     * Returns a field value for a data.
     *
     * @param mixed  $data      The data.
     * @param string $fieldName The field name.
     *
     * @return The value.
     */
    public function getDataFieldValue($data, $fieldName)
    {
        return $data->{'get'.ucfirst($fieldName)}();
    }

    private function initializeFields()
    {
        $fields = array();
        foreach ($this->rawFields as $name => $options) {
            $fields[$name] = new Field($name, $options);
        }
        $this->fields = $fields;
        $this->rawFields = null;
    }

    private function initializeFieldGuessers()
    {
        $fieldGuessers = array();
        foreach ($this->rawFieldGuessers as $rawFieldGuesser) {
            if (is_string($rawFieldGuesser)) {
                $rawFieldGuesser = $this->container->get('pablodip_module.field_guesser_factory')->get($rawFieldGuesser);
            }
            $fieldGuessers[] = $rawFieldGuesser;
        }
        $this->fieldGuessers = $fieldGuessers;
        $this->rawFieldGuessers = null;
    }

    private function initializeActions()
    {
        $actions = array();
        foreach ($this->cleanActions($this->rawActions) as $action) {
            if (isset($actions[$action->getFullName()])) {
                throw new \RuntimeException(sprintf('You cannot use the action "%s" more than once.', $action->getFullName()));
            }
            $action->setModule($this);
            $action->setContainer($this->container);

            foreach ($action->getActionProcessors() as $actionName => $processor) {
                $actionToProcess = $this->findAction($actions, $actionName);
                if (null !== $actionToProcess) {
                    $processor($actionToProcess);
                }
            }

            $actions[$action->getFullName()] = $action;
        }

        // action options sets
        foreach ($this->actionOptionsSets as $actionName => $options) {
            $action = $this->findAction($actions, $actionName);

            foreach ($options as $name => $value) {
                $action->setOption($name, $value);
            }
        }

        // action options processors
        foreach ($this->actionOptionsProcessors as $actionName => $options) {
            $action = $this->findAction($actions, $actionName);

            foreach ($options as $name => $processors) {
                $value = $action->getOption($name);
                foreach ($processors as $processor) {
                    $value = $processor($value);
                }
                $action->setOption($name, $value);
            }
        }

        $this->actions = $actions;
        $this->rawActions = null;
    }

    private function cleanActions(array $inputActions)
    {
        $actions = array();
        foreach ($inputActions as $action) {
            // in the container
            if (is_string($action)) {
                // action
                if ($this->container->get('pablodip_module.action_factory')->has($action)) {
                    $action = clone $this->container->get('pablodip_module.action_factory')->get($action);
                } else {
                    // collection
                    if ($this->container->get('pablodip_module.action_collection_factory')->has($action)) {
                        $action = clone $this->container->get('pablodip_module.action_collection_factory')->get($action);
                    }
                }
            }

            // normal action
            if ($action instanceof ActionInterface) {
                $actions[] = $action;
                continue;
            }
            // action collection
            if ($action instanceof ActionCollectionInterface) {
                foreach (array($action->getFullName(), $action->getName()) as $actionCollectionName) {
                    if (isset($this->actionCollectionOptionsSets[$actionCollectionName])) {
                        foreach ($this->actionCollectionOptionsSets[$actionCollectionName] as $optionName => $optionValue) {
                            $action->setOption($optionName, $optionValue);
                        }
                        break;
                    }
                }

                $actions = array_merge($actions, $this->cleanActions($action->getActions()));
                continue;
            }

            // invalid
            throw new \RuntimeException('The action is not an instance of ActionInterface nor ActionCollectionInterface.');
        }

        return $actions;
    }

    private function findAction(array $actions, $actionName, $throwException = true)
    {
        // by full name
        if (isset($actions[$actionName])) {
            return $actions[$actionName];
        }
        // by name
        foreach ($actions as $action) {
            if ($action->getName() == $actionName) {
                return $action;
            }
        }
        // action does not exist
        if ($throwException) {
            throw new \RuntimeException(sprintf('The action "%s" does not exist.', $actionName));
        }
    }
    /**
     * Returns a module view with the module.
     *
     * @return ModuleView A module view.
     */
    public function createView()
    {
        return new ModuleView($this);
    }
}
