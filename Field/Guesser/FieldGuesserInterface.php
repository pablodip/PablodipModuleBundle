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
 * FieldGuesserInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface FieldGuesserInterface
{
    /**
     * Guess options for a class and field name.
     *
     * @param string $class     The class.
     * @param string $fieldName The field name.
     *
     * @return array An array of guesses.
     */
    function guessOptions($class, $fieldName);
}
