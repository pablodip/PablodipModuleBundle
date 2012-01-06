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
use Pablodip\ModuleBundle\Field\Guesser\FieldGuessador;

/**
 * DataExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DataExtension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'data';
    }

    /**
     * {@inheritdoc}
     */
    public function defineConfiguration()
    {
        $this->getModule()->addOptions(array(
            'dataClass'         => null,
            'dataFields'        => array(),
            'dataFieldGuessers' => array(),
        ));

        $this->getModule()->addCallbacks(array(
            'setDataFieldValue' => array($this, 'setDataFieldValue'),
            'getDataFieldValue' => array($this, 'getDataFieldValue'),
            'dataFromArray'     => array($this, 'dataFromArray'),
            'dataToArray'       => array($this, 'dataToArray'),
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
        if (null === $this->getModule()->getOption('dataClass')) {
            throw new \RuntimeException('The dataClass option must have a value.');
        }

        if (count($fieldGuessers = $this->getModule()->getOption('dataFieldGuessers'))) {
            $guessador = new FieldGuessador($this->fieldGuessers);
            foreach ($this->fields as $field) {
                $guessOptions = $guessador->guessOptions($this->dataClass, $field->getName());
                $field->setOptions(array_merge($guessOptions, $field->getOptions()));
            }
        }
    }

    /**
     * Sets a data field value by setter.
     *
     * @param object $data      The data.
     * @param string $fieldName The field name.
     * @param mixed  $value     The value.
     */
    public function setDataFieldValue($data, $fieldName, $value)
    {
        $data->{'set'.ucfirst($fieldName)}($value);
    }

    /**
     * Returns a data field value by getter.
     *
     * @param object $data      The data.
     * @param string $fieldName The field name.
     *
     * @return mixed The value.
     */
    public function getDataFieldValue($data, $fieldName)
    {
        return $data->{'get'.ucfirst($fieldName)}();
    }

    /**
     * Sets an array of field values to a data.
     *
     * The fields sets are the ones in the dataFields option.
     * It fails if there are extra fields.
     *
     * @param object $data  The data.
     * @param array  $array The array.
     *
     * @return Boolean True if everything is ok, false if something fails.
     */
    public function dataFromArray($data, array $array)
    {
        // extra fields
        if (array_diff(array_keys($array), array_keys($this->getModule()->getOption('dataFields')))) {
            return false;
        }

        foreach ($array as $name => $value) {
            $this->getModule()->call('setDataFieldValue', $data, $name, $value);
        }

        return true;
    }

    /**
     * Returns an array with the field values.
     *
     * @param object $data The data.
     *
     * @return array The array.
     */
    public function dataToArray($data)
    {
        $array = array();
        foreach (array_keys($this->getModule()->getOption('dataFields')) as $fieldName) {
            $array[$fieldName] = $this->getModule()->call('getDataFieldValue', $data, $fieldName);
        }

        return $array;
    }
}
