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

/**
 * MandangoDataManagerExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoDataManagerExtension extends BaseDataManagerExtension
{
    /**
     * {@inheritdoc}
     */
    public function defineConfiguration()
    {
        $this->addOptions(array(
            'filterCriteriaCallbacks' => new \ArrayObject(),
        ));

        $this->addCallbacks(array(
            'filterCriteria' => array($this, 'filterCriteria'),
        ));
    }

    public function filterCriteria(array $criteria)
    {
        foreach ($this->getModule()->getModule('filterCriteriaCallbacks') as $callback) {
            $criteria = call_user_func($callback, $criteria);
        }

        return $criteria;
    }

    public function createQuery()
    {
        $query = $this->getModule()->getContainer()->get('mandango')->getRepository(
            $this->getModule()->getOption('dataClass')
        )->createQuery();

        // filter criteria
        $query->criteria($this->getModule()->call('filterCriteria', $criteria));

        return $query;
    }

    public function findDataById($id)
    {
        $query = $this->getModule()->call('createQuery');

        $id = $this->getModule()->getContainer()->get('mandango')->getRepository(
            $this->getModule()->getOption('dataClass')
        )->idToMongo($id);

        return $query->mergeCriteria(array('_id' => $id))->one();
    }

    public function createData()
    {
        $data = $this->getModule()->getContainer()->get('mandango')->create($this->getModule()->getOption('dataClass'));

        // after callbacks
        foreach ($this->getModule()->getOption('createDataAfterCallbacks') as $callback) {
            call_user_func($callback, $data);
        }

        return $data;
    }

    public function saveData($data)
    {
        // before callbacks
        foreach ($this->getModule()->getOption('saveDataBeforeCallbacks') as $callback) {
            call_user_func($callback, $data);
        }

        $data->save();
    }

    public function deleteData($data)
    {
        // before callbacks
        foreach ($this->getModule()->getOption('deleteDataBeforeCallbacks') as $callback) {
            call_user_func($callback, $data);
        }

        $data->delete();
    }
}
