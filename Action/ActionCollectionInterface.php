<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Action;

/**
 * ActionCollectionInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface ActionCollectionInterface
{
    /**
     * Returns the namespace.
     *
     * @return string The namespace.
     */
    function getNamespace();

    /**
     * Returns the name.
     *
     * @return string The name.
     */
    function getName();

    /**
     * Returns the full name (namespace + name).
     *
     * @return string The full name.
     */
    function getFullName();

    /**
     * Returns where an option exists or not.
     *
     * @param string $name The name.
     *
     * @return Boolean Where the option exists or not.
     */
    function hasOption($name);

    /**
     * Returns an option value.
     *
     * @param string $name The name.
     *
     * @return mixed The value.
     *
     * @throws \InvalidArgumentException If the option does not exist.
     */
    function getOption($name);

    /**
     * Returns the options.
     *
     * @return array The options.
     */
    function getOptions();

    /**
     * Returns the actions.
     *
     * @return array The actions.
     */
    function getActions();
}
