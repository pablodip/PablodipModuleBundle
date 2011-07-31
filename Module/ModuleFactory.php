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

/**
 * ModuleFactory.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModuleFactory extends ContainerAware
{
    private $moduleIds;

    /**
     * Constructor.
     *
     * @param array $moduleIds The module ids.
     */
    public function __construct(array $moduleIds)
    {
        $this->moduleIds = $moduleIds;
    }

    /**
     * Returns the module ids.
     *
     * @return array The module ids.
     */
    public function getModuleIds()
    {
        return $this->moduleIds;
    }

    /**
     * Returns the modules.
     *
     * @return array The modules.
     */
    public function getModules()
    {
        $modules = array();
        foreach ($this->moduleIds as $moduleId) {
            $modules[$moduleId] = $this->container->get($moduleId);
        }

        return $modules;
    }
}
