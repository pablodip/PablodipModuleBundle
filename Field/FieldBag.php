<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Field;

use Pablodip\ModuleBundle\OptionBag;

/**
 * FieldBag.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class FieldBag extends OptionBag
{
    /**
     * Constructor.
     *
     * @param array $fields An array of fields.
     */
    public function __construct(array $fields = array())
    {
        parent::__construct($fields, function ($key, $value) {
            if (is_int($key) && is_string($value)) {
                $key = $value;
                $value = array();
            }

            if (is_array($value)) {
                $value = new Field($key, $value);
            } elseif (!$value instanceof Field) {
                throw new \InvalidArgumentException('The field must be an instance of Field.');
            }

            return array($key, $value);
        });
    }
}
