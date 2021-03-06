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
 * Action.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Action extends BaseAction
{
    /**
     * Constructor.
     *
     * @param string $name       The name.
     * @param mixed  $controller The controller (a callback).
     */
    public function __construct($name, $controller)
    {
        parent::__construct();

        $this->setName($name);
        $this->setController($controller);
    }

    /**
     * {@inheritdoc}
     */
    protected function defineConfiguration()
    {
    }
}
