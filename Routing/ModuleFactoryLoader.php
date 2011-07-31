<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Routing;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Pablodip\ModuleBundle\Module\ModuleFactory;

/**
 * ModuleFactoryLoader.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModuleFactoryLoader extends FileLoader
{
    private $moduleFactory;

    /**
     * Constructor.
     *
     * @param ModuleFactory $moduleFactory A module factory.
     */
    public function __construct(ModuleFactory $moduleFactory)
    {
        $this->moduleFactory = $moduleFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        foreach ($this->moduleFactory->getModules() as $moduleId => $module) {
            $routePatternPrefix = $module->getRoutePatternPrefix();
            $routeNamePrefix = $module->getRouteNamePrefix();

            foreach ($module->getActions() as $action) {
                $defaults = array(
                    '_controller' => 'PablodipModuleBundle:Action:execute',
                    '_pablodip_module.module' => $moduleId,
                    '_pablodip_module.action' => $action->getFullName(),
                );
                $defaults = array_merge($action->getRouteDefaults(), $defaults);
                $route = new Route($routePatternPrefix.$action->getRoutePatternSuffix(), $defaults, $action->getRouteRequirements());

                $collection->add($ups = $routeNamePrefix.'_'.$action->getRouteNameSuffix(), $route);

                $reflection = new \ReflectionObject($action);
                $collection->addResource(new FileResource($reflection->getFileName()));
            }

            $reflection = new \ReflectionObject($module);
            $collection->addResource(new FileResource($reflection->getFileName()));
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'pablodip_module' == $type;
    }
}
