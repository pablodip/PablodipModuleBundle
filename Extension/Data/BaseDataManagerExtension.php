<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Extension\Data;

use Pablodip\ModuleBundle\Extension\BaseExtension;

/**
 * BaseDataManagerExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseDataManagerExtension extends BaseExtension implements DataManagerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function defineConfiguration()
    {
        $this->getModule()->addOptions(array(
            'createDataAfterCallbacks'  => new \ArrayObject(),
            'saveDataBeforeCallbacks'   => new \ArrayObject(),
            'deleteDataBeforeCallbacks' => new \ArrayObject(),
        ));

        $this->getModule()->addCallbacks(array(
            'createQuery'  => array($this, 'createQuery'),
            'findDataById' => array($this, 'findDataById'),
            'createData'   => array($this, 'createData'),
            'saveData'     => array($this, 'saveData'),
            'deleteData'   => array($this, 'deleteData'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parseConfiguration()
    {
    }
}
