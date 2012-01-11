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

use Pablodip\ModuleBundle\OptionBag;

/**
 * MandangoModelManagerExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoModelManagerExtension extends BaseModelManagerExtension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mandango_model_manager';
    }

    /**
     * {@inheritdoc}
     */
    public function defineConfiguration()
    {
        parent::defineConfiguration();

        $this->getModule()->addOptions(array(
            'filterMandangoCriteriaCallbacks' => new OptionBag(),
        ));

        $this->getModule()->addCallbacks(array(
            'filterMandangoCriteria' => array($this, 'filterMandangoCriteria'),
        ));
    }

    public function filterMandangoCriteria($modelClass, array $criteria)
    {
        foreach ($this->getModule()->getOption('filterMandangoCriteriaCallbacks') as $callback) {
            $criteria = call_user_func($callback, $modelClass, $criteria);
        }

        return $criteria;
    }

    public function createModelQuery($modelClass)
    {
        $query = $this->getModule()->getContainer()->get('mandango')->getRepository($modelClass)->createQuery();

        // filter criteria
        $query->criteria($this->getModule()->call('filterMandangoCriteria', $modelClass, $query->getCriteria()));

        return $query;
    }

    public function findModelById($modelClass, $id)
    {
        $query = $this->getModule()->call('createModelQuery', $modelClass);

        $id = $this->getModule()->getContainer()->get('mandango')->getRepository($modelClass)->idToMongo($id);

        return $query->mergeCriteria(array('_id' => $id))->one();
    }

    public function createModel($modelClass)
    {
        $model = $this->getModule()->getContainer()->get('mandango')->create($modelClass);

        // after callbacks
        foreach ($this->getModule()->getOption('createModelAfterCallbacks') as $callback) {
            call_user_func($callback, $model);
        }

        return $model;
    }

    public function saveModel($model)
    {
        // before callbacks
        foreach ($this->getModule()->getOption('saveModelBeforeCallbacks') as $callback) {
            call_user_func($callback, $model);
        }

        $model->save();
    }

    public function deleteModel($model)
    {
        // before callbacks
        foreach ($this->getModule()->getOption('deleteModelBeforeCallbacks') as $callback) {
            call_user_func($callback, $model);
        }

        $model->delete();
    }
}
