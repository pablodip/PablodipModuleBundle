<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Extension\Model;

use Pablodip\ModuleBundle\Extension\BaseExtension;
use Pablodip\ModuleBundle\OptionBag;

/**
 * BaseModelManagerExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseModelManagerExtension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function defineConfiguration()
    {
        $this->getModule()->addOptions(array(
            'createModelAfterCallbacks'  => new OptionBag(),
            'saveModelBeforeCallbacks'   => new OptionBag(),
            'deleteModelBeforeCallbacks' => new OptionBag(),
        ));

        $this->getModule()->addCallbacks(array(
            'createModelQuery'  => array($this, 'createModelQuery'),
            'findModelById'     => array($this, 'findModelById'),
            'createModel'       => array($this, 'createModel'),
            'saveModel'         => array($this, 'saveModel'),
            'deleteModel'       => array($this, 'deleteModel'),
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

    /**
     * Creates a model query.
     *
     * @param string $modelClass The model class.
     *
     * @return mixed The query.
     */
    abstract public function createModelQuery($modelClass);

    /**
     * Finds a model by id.
     *
     * @param string $modelClass The model class.
     * @param mixed  $id         The id.
     *
     * @return object|null The model or null if it does not exist.
     */
    abstract public function findModelById($modelClass, $id);

    /**
     * Creates a model.
     *
     * @param string $modelClass The model class.
     *
     * @return object The model.
     */
    abstract public function createModel($modelClass);

    /**
     * Saves a model.
     *
     * @param object $model The model.
     */
    abstract public function saveModel($model);

    /**
     * Deletes a model.
     *
     * @param object $model The model.
     */
    abstract public function deleteModel($model);
}
