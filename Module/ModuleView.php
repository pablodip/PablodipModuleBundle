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
     * Generates a path for an action url.
     *
     * @param string $actionRouteName The action route name.
     * @param array  $parameters      An array of parameters.
     *
     * @return string The path.
     */
    public function path($actionRouteName, array $parameters = array())
    {
        return $this->module->generateActionUrl($actionRouteName, $parameters, false);
    }

    /**
     * Generates a url for an action url.
     *
     * @param string $actionRouteName The action route name.
     * @param array  $parameters      An array of parameters.
     *
     * @return string The path.
     */
    public function url($actionRouteName, array $parameters = array())
    {
        return $this->module->generateActionUrl($actionRouteName, $parameters, true);
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
