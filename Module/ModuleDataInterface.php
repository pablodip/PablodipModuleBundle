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
 * ModuleDataInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface ModuleDataInterface extends ModuleInterface
{
    /**
     * Returns the data class.
     *
     * @return string The data class.
     */
    function getDataClass();

    /**
     * Returns whether a field exists or not.
     *
     * @param string $name The field name.
     *
     * @return Boolean Whether the field exists or not.
     */
    function hasField($name);

    /**
     * Returns a field.
     *
     * @param string $name The field name.
     *
     * @return Field The field.
     *
     * @throws \InvalidArgumentException If the field does not exist.
     */
    function getField($name);

    /**
     * Returns the fields.
     *
     * @return array An array of fields.
     */
    function getFields();

    /**
     * Returns the field guessers.
     *
     * @return array The field guessers.
     */
    function getFieldGuessers();

    /**
     * Returns a field value for a data.
     *
     * @param mixed  $data      The data.
     * @param string $fieldName The field name.
     *
     * @return The value.
     */
    function getDataFieldValue($data, $fieldName);
}
