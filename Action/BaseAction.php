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
 * BaseAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseAction extends AbstractAction
{
    /**
     * Constructor.
     *
     * @param array $options An array of options (optional).
     */
    public function __construct(array $options = array())
    {
        $this->configure();

        if (!$this->getName()) {
            throw new \RuntimeException('An action must have name.');
        }
        if (!$this->getRouteName()) {
            throw new \RuntimeException('An action must have route name.');
        }
        if (!$this->getRoutePattern()) {
            throw new \RuntimeException('An action must have route pattern.');
        }
        if (!$this->getController()) {
            throw new \RuntimeException('An action must have controller.');
        }

        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    /**
     * Configures the action.
     *
     * You must put in this method at least the name, route name, route pattern and controller.
     */
    abstract protected function configure();
}
