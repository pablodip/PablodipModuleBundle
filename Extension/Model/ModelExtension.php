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
}
