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

use Pablodip\ModuleBundle\Extension\ExtensionInterface;

/**
 * ManagerExtensionInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface DataManagerExtensionInterface extends ExtensionInterface
{
    /**
     * Creates a query.
     */
    function createQuery();

    /**
     * Finds a data by id.
     */
    function findDataById($id);

    /**
     * Creates a data.
     */
    function createData();

    /**
     * Saves a data.
     */
    function saveData($data);

    /**
     * Deletes a data.
     */
    function deleteData($data);
}
