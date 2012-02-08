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
    private $modules;
    private $container;
    private $parser;

    /**
     * Cosntructor.
     *
     * @param ContainerInterface        $container A container.
     * @param ModuleNameParserInterface $parser    A module name parser.
     */
    public function __construct(ContainerInterface $container, ModuleNameParserInterface $parser)
    {
        $this->modules = array();

        $this->container = $container;
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function get($module)
    {
        if (false !== strpos($module, ':')) {
            $module = $this->parser->parse($module);
        }

        if (!isset($this->modules[$module])) {
            $this->modules[$module] = new $module($this->container);
        }

        return $this->modules[$module];
    }
}
