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

/**
 * ModuleManager.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModuleManager implements ModuleManagerInterface
{
    private $container;
    private $modules;

    /**
     * Cosntructor.
     *
     * @param ContainerInterface $container A container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->modules = array();
    }

    /**
     * {@inheritdoc}
     */
    public function get($moduleClass)
    {
        if (!isset($this->modules[$moduleClass])) {
            $this->modules[$moduleClass] = new $moduleClass($this->container);
        }

        return $this->modules[$moduleClass];
    }
}
