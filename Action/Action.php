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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pablodip\ModuleBundle\Module\ModuleInterface;

/**
 * Action.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class Action implements ActionInterface
{
    private $module;
    private $container;

    private $name;

    private $routeNameSuffix;
    private $routePatternSuffix;
    private $routeDefaults;
    private $routeRequirements;

    private $options;

    /**
     * Constructor.
     *
     * @param array $options An array of options (optional).
     */
    public function __construct(array $options = array())
    {
        $this->routeDefaults = array();
        $this->routeRequirements = array();
        $this->options = array();

        $this->configure();

        if (!$this->name) {
            throw new \RuntimeException('An action must have name.');
        }
        if (!$this->routeNameSuffix) {
            throw new \RuntimeException('An action must have route name suffix.');
        }
        if (null === $this->routePatternSuffix) {
            throw new \RuntimeException('An action must have route name suffix.');
        }

        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    /**
     * Configures the action.
     *
     * You must put in this method at least the name, route name suffix and route pattern suffix.
     */
    abstract protected function configure();

    /**
     * {@inheritdoc}
     */
    public function setModule(ModuleInterface $module)
    {
        $this->module = $module;
        $this->container = $module->getContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Returns a module option.
     *
     * @param string $name The name.
     *
     * @return mixed The option.
     */
    public function getModuleOption($name)
    {
        return $this->module->getOption($name);
    }

    /**
     * Sets the name.
     *
     * @param string $name The name.
     *
     * @return Action The action (fluent interface).
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the route name suffix.
     *
     * @param string $routeNameSuffix The route name suffix.
     *
     * @return Action The action (fluent interface).
     */
    public function setRouteNameSuffix($routeNameSuffix)
    {
        $this->routeNameSuffix = $routeNameSuffix;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteNameSuffix()
    {
        return $this->routeNameSuffix;
    }

    /**
     * Sets the route pattern suffix.
     *
     * @param string $routePatternSuffix The route pattern suffix.
     *
     * @return Action The action (fluent interface).
     */
    public function setRoutePatternSuffix($routePatternSuffix)
    {
        $this->routePatternSuffix = $routePatternSuffix;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutePatternSuffix()
    {
        return $this->routePatternSuffix;
    }

    /**
     * Sets the route defaults.
     *
     * @param array $routeDefaults The route defaults.
     *
     * @return Action The action (fluent interface).
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
     * Sets the route requirements.
     *
     * @param array $routeRequirements The route requirements.
     *
     * @return Action The action (fluent interface).
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
     * Set the route (less verbose than to use all the methods).
     *
     * @param string $routeNameSuffix    The route name suffix.
     * @param string $routePatternSuffix The route pattern suffix.
     * @param array  $routeDefaults      The route defaults (optional).
     * @param array  $routeRequirements  The route requirements (optional).
     *
     * @return Action The action (fluent interface).
     */
    public function setRoute($routeNameSuffix, $routePatternSuffix, array $routeDefaults = array(), array $routeRequirements = array())
    {
        $this->setRouteNameSuffix($routeNameSuffix);
        $this->setRoutePatternSuffix($routePatternSuffix);
        $this->setRouteDefaults($routeDefaults);
        $this->setRouteRequirements($routeRequirements);

        return $this;
    }

    /**
     * Adds an option.
     *
     * @param string $name         The name.
     * @param mixed  $defaultValue The default value.
     *
     * @return Action The action (fluent interface).
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
     * @return Action The action (fluent interface).
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
     * @return Action The action (fluent interface).
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
     * Generates a URL from the given parameters.
     *
     * @param string  $name       The name of the route
     * @param array   $parameters An array of parameters
     * @param Boolean $absolute   Whether to generate an absolute URL
     *
     * @return string The generated URL
     */
    public function generateUrl($route, array $parameters = array(), $absolute = false)
    {
        return $this->container->get('router')->generate($route, $parameters, $absolute);
    }

    /**
     * Generates a module url.
     *
     * @param string  $routeNameSuffix The route name suffix.
     * @param array   $parameters      An array of parameters.
     * @param Boolean $absolute        Whether to generate an absolute URL.
     *
     * @return string The URL.
     */
    public function generateModuleUrl($routeNameSuffix, array $parameters = array(), $absolute = false)
    {
        return $this->module->generateUrl($routeNameSuffix, $parameters, $absolute);
    }

    /**
     * Forwards the request to another controller.
     *
     * @param  string  $controller The controller name (a string like BlogBundle:Post:index)
     * @param  array   $path       An array of path parameters
     * @param  array   $query      An array of query parameters
     *
     * @return Response A Response instance
     */
    public function forward($controller, array $path = array(), array $query = array())
    {
        return $this->container->get('http_kernel')->forward($controller, $path, $query);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string  $url The URL to redirect to
     * @param integer $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Renders a view.
     *
     * Adds the "_module" and "_action" parameters with their view objects.
     *
     * @param string $template   The template.
     * @param array  $parameters An array of parameters (optional).
     *
     * @return string The view rendered.
     */
    public function renderView($template, array $parameters = array())
    {
        $parameters['_module'] = $this->module->createView();
        $parameters['_action'] = $this->createView();

        return $this->container->get('templating')->render($template, $parameters);
    }

    /**
     * Renders a view a returns a response.
     *
     * Adds the "_module" and "_action" parameters with their view objects.
     *
     * @param string   $template   The template.
     * @param array    $parameters An array of parameters (optional).
     * @param Response $response   The response (optional).
     *
     * @return Response The response.
     */
    public function render($template, array $parameters = array(), $response = null)
    {
        $parameters['_module'] = $this->module->createView();
        $parameters['_action'] = $this->createView();

        return $this->container->get('templating')->renderResponse($template, $parameters, $response);
    }

    /**
     * Returns a NotFoundHttpException.
     *
     * This will result in a 404 response code. Usage example:
     *
     *     throw $this->createNotFoundException('Page not found!');
     *
     * @return NotFoundHttpException
     */
    public function createNotFoundException($message = 'Not Found', \Exception $previous = null)
    {
        return new NotFoundHttpException($message, $previous);
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type    The built type of the form
     * @param mixed $data                       The initial data for the form
     * @param array $options                    Options for the form
     *
     * @return Form
     */
    public function createForm($type, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    /**
     * Creates and returns a form builder instance
     *
     * @param mixed $data               The initial data for the form
     * @param array $options            Options for the form
     *
     * @return FormBuilder
     */
    public function createFormBuilder($data = null, array $options = array())
    {
        return $this->container->get('form.factory')->createBuilder('form', $data, $options);
    }

    /**
     * Returns whether a container service exists.
     *
     * @return Boolean Whether a container service exists.
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Returns a container service.
     *
     * @param string $id The service id.
     *
     * @return mixed The container service.
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Returns an action view instance with the action.
     *
     * @return ActionView An action view instance with the action.
     */
    public function createView()
    {
        return new ActionView($this);
    }
}
