<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines string apis
 *
 * @package   core
 * @copyright 2011 Sam Hemelryk
 *            2012 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * A collator class with static methods that can be used for sorting.
 *
 * @package   core
 * @copyright 2011 Sam Hemelryk
 *            2012 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_collator {
    /** @const compare items using general PHP comparison, equivalent to Collator::SORT_REGULAR, this may bot be locale aware! */
    const SORT_REGULAR = 0;

    /** @const compare items as strings, equivalent to Collator::SORT_STRING */
    const SORT_STRING = 1;

    /** @const compare items as numbers, equivalent to Collator::SORT_NUMERIC */
    const SORT_NUMERIC = 2;

    /** @const compare items like natsort(), equivalent to SORT_NATURAL */
    const SORT_NATURAL = 6;

    /** @const do not ignore case when sorting, use bitwise "|" with SORT_NATURAL or SORT_STRING, equivalent to Collator::UPPER_FIRST */
    const CASE_SENSITIVE = 64;

    /** @var Collator|false|null **/
    protected static $collator = null;

    /** @var string|null The locale that was used in instantiating the current collator **/
    protected static $locale = null;

    /**
     * Prevent class instances, all methods are static.
     */
    private function __construct() {
    }

    /**
     * Ensures that a collator is available and created
     *
     * @return bool Returns true if collation is available and ready
     */
    protected static function ensure_collator_available() {
        $locale = get_string('locale', 'langconfig');
        if (is_null(self::$collator) || $locale != self::$locale) {
            self::$collator = false;
            self::$locale = $locale;
            if (class_exists('Collator', false)) {
                $collator = new Collator($locale);
                if (!empty($collator) && $collator instanceof Collator) {
                    // Check for non fatal error messages. This has to be done immediately
                    // after instantiation as any further calls to collation will cause
                    // it to reset to 0 again (or another error code if one occurred)
                    $errorcode = $collator->getErrorCode();
                    $errormessage = $collator->getErrorMessage();
                    // Check for an error code, 0 means no error occurred
                    if ($errorcode !== 0) {
                        // Get the actual locale being used, e.g. en, he, zh
                        $localeinuse = $collator->getLocale(Locale::ACTUAL_LOCALE);
                        // Check for the common fallback warning error codes. If any of the two
                        // following errors occurred, there is normally little to worry about:
                        // * U_USING_FALLBACK_WARNING (-128) indicates that a fall back locale was
                        //   used. For example, 'de_CH' was requested, but nothing was found
                        //   there, so 'de' was used.
                        // * U_USING_DEFAULT_WARNING (-127) indicates that the default locale
                        //   data was used; neither the requested locale nor any of its fall
                        //   back locales could be found. For example, 'pt' was requested, but
                        //   UCA was used (Unicode Collation Algorithm http://unicode.org/reports/tr10/).
                        // See http://www.icu-project.org/apiref/icu4c/classicu_1_1ResourceBundle.html
                        if ($errorcode === -127 || $errorcode === -128) {
                            // Check if the locale in use is UCA default one ('root') or
                            // if it is anything like the locale we asked for
                            if ($localeinuse !== 'root' && strpos($locale, $localeinuse) !== 0) {
                                // The locale we asked for is completely different to the locale
                                // we have received, let the user know via debugging
                                debugging('Locale warning (not fatal) '.$errormessage.': '.
                                    'Requested locale "'.$locale.'" not found, locale "'.$localeinuse.'" used instead. '.
                                    'The most specific locale supported by ICU relatively to the requested locale is "'.
                                    $collator->getLocale(Locale::VALID_LOCALE).'".');
                            } else {
                                // Nothing to do here, this is expected!
                                // The Moodle locale setting isn't what the collator expected but
                                // it is smart enough to match the first characters of our locale
                                // to find the correct locale or to use UCA collation
                            }
                        } else {
                            // We've received some other sort of non fatal warning - let the
                            // user know about it via debugging.
                            debugging('Problem with locale: '.$errormessage.'. '.
                                'Requested locale: "'.$locale.'", actual locale "'.$localeinuse.'". '.
                                'The most specific locale supported by ICU relatively to the requested locale is "'.
                                $collator->getLocale(Locale::VALID_LOCALE).'".');
                        }
                    }
                    // Store the collator object now that we can be sure it is in a workable condition
                    self::$collator = $collator;
                } else {
                    // Fatal error while trying to instantiate the collator... something went wrong
                    debugging('Error instantiating collator for locale: "' . $locale . '", with error [' .
                    intl_get_error_code() . '] ' . intl_get_error_message($collator));
                }
            }
        }
        return (self::$collator instanceof Collator);
    }

    /**
     * Restore array contents keeping new keys.
     * @static
     * @param array $arr
     * @param array $original
     * @return void modifies $arr
     */
    protected static function restore_array(array &$arr, array &$original) {
        foreach ($arr as $key => $ignored) {
            $arr[$key] = $original[$key];
        }
    }

    /**
     * Normalise numbers in strings for natural sorting comparisons.
     * @static
     * @param string $string
     * @return string string with normalised numbers
     */
    protected static function naturalise($string) {
        return preg_replace_callback('/[0-9]+/', array('core_collator', 'callback_naturalise'), $string);
    }

    /**
     * @internal
     * @static
     * @param array $matches
     * @return string
     */
    public static function callback_naturalise($matches) {
        return str_pad($matches[0], 20, '0', STR_PAD_LEFT);
    }

    /**
     * Locale aware sorting, the key associations are kept, values are sorted alphabetically.
     *
     * @param array $arr array to be sorted (reference)
     * @param int $sortflag One of core_collator::SORT_NUMERIC, core_collator::SORT_STRING, core_collator::SORT_NATURAL, core_collator::SORT_REGULAR
     *      optionally "|" core_collator::CASE_SENSITIVE
     * @return bool True on success
     */
    public static function asort(array &$arr, $sortflag = core_collator::SORT_STRING) {
        if (empty($arr)) {
            // nothing to do
            return true;
        }

        $original = null;

        $casesensitive = (bool)($sortflag & core_collator::CASE_SENSITIVE);
        $sortflag = ($sortflag & ~core_collator::CASE_SENSITIVE);
        if ($sortflag != core_collator::SORT_NATURAL and $sortflag != core_collator::SORT_STRING) {
            $casesensitive = false;
        }

        if (self::ensure_collator_available()) {
            if ($sortflag == core_collator::SORT_NUMERIC) {
                $flag = Collator::SORT_NUMERIC;

            } else if ($sortflag == core_collator::SORT_REGULAR) {
                $flag = Collator::SORT_REGULAR;

            } else {
                $flag = Collator::SORT_STRING;
            }

            if ($sortflag == core_collator::SORT_NATURAL) {
                $original = $arr;
                if ($sortflag == core_collator::SORT_NATURAL) {
                    foreach ($arr as $key => $value) {
                        $arr[$key] = self::naturalise((string)$value);
                    }
                }
            }
            if ($casesensitive) {
                self::$collator->setAttribute(Collator::CASE_FIRST, Collator::UPPER_FIRST);
            } else {
                self::$collator->setAttribute(Collator::CASE_FIRST, Collator::OFF);
            }
            $result = self::$collator->asort($arr, $flag);
            if ($original) {
                self::restore_array($arr, $original);
            }
            return $result;
        }

        // try some fallback that works at least for English

        if ($sortflag == core_collator::SORT_NUMERIC) {
            return asort($arr, SORT_NUMERIC);

        } else if ($sortflag == core_collator::SORT_REGULAR) {
            return asort($arr, SORT_REGULAR);
        }

        if (!$casesensitive) {
            $original = $arr;
            foreach ($arr as $key => $value) {
                $arr[$key] = core_text::strtolower($value);
            }
        }

        if ($sortflag == core_collator::SORT_NATURAL) {
            $result = natsort($arr);

        } else {
            $result = asort($arr, SORT_LOCALE_STRING);
        }

        if ($original) {
            self::restore_array($arr, $original);
        }

        return $result;
    }

    /**
     * Locale aware sort of objects by a property in common to all objects
     *
     * @param array $objects An array of objects to sort (handled by reference)
     * @param string $property The property to use for comparison
     * @param int $sortflag One of core_collator::SORT_NUMERIC, core_collator::SORT_STRING, core_collator::SORT_NATURAL, core_collator::SORT_REGULAR
     *      optionally "|" core_collator::CASE_SENSITIVE
     * @return bool True on success
     */
    public static function asort_objects_by_property(array &$objects, $property, $sortflag = core_collator::SORT_STRING) {
        $original = $objects;
        foreach ($objects as $key => $object) {
            $objects[$key] = $object->$property;
        }
        $result = self::asort($objects, $sortflag);
        self::restore_array($objects, $original);
        return $result;
    }

    /**
     * Locale aware sort of objects by a method in common to all objects
     *
     * @param array $objects An array of objects to sort (handled by reference)
     * @param string $method The method to call to generate a value for comparison
     * @param int $sortflag One of core_collator::SORT_NUMERIC, core_collator::SORT_STRING, core_collator::SORT_NATURAL, core_collator::SORT_REGULAR
     *      optionally "|" core_collator::CASE_SENSITIVE
     * @return bool True on success
     */
    public static function asort_objects_by_method(array &$objects, $method, $sortflag = core_collator::SORT_STRING) {
        $original = $objects;
        foreach ($objects as $key => $object) {
            $objects[$key] = $object->{$method}();
        }
        $result = self::asort($objects, $sortflag);
        self::restore_array($objects, $original);
        return $result;
    }

    /**
     * Locale aware sort of array of arrays.
     *
     * Given an array like:
     * $array = array(
     *     array('name' => 'bravo'),
     *     array('name' => 'charlie'),
     *     array('name' => 'alpha')
     * );
     *
     * If you call:
     * core_collator::asort_array_of_arrays_by_key($array, 'name')
     *
     * You will be returned $array sorted by the name key of the subarrays. e.g.
     * $array = array(
     *     array('name' => 'alpha'),
     *     array('name' => 'bravo'),
     *     array('name' => 'charlie')
     * );
     *
     * @param array $array An array of objects to sort (handled by reference)
     * @param string $key The key to use for comparison
     * @param int $sortflag One of
     *          core_collator::SORT_NUMERIC,
     *          core_collator::SORT_STRING,
     *          core_collator::SORT_NATURAL,
     *          core_collator::SORT_REGULAR
     *      optionally "|" core_collator::CASE_SENSITIVE
     * @return bool True on success
     */
    public static function asort_array_of_arrays_by_key(array &$array, $key, $sortflag = core_collator::SORT_STRING) {
        $original = $array;
        foreach ($array as $initkey => $item) {
            $array[$initkey] = $item[$key];
        }
        $result = self::asort($array, $sortflag);
        self::restore_array($array, $original);
        return $result;
    }

    /**
     * Locale aware sorting, the key associations are kept, keys are sorted alphabetically.
     *
     * @param array $arr array to be sorted (reference)
     * @param int $sortflag One of core_collator::SORT_NUMERIC, core_collator::SORT_STRING, core_collator::SORT_NATURAL, core_collator::SORT_REGULAR
     *      optionally "|" core_collator::CASE_SENSITIVE
     * @return bool True on success
     */
    public static function ksort(array &$arr, $sortflag = core_collator::SORT_STRING) {
        $keys = array_keys($arr);
        if (!self::asort($keys, $sortflag)) {
            return false;
        }
        // This is a bit slow, but we need to keep the references
        $original = $arr;
        $arr = array(); // Surprisingly this does not break references outside
        foreach ($keys as $key) {
            $arr[$key] = $original[$key];
        }

        return true;
    }
}
