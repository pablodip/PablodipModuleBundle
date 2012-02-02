<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle;

/**
 * OptionBag.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class OptionBag implements \Countable, \IteratorAggregate
{
    private $options;
    private $parser;

    /**
     * Constructor.
     *
     * @param array $options An array of options.
     * @param mixed $parser  A parser.
     */
    public function __construct(array $options = array(), $parser = null)
    {
        $this->options = array();
        $this->setParser($parser);
        $this->add($options);
    }

    /**
     * Sets the parser.
     *
     * The parser receives the name and value as arguments, and must
     * return an array with the parsed name and value.
     *
     * @param mixed $parser The parser.
     *
     * @throws \InvalidArgumentException If the parser is not a callback.
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
    }

    /**
     * Returns the parser.
     *
     * @return mixed The parser.
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Returns the options.
     *
     * @return array The options.
     */
    public function all()
    {
        return $this->options;
    }

    /**
     * Returns the option keys.
     *
     * @return array The option keys.
     */
    public function keys()
    {
        return array_keys($this->options);
    }

    /**
     * Sets an option.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     */
    public function set($name, $value)
    {
        if (null !== $this->parser) {
            list($name, $value) = call_user_func($this->parser, $name, $value);
        }

        $this->options[$name] = $value;
    }

    /**
     * Adds options.
     *
     * @param array $options An array of options.
     */
    public function add(array $options)
    {
        foreach ($options as $name => $value) {
            $this->set($name, $value);
        }
    }

    /**
     * Returns a option by name.
     *
     * @param string $name The name.
     *
     * @return mixed The option value.
     *
     * @throws \InvalidArgumentException If the option does not exist.
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        return $this->options[$name];
    }

    /**
     * Returns whether or not an option exists.
     *
     * @param string $name The name.
     *
     * @return Boolean Whether the option exists or not.
     */
    public function has($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Replaces the options.
     *
     * @param array $options An array of options.
     */
    public function replace(array $options)
    {
        $this->options = array();
        $this->add($options);
    }

    /**
     * Removes an option.
     *
     * @param string $name The name.
     */
    public function remove($name)
    {
        unset($this->options[$name]);
    }

    /**
     * Returns the number of options.
     *
     * Implements the countable interface.
     *
     * @return integer The number of options.
     */
    public function count()
    {
        return count($this->options);
    }

    /**
     * Implements the IteratorAggregate interface.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->options);
    }
}
