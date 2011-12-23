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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Pablodip\ModuleBundle\Field\Field;
use Pablodip\ModuleBundle\Field\Guesser\FieldGuessador;
use Pablodip\ModuleBundle\Field\Guesser\FieldGuesserInterface;

/**
 * ModuleData.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class ModuleData extends Module implements ModuleDataInterface
{
    private $dataClass;
    private $fields;
    private $fieldGuessers;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container)
    {
        $this->fields = array();
        $this->fieldGuessers = array();

        parent::__construct($container);

        if (!$this->dataClass) {
            throw new \RuntimeException('A module data must have data class.');
        }

        if ($this->fieldGuessers) {
            $guessador = new FieldGuessador($this->fieldGuessers);
            foreach ($this->fields as $field) {
                $guessOptions = $guessador->guessOptions($this->dataClass, $field->getName());
                $field->setOptions(array_merge($guessOptions, $field->getOptions()));
            }
        }
    }

    /**
     * Sets the data class.
     *
     * @param string $dataClass The data class.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function setDataClass($dataClass)
    {
        $this->dataClass = $dataClass;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataClass()
    {
        return $this->dataClass;
    }

    /**
     * Adds a field.
     *
     * @param string $name   The name.
     * @param array  $option An array of options (optional).
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addField($name, array $options = array())
    {
        $this->fields[$name] = new Field($name, $options);

        return $this;
    }

    /**
     * Adds fields.
     *
     * You can define the fields in two ways:
     *
     *   * The name as the key and the options as the value.
     *   * The name as the value (without options).
     *
     * @param array $fields An array of fields.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $name => $options) {
            if (is_integer($name) && is_string($options)) {
                $name = $options;
                $options = array();
            }
            $this->addField($name, $options);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getField($name)
    {
        if (!$this->hasField($name)) {
            throw new \InvalidArgumentException(sprintf('The field "%s" does not exist.', $name));
        }

        return $this->fields[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return array_values($this->fields);
    }

    /**
     * Adds a field guesser.
     *
     * @param FieldGuesserInterface $fieldGuesser A field guesser.
     *
     * @return ModuleInterface The module (fluent interface).
     */
    public function addFieldGuesser(FieldGuesserInterface $fieldGuesser)
    {
        $this->fieldGuessers[] = $fieldGuesser;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldGuessers()
    {
        return $this->fieldGuessers;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataFieldValue($data, $fieldName, $value)
    {
        $data->{'set'.ucfirst($fieldName)}($value);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFieldValue($data, $fieldName)
    {
        return $data->{'get'.ucfirst($fieldName)}();
    }
}
