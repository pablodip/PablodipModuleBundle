<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Twig;

use Pablodip\ModuleBundle\Module\ModuleManagerInterface;

/**
 * ModuleExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModuleExtension extends \Twig_Extension
{
    private $moduleManager;

    /**
     * Constructor.
     *
     * @param ModuleManagerInterface $moduleManager A module manager.
     */
    public function __construct(ModuleManagerInterface $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'get_module' => new \Twig_Function_Method($this, 'getModule'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'module';
    }

    public function getModule($module)
    {
        return $this->moduleManager->get($module)->createView();
    }
}
