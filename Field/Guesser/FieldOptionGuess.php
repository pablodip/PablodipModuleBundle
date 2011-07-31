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
 * FieldOptionGuess.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class FieldOptionGuess
{
    const HIGH_CONFIDENCE   = 2;
    const MEDIUM_CONFIDENCE = 1;
    const LOW_CONFIDENCE    = 0;

    private static $confidences = array(
        self::HIGH_CONFIDENCE,
        self::MEDIUM_CONFIDENCE,
        self::LOW_CONFIDENCE,
    );

    private $optionName;
    private $optionValue;
    private $confidence;

    /**
     * Constructor.
     *
     * @param string  $optionName  The option name.
     * @param mixed   $optionValue The option value.
     * @param integer $confidence  The confidence.
     */
    public function __construct($optionName, $optionValue, $confidence)
    {
        if (!in_array($confidence, self::$confidences, true)) {
            throw new \RuntimeException('The confidence is not valid.');
        }

        $this->optionName = $optionName;
        $this->optionValue = $optionValue;
        $this->confidence = $confidence;
    }

    /**
     * Returns the option name.
     *
     * @return string The option name.
     */
    public function getOptionName()
    {
        return $this->optionName;
    }

    /**
     * Returns the option value.
     *
     * @return mixed The option value.
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }

    /**
     * Returns the confidence.
     *
     * @return integer The confidence.
     */
    public function getConfidence()
    {
        return $this->confidence;
    }
}
