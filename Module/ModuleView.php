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

/**
 * ModuleView.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModuleView
{
    private $module;

    /**
     * Constructor.
     *
     * @param ModuleInterface $module.
     */
    public function __construct(ModuleInterface $module)
    {
        $this->module = $module;
    }

    /**
     * Returns the module.
     *
     * @return ModuleInterface The module.
     */
    protected function getModule()
    {
        return $this->module;
    }

    /**
     * Generates a path for a module url.
     *
     * @param string $routeNameSuffix The route name suffix.
     * @param array  $parameters      An array of parameters.
     *
     * @return string The path.
     */
    public function path($routeNameSuffix, array $parameters = array())
    {
        return $this->module->generateModuleUrl($routeNameSuffix, $parameters, false);
    }

    /**
     * Generates a url for a module url.
     *
     * @param string $routeNameSuffix The route name suffix.
     * @param array  $parameters      An array of parameters.
     *
     * @return string The path.
     */
    public function url($routeNameSuffix, array $parameters = array())
    {
        return $this->module->generateModuleUrl($routeNameSuffix, $parameters, true);
    }

    /**
     * Returns a module option.
     *
     * @param string $name The name.
     *
     * @return mixed The value.
     */
    public function getOption($name)
    {
        return $this->module->getOption($name);
    }

    /**
     * Returns an action option.
     *
     * @param string $actionName The action name.
     * @param string $optionName The option name.
     *
     * @return mixed The option value.
     */
    public function getActionOption($actionName, $optionName)
    {
        return $this->module->getAction($actionName)->getOption($optionName);
    }

    /**
     * Renders an action.
     *
     * @param string $actionName The action name.
     * @param array  $attributes An array of attributes (optional).
     * @param array  $options    An array of options (optional).
     *
     * @see Symfony\Bundle\FrameworkBundle\HttpKernel::render()
     */
    public function render($actionName, array $attributes = array(), array $options = array())
    {
        $controller = 'PablodipModuleBundle:Module:execute';

        $attributes['_module.module'] = get_class($this->module);
        $attributes['_module.action'] = $actionName;

        $options['attributes'] = $attributes;

        return $this->module->getContainer()->get('http_kernel')->render($controller, $options);
    }

    /**
     * Returns the parameters to propagate.
     *
     * Useful when you have to propagate parameters in a form.
     *
     * @return array The parameters to propagate.
     */
    public function getParametersToPropagate()
    {
        return $this->module->getParametersToPropagate();
    }
}
