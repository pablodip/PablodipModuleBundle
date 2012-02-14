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
use Pablodip\ModuleBundle\Field\FieldBag;
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
            'model_class'          => null,
            'model_fields'         => new FieldBag(),
            'model_field_guessers' => new OptionBag(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function parseConfiguration()
    {
        if (null === $modelClass = $this->getModule()->getOption('model_class')) {
            throw new \RuntimeException('The "model_class" option is required.');
        }

        if (count($modelFieldGuessers = $this->getModule()->getOption('model_field_guessers'))) {
            $guessador = new FieldGuessador($modelFieldGuessers->all());
            foreach ($this->getModule()->getOption('model_fields') as $field) {
                $guessOptions = $guessador->guessOptions($modelClass, $field->getName());
                $field->setOptions(array_merge($guessOptions, $field->getOptions()));
            }
        }
    }

    /**
     * Filters the global fields.
     *
     * @param FieldBag $fields A field bag.
     *
     * @return FieldBag The fields filtered.
     */
    public function filterFields(FieldBag $fields)
    {
        $allFields = $this->getModule()->getOption('model_fields');

        $filteredFields = new FieldBag();
        foreach ($fields as $name => $field) {
            if (!$allFields->has($name)) {
                throw new \RuntimeException(sprintf('The field "%s" does not exist.', $name));
            }

            $filteredField = clone $field;
            foreach ($allFields->get($name)->getOptions() as $optionName => $optionValue) {
                if (!$filteredField->hasOption($optionName)) {
                    $filteredField->setOption($optionName, $optionValue);
                }
            }

            $filteredFields->set($name, $filteredField);
        }

        return $filteredFields;
    }
}
