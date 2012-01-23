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

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * ModuleLoader.
 *
 * Based on the AnnotationFileLoader.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModuleFileLoader extends FileLoader
{
    private $container;

    /**
     * Constructor.
     *
     * @param FileLocator $locator A FileLocator instance.
     */
    public function __construct(FileLocator $locator, ContainerInterface $container)
    {
        if (!function_exists('token_get_all')) {
            throw new \RuntimeException('The Tokenizer extension is required for the module loaders.');
        }

        parent::__construct($locator);

        $this->container = $container;
    }

    /**
     * Loads from modules in a file
     *
     * @param string $file A PHP file path
     * @param string $type The resource type
     *
     * @return RouteCollection A RouteCollection instance
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $collection = new RouteCollection();
        if ($class = $this->findClass($path)) {
            // module file resource
            $collection->addResource(new FileResource($path));

            // module instance
            $module = new $class($this->container);

            // routes prefixes
            $routeNamePrefix = $module->getRouteNamePrefix();
            $routePatternPrefix = $module->getRoutePatternPrefix();

            // route actions
            foreach ($module->getRouteActions() as $action) {
                // action file resource
                $reflection = new \ReflectionObject($action);
                $collection->addResource(new FileResource($reflection->getFileName()));

                // name (module prefix + action suffix)
                $name = $routeNamePrefix.$action->getRouteNameSuffix();
                // pattern (module prefix + action suffix)
                if ('/' === $action->getRoutePatternSuffix() && '' !== $routePatternPrefix) {
                    $pattern = $routePatternPrefix;
                } else {
                    $pattern = $routePatternPrefix.$action->getRoutePatternSuffix();
                }
                // defaults (action defaults + defaults needed to execute)
                $defaults = array_merge($action->getRouteDefaults(), array(
                    '_controller' => 'PablodipModuleBundle:Module:execute',
                    '_pablodip_module.module' => $class,
                    '_pablodip_module.action' => $action->getName(),
                ));
                // requirements (just from the action)
                $requirements = $action->getRouteRequirements();

                $collection->add($name, new Route($pattern, $defaults, $requirements));
            }
        }

        return $collection;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return Boolean True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'module' === $type && is_string($resource) && 'php' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    /**
     * Returns the full class name for the first class in the file.
     *
     * @param string $file A PHP file path
     *
     * @return string|false Full class name if found, false otherwise
     */
    protected function findClass($file)
    {
        $class = false;
        $namespace = false;
        $tokens = token_get_all(file_get_contents($file));
        for ($i = 0, $count = count($tokens); $i < $count; $i++) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if (true === $class && T_STRING === $token[0]) {
                return $namespace.'\\'.$token[1];
            }

            if (true === $namespace && T_STRING === $token[0]) {
                $namespace = '';
                do {
                    $namespace .= $token[1];
                    $token = $tokens[++$i];
                } while ($i < $count && is_array($token) && in_array($token[0], array(T_NS_SEPARATOR, T_STRING)));
            }

            if (T_CLASS === $token[0]) {
                $class = true;
            }

            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }

        return false;
    }
}
