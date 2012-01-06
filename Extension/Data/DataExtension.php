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

    public function setDataFieldValue($data, $fieldName, $value)
    {
        $data->{'set'.ucfirst($fieldName)}($value);
    }

    public function getDataFieldValue($data, $fieldName)
    {
        return $data->{'get'.ucfirst($fieldName)}();
    }
}
