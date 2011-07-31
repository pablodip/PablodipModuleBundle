<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Field\Guesser;

/**
 * FieldGuessador.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class FieldGuessador
{
    private $guessers;

    /**
     * Constructor.
     *
     * @param array $guessers An array of guessers.
     */
    public function __construct(array $guessers)
    {
        $this->guessers = array();
        foreach ($guessers as $guesser) {
            $this->add($guesser);
        }
    }

    /**
     * Add a guesser.
     */
    public function add(FieldGuesserInterface $guesser)
    {
        $this->guessers[] = $guesser;
    }

    /**
     * Returns all the guessers.
     */
    public function all()
    {
        return $this->guessers;
    }

    /**
     * Guesses option for a class and field.
     *
     * @param string $class     The class.
     * @param string $fieldName The field name.
     *
     * @return An array of options.
     */
    public function guessOptions($class, $fieldName)
    {
        $optionGuesses = array();
        foreach ($this->guessers as $guesser) {
            $guesses = $guesser->guessOptions($class, $fieldName);
            foreach ($guesses as $guess) {
                $optionGuesses[$guess->getOptionName()][] = $guess;
            }
        }

        $options = array();
        foreach ($optionGuesses as $name => $guesses) {
            $value = null;
            $confidence = 0;
            foreach ($guesses as $guess) {
                if ($guess->getConfidence() >= $confidence) {
                    $value = $guess->getOptionValue();
                    $confidence = $guess->getConfidence();
                }
            }
            $options[$name] = $value;
        }

        return $options;
    }
}
