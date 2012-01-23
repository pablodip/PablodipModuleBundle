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

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Resource\DirectoryResource;

/**
 * ModuleLoader.
 *
 * Based on the AnnotationDirectoryLoader.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModuleDirectoryLoader extends ModuleFileLoader
{
    /**
     * Loads from annotations from a directory.
     *
     * @param string $path A directory path
     * @param string $type The resource type
     *
     * @return RouteCollection A RouteCollection instance
     */
    public function load($path, $type = null)
    {
        $dir = $this->locator->locate($path);

        $collection = new RouteCollection();
        $collection->addResource(new DirectoryResource($dir, '/\.php$/'));
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if (!$file->isFile() || '.php' !== substr($file->getFilename(), -4)) {
                continue;
            }

            if ($class = $this->findClass($file)) {
                $refl = new \ReflectionClass($class);
                if ($refl->isAbstract() || !$refl->implementsInterface('Pablodip\ModuleBundle\Module\ModuleInterface')) {
                    continue;
                }

                $collection->addCollection(parent::load($file->getRealPath(), $type));
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
        try {
            $path = $this->locator->locate($resource);
        } catch (\Exception $e) {
            return false;
        }

        return 'module' === $type && is_string($resource) && is_dir($path);
    }
}
