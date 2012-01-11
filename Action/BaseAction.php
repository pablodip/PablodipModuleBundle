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

/**
 * BaseAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseAction extends AbstractAction
{
    private $constructorOptions;

    /**
     * Constructor.
     *
     * @param array $options An array of options (optional).
     */
    public function __construct(array $options = array())
    {
        parent::__construct();

        $this->constructorOptions = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setModule(ModuleInterface $module)
    {
        parent::setModule($module);

        $this->initialize();
    }

    private function initialize()
    {
        $this->defineConfiguration();

        if (!$this->getRouteNameSuffix()) {
            throw new \RuntimeException('An action must have route name suffix.');
        }
        if (!$this->getRoutePatternSuffix()) {
            throw new \RuntimeException('An action must have route pattern suffix.');
        }
        if (!$this->getController()) {
            throw new \RuntimeException('An action must have controller.');
        }

        // if there is no name the route name suffix is the name
        if (!$this->getName()) {
            $this->setName($this->getRouteNameSuffix());
        }

        foreach ($this->constructorOptions as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    /**
     * Defines the action configuration.
     *
     * You must put in this method at least the route name, pattern and controller.
     */
    abstract protected function defineConfiguration();
}
