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

/**
 * ModuleDataView.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModuleDataView extends ModuleView
{
    /**
     * Constructor.
     *
     * @param ModuleDataInterface $module The module.
     */
    public function __construct(ModuleDataInterface $module)
    {
        parent::__construct($module);
    }

    /**
     * Returns a field value for a data.
     *
     * @param mixed  $data      The data.
     * @param string $fieldName The field name.
     *
     * @return The value.
     */
    public function getDataFieldValue($data, $fieldName)
    {
        return $this->getModule()->getDataFieldValue($data, $fieldName);
    }
}
