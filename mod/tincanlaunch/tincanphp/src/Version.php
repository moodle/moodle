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
 * TinCan API Version
 *
 * @internal implemented similar to a type-safe enum
 * @package  TinCan
 */
final class Version
{
    /**#@+
     * Value constants
     *
     * @var string
     */
    const V103 = "1.0.3";
    const V102 = "1.0.2";
    const V101 = "1.0.1";
    const V100 = "1.0.0";
    const V095 = "0.95";
    /**#@- */

    /** @var array string => bool */
    private static $supported = [
        self::V103 => true,
        self::V102 => true,
        self::V101 => true,
        self::V100 => true,
        self::V095 => false
    ];

    /** @var self[] */
    private static $instances = [];

    /** @var string */
    private $value;

    /**
     * Constructor
     *
     * @param  string $aValue a version value
     * @throws InvalidArgumentException when the value is not recognized
     */
    private function __construct($aValue) {
        if (!isset(static::$supported[$aValue])) {
            throw new InvalidArgumentException("Invalid version [$aValue]");
        }
        $this->value = $aValue;
    }

    /**
     * Does the value match?
     *
     * @param  string $aValue a value to check
     * @return bool
     */
    public function hasValue($aValue) {
        return $this->value === (string) $aValue;
    }

    /**
     * Is the value contained in a list of versions?
     *
     * @param  string[] $aValueList a list of values to check
     * @return bool
     */
    public function hasAnyValue(array $aValueList) {
        return in_array($this->value, $aValueList);
    }

    /**
     * Is this the latest version?
     *
     * @return bool
     */
    public function isLatest() {
        return $this->value === static::latest();
    }

    /**
     * Is this a supported version?
     *
     * @return bool
     */
    public function isSupported() {
        return static::$supported[$this->value];
    }

    /**
     * Convert the object to a string
     *
     * @return string
     */
    public function __toString() {
        return $this->value;
    }

    /**
     * Factory constructor
     *
     * @example $version = Version::V101();
     * @param   string $aValue    the called method as a version value
     * @param   array  $arguments unused arguments passed to the method
     * @return  self
     */
    public static function __callStatic($aValue, array $arguments = []) {
        $aValue = trim(preg_replace("#v(\d)(95|\d)(\d)?#i", '$1.$2.$3', $aValue), ".");
        if (!isset(static::$instances[$aValue])) {
            static::$instances[$aValue] = new static($aValue);
        }
        return static::$instances[$aValue];
    }

    /**
     * Convert a string into a Version instance
     *
     * @param  string $aValue a version value
     * @return self
     */
    public static function fromString($aValue) {
        $aValue = str_replace(".", "", $aValue);
        return static::{"V$aValue"}();
    }

    /**
     * List all supported versions
     *
     * @return string[]
     */
    public static function supported() {
        return array_keys(array_filter(static::$supported, function($supported) {
            return $supported === true;
        }));
    }

    /**
     * Retrieve the most recent version
     *
     * @return string
     */
    public static function latest() {
        return array_keys(static::$supported)[0];
    }
}
