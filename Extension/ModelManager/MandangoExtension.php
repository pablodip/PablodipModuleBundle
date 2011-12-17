<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Extension\ModelManager;

use Pablodip\ModuleBundle\Extension\ExtensionInterface;
use Pablodip\ModuleBundle\Module\ModuleInterface;
use Pablodip\ModuleBundle\Module\ModuleDataInterface;

/**
 * Useful function to use mandango in a ModuleData.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(ModuleInterface $module)
    {
        if (!$module instanceof ModuleDataInterface) {
            throw new \InvalidArgumentException('The module must be an instance of ModuleDataInterface');
        }

        $options = array();

        /*
         * Callbacks to filter the criteria.
         */
        $options['filter_criteria_callbacks'] = new \ArrayObject();

        /*
         * Closure to create a query to query the data class repository.
         */
        $options['create_query_closure'] = function () use ($module) {
            $query = $module->getContainer()->get('mandango')->getRepository($module->getDataClass())->createQuery();

            // filter criteria
            foreach ($module->getOption('filter_criteria_callbacks') as $callback) {
                $query->criteria(call_user_func($callback, $query->getCriteria(), $module));
            }

            return $query;
        };

        /*
         * Callbacks to call after creating a data.
         */
        $options['create_data_after_callbacks'] = new \ArrayObject();

        /*
         * Closure to create a data.
         */
        $options['create_data_closure'] = function () use ($module) {
            $data = $module->getContainer()->get('mandango')->create($module->getDataClass());

            // after callbacks
            foreach ($module->getOption('create_data_after_callbacks') as $callback) {
                call_user_func($callback, $data, $module);
            }

            return $data;
        };

        /*
         * Closure to save a data.
         */
        $options['save_data_closure'] = function ($data) {
            $data->save();
        };

        /*
         * Closure to find a data by id.
         */
        $options['find_data_by_id'] = function ($id) use ($module) {
            $closure = $module->getOption('create_query_closure');
            $query = $closure();

            $id = $module->getContainer()->get('mandango')->getRepository($module->getDataClass())->idToMongo($id);

            return $query->mergeCriteria(array('_id' => $id))->one();
        };

        /*
         * Closure to delete a data.
         */
        $options['delete_data_closure'] = function ($data) {
            $data->delete();
        };

        $module->addOptions($options);
    }
}
