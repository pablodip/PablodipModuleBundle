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

use Pablodip\ModuleBundle\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * BaseAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseAction implements ActionInterface
{
    private $module;

    private $name;

    private $options;

    private $controller;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = array();
    }

    /**
     * {@inheritdoc}
     */
    public function setModule(ModuleInterface $module)
    {
        if (null !== $this->module) {
            throw new \LogicException('The module has already been set.');
        }

        $this->module = $module;

        $this->initialize();
    }

    protected function initialize()
    {
        $this->defineConfiguration();

        if (!$this->getController()) {
            throw new \RuntimeException('An action must have controller.');
        }
    }

    /**
     * Defines the action configuration.
     */
    abstract protected function defineConfiguration();

    /**
     * {@inheritdoc}
     */
    public function getModule()
    {
        if (null === $this->module) {
            throw new \LogicException('There is no module.');
        }

        return $this->module;
    }

    /**
     * Returns a module option.
     *
     * @param string $name The option name.
     *
     * @return mixed The option value.
     */
    public function getModuleOption($name)
    {
        return $this->getModule()->getOption($name);
    }

    /**
     * Returns the module container.
     *
     * @return ContainerInterface The container.
     */
    public function getContainer()
    {
        return $this->getModule()->getContainer();
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
     * {@inheritdoc}
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
     * Sets the controller.
     *
     * @param mixed $controller The controller (a callback).
     *
     * @return Action The action (fluent interface).
     *
     * @throws \InvalidArgumentException If the controller is not a callback.
     */
    public function setController($controller)
    {
        if (!is_callable($controller)) {
            throw new \InvalidArgumentException('The controller is not a callback.');
        }

        $this->controller = $controller;

        return $this;
    }

    /**
     * Returns the controller.
     *
     * @return mixed The controller.
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * {@inheritdoc}
     */
    public function executeController()
    {
        return call_user_func($this->controller);
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
        return $this->getContainer()->get('router')->generate($route, $parameters, $absolute);
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
        return $this->module->generateModuleUrl($routeNameSuffix, $parameters, $absolute);
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
     * Adds the "module" parameter.
     *
     * @param string $template   The template.
     * @param array  $parameters An array of parameters (optional).
     *
     * @return string The view rendered.
     */
    public function renderView($template, array $parameters = array())
    {
        $parameters['module'] = $this->module->createView();

        return $this->getContainer()->get('templating')->render($template, $parameters);
    }

    /**
     * Renders a view a returns a response.
     *
     * Adds the "module" parameter.
     *
     * @param string   $template   The template.
     * @param array    $parameters An array of parameters (optional).
     * @param Response $response   The response (optional).
     *
     * @return Response The response.
     */
    public function render($template, array $parameters = array(), $response = null)
    {
        if (is_array($template)) {
            // guessing template
            $parameters = $template;

            $bundle = '';
            $module = null;
            foreach (explode('\\', get_class($this->getModule())) as $part) {
                if (null === $module) {
                    if (strlen($part) > 6 && 'Bundle' === substr($part, -6)) {
                        $bundle .= $part;
                        $module = '';
                    } elseif ('Bundle' !== $part) {
                        $bundle .= $part;
                    }
                } elseif (strlen($part) > 6 && 'Module' === substr($part, -6)) {
                    $module = substr($part, 0, strlen($part) - 6);
                }
            }

            if (null === $bundle || null === $module) {
                throw new \RuntimeException(sprintf('The template for the action "%s" from the module "%s" cannot be guessed.', $this->getName(), get_class($this->getModule())));
            }

            $template = sprintf('%s:%s:%s.html.twig', $bundle, $module, $this->getName());
        }

        $parameters['module'] = $this->module->createView();

        return $this->getContainer()->get('templating')->renderResponse($template, $parameters, $response);
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
        return $this->getContainer()->get('form.factory')->create($type, $data, $options);
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
        return $this->getContainer()->get('form.factory')->createBuilder('form', $data, $options);
    }

    /**
     * Returns whether a container service exists.
     *
     * @return Boolean Whether a container service exists.
     */
    public function has($id)
    {
        return $this->getContainer()->has($id);
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
        return $this->getContainer()->get($id);
    }
}
