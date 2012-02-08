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

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Converts a short notation "PablodipBlogModuleBundle:Blog" to
 * the full class: "Pablodip\PablodipBlogModuleBundle\Module\BlogModule".
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModuleNameParser implements ModuleNameParserInterface
{
    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance.
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($module)
    {
        if (2 !== count($parts = explode(':', $module))) {
            throw new \InvalidArgumentException(sprintf('The "%s" module is not a valid a:b module string.', $module));
        }

        list($bundle, $module) = $parts;
        $module = str_replace('/', '\\', $module);
        $class = null;
        $logs = array();
        foreach ($this->kernel->getBundle($bundle, false) as $b) {
            $try = $b->getNamespace().'\\Module\\'.$module.'Module';
            if (class_exists($try)) {
                $class = $try;

                break;
            }
        }

        if (null === $class) {
            throw new \InvalidArgumentException(sprintf('Unable to find module "%s:%s".', $bundle, $module));
        }

        return $class;
    }
}
