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
use Pablodip\ModuleBundle\Field\Guesser\FieldGuessador;

/**
 * ModelExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModelExtension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'model';
    }

    /**
     * {@inheritdoc}
     */
    public function defineConfiguration()
    {
        $this->getModule()->addOptions(array(
            'modelClass'         => null,
            'modelFields'        => new OptionBag(),
            'modelFieldGuessers' => new OptionBag(),
        ));

        $this->getModule()->addCallbacks(array(
            'setModelFieldValue' => array($this, 'setModelFieldValue'),
            'getModelFieldValue' => array($this, 'getModelFieldValue'),
            'modelFromArray'     => array($this, 'modelFromArray'),
            'modelToArray'       => array($this, 'modelToArray'),
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
        if (null === $modelClass = $this->getModule()->getOption('modelClass')) {
            throw new \RuntimeException('The "modelClass" option is required.');
        }

        if (count($modelFieldGuessers = $this->getModule()->getOption('modelFieldGuessers'))) {
            $guessador = new FieldGuessador($modelFieldGuessers);
            foreach ($this->getModule()->getOption('modelFields') as $field) {
                $guessOptions = $guessador->guessOptions($modelClass, $field->getName());
                $field->setOptions(array_merge($guessOptions, $field->getOptions()));
            }
        }
    }

    /**
     * Sets a model field value by setter.
     *
     * @param object $model     The model.
     * @param string $fieldName The field name.
     * @param mixed  $value     The value.
     */
    public function setModelFieldValue($model, $fieldName, $value)
    {
        $model->{'set'.ucfirst($fieldName)}($value);
    }

    /**
     * Returns a model field value by getter.
     *
     * @param object $model     The model.
     * @param string $fieldName The field name.
     *
     * @return mixed The value.
     */
    public function getModelFieldValue($model, $fieldName)
    {
        return $model->{'get'.ucfirst($fieldName)}();
    }

    /**
     * Sets an array of field values to a model.
     *
     * The fields sets are the ones in the modelFields option.
     * It fails if there are extra fields.
     *
     * @param object $model The model.
     * @param array  $array The array.
     *
     * @return Boolean True if everything is ok, false if something fails.
     */
    public function modelFromArray($model, array $array)
    {
        // extra fields
        if (array_diff(array_keys($array), $this->getModule()->getOption('modelFields')->keys())) {
            return false;
        }

        foreach ($array as $name => $value) {
            $this->getModule()->call('setModelFieldValue', $model, $name, $value);
        }

        return true;
    }

    /**
     * Returns an array with the field values.
     *
     * @param object $model The model.
     *
     * @return array The array.
     */
    public function modelToArray($model)
    {
        $array = array();
        foreach ($this->getModule()->getOption('modelFields')->keys() as $fieldName) {
            $array[$fieldName] = $this->getModule()->call('getModelFieldValue', $model, $fieldName);
        }

        return $array;
    }
}
