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
 * FieldGuesserFactory.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class FieldGuesserFactory
{
    private $fieldGuessers;

    /**
     * Constructor.
     *
     * @param array $fieldGuessers An array of field guessers (optional).
     */
    public function __construct(array $fieldGuessers = array())
    {
        $this->fieldGuessers = array();
        $this->addFieldGuessers($fieldGuessers);
    }

    /**
     * Adds a field guesser.
     *
     * @param string                $name         The name.
     * @param FieldGuesserInterface $fieldGuesser The field guesser.
     */
    public function add($name, FieldGuesserInterface $fieldGuesser)
    {
        $this->fieldGuessers[$name] = $fieldGuesser;
    }

    /**
     * Adds an array of field guessers.
     *
     * @param array $fieldGuessers An array of field guessers.
     */
    public function addFieldGuessers(array $fieldGuessers)
    {
        foreach ($fieldGuessers as $name => $fieldGuesser) {
            $this->add($name, $fieldGuesser);
        }
    }

    /**
     * Returns if a field guesser exists.
     *
     * @param string $name The name.
     *
     * @return Boolean If the field guesser exists.
     */
    public function has($name)
    {
        return isset($this->fieldGuessers[$name]);
    }

    /**
     * Returns a field guesser by name.
     *
     * @param string $name The name.
     *
     * @return FieldGuesserInterface The field guesser.
     *
     * @throws \InvalidArgumentException If the field guesser does not exist.
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The field guesser "%s" does not exist.', $name));
        }

        return $this->fieldGuessers[$name];
    }

    /**
     * Returns the field guessers.
     *
     * @return array The field guessers.
     */
    public function all()
    {
        return $this->guessers;
    }
}
