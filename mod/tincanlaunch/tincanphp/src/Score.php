<?php
/*
    Copyright 2014 Rustici Software

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

namespace TinCan;

use InvalidArgumentException;

/**
 * An optional field that represents the outcome of a graded Activity achieved
 * by an actor.
 */
class Score implements VersionableInterface, ComparableInterface
{
    use ArraySetterTrait, FromJSONTrait, AsVersionTrait, SignatureComparisonTrait;

    /**#@+
     * Class constants
     *
     * @var int
     */
    const SCALE_MIN         = -1;
    const SCALE_MAX         = 1;
    /**#@- */

    /**
     * Decimal number between -1 and 1, inclusive
     *
     * @var float
     */
    protected $scaled;

    /**
     * Decimal number between min and max (if present, otherwise unrestricted),
     * inclusive
     *
     * @var float
     */
    protected $raw;

    /**
     * Decimal number less than max (if present)
     *
     * @var float
     */
    protected $min;

    /**
     * Decimal number greater than min (if present)
     *
     * @var float
     */
    protected $max;

    /**
     * Constructor
     *
     * @param float|array $aRawValue      the raw score value, may also be an array of properties
     * @param float       $aMin           the score minimum
     * @param float       $aMax           the score maximum
     * @param float       $aScaledValue   the scaled score
     */
    public function __construct($aRawValue = null, $aMin = null, $aMax = null, $aScaledValue = null) {
        if (!is_array($aRawValue)) {
            $aRawValue = [
                'raw'    => $aRawValue,
                'min'    => $aMin,
                'max'    => $aMax,
                'scaled' => $aScaledValue
            ];
        }
        $this->_fromArray($aRawValue);
    }

    /**
     * @param  float $value
     * @throws InvalidArgumentException
     * @return self
     */
    public function setScaled($value) {
        if ($value < static::SCALE_MIN) {
            throw new InvalidArgumentException(
                sprintf( "Value must be greater than or equal to %s [%s]", static::SCALE_MIN, $value)
            );
        }
        if ($value > static::SCALE_MAX) {
            throw new InvalidArgumentException(
                sprintf( "Value must be less than or equal to %s [%s]", static::SCALE_MAX, $value)
            );
        }
        $this->scaled = (float) $value;
        return $this;
    }

    /**
     * @return null|float
     */
    public function getScaled() {
        return $this->scaled;
    }

    /**
     * @param  float $value
     * @throws InvalidArgumentException
     * @return self
     */
    public function setRaw($value) {
        if (isset($this->min) && $value < $this->min) {
            throw new InvalidArgumentException(
                sprintf("Value must be greater than or equal to 'min' (%s) [%s]", $this->min, $value)
            );
        }
        if (isset($this->max) && $value > $this->max) {
            throw new InvalidArgumentException(
                sprintf("Value must be less than or equal to 'max' (%s) [%s]", $this->max, $value)
            );
        }

        $this->raw = (float) $value;
        return $this;
    }

    /**
     * @return null|float
     */
    public function getRaw() {
        return $this->raw;
    }

    /**
     * @param  float $value
     * @throws InvalidArgumentException
     * @return self
     */
    public function setMin($value) {
        if (isset($this->raw) && $value > $this->raw) {
            throw new InvalidArgumentException(
                sprintf("Value must be less than or equal to 'raw' (%s) [%s]", $this->raw, $value)
            );
        }
        if (isset($this->max) && $value >= $this->max) {
            throw new InvalidArgumentException(
                sprintf("Value must be less than 'max' (%s) [%s]", $this->max, $value)
            );
        }
        $this->min = (float) $value;
        return $this;
    }

    /**
     * @return null|float
     */
    public function getMin() {
        return $this->min;
    }

    /**
     * @param  float $value
     * @throws InvalidArgumentException
     * @return self
     */
    public function setMax($value) {
        if (isset($this->raw) && $value < $this->raw) {
            throw new InvalidArgumentException(
                sprintf("Value must be greater than or equal to 'raw' (%s) [%s]", $this->raw, $value)
            );
        }
        if (isset($this->min) && $value <= $this->min) {
            throw new InvalidArgumentException(
                sprintf("Value must be greater than 'min' (%s) [%s]", $this->min, $value)
            );
        }
        $this->max = (float) $value;
        return $this;
    }

    /**
     * @return null|float
     */
    public function getMax() {
        return $this->max;
    }
}
