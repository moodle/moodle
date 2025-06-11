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
 * Steps definitions for behat theme.
 *
 * @package   theme_snap
 * @category  test
 * @copyright Copyright (c) 2018 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Check for color setup in categories
 *
 * @package   theme_snap
 * @category  test
 * @copyright Copyright (c) 2018 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap_category_colors extends behat_base {

    /**
     * Checks if classes are loaded to body on pages.
     *
     * @Given /^I check body for classes "(?P<classes_string>(?:[^"]|\\")*)"$/
     * @param string $classes classes separated by comma
     * @throws Exception
     */
    public function i_check_body_for_classes($classes) {
        $classesarray = explode(",", $classes);
        $xpath = '/body[';
        foreach ($classesarray as $key => $class) {
            $xpath .= 'contains(@class,"' . $class . '")';
            if ($key < count($classesarray) - 1) {
                $xpath .= " and ";
            }
        }
        $xpath .= "]";
        $this->find_all('xpath', $xpath);
    }

    /**
     * Checks if classes are loaded to body on pages.
     *
     * @Given /^I check element "(?P<element_string>(?:[^"]|\\")*)" with color "(?P<color_string>(?:[^"]|\\")*)"$/
     * @param string $element element to be checked
     * @param string $color hex color
     * @throws Exception
     */
    public function i_check_element_with_color($element, $color) {
        $session = $this->getSession();
        $elementcolor = $session->getDriver()->evaluateScript(
                'window.getComputedStyle(document.querySelectorAll("'
                . $element . '")[0], null).getPropertyValue("color");');
        $fromcolor = self::hex2rgb($color);
        $tocolor = self::rgb2array($elementcolor);

        if ($fromcolor !== $tocolor) {
            throw new Exception("Color " . $color . " was not found in element "
                    . $element . ", instead " . $elementcolor . " was found.");
        }
    }

    /**
     * Checks if css element have a property with input value.
     *
     * @codingStandardsIgnoreStart
     * @Given /^I check element "(?P<element_string>(?:[^"]|\\")*)" with property "(?P<property_string>(?:[^"]|\\")*)" = "(?P<value_string>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     * @param string $element element to be checked
     * @param string $property property to be checked
     * @param string $value value of the property
     * @throws Exception
     */
    public function i_check_element_with_property($element, $property, $value) {
        $session = $this->getSession();
        $elementvalue = $session->getDriver()->evaluateScript(
            'window.getComputedStyle(document.querySelectorAll("'
            . $element . '")[0], null).getPropertyValue("' . $property . '");');

        if (strpos($elementvalue, 'rgb') !== false) {
            $elementvalue = self::rgb2array($elementvalue);
            $value = self::hex2rgb($value);
        } else {
            $value = self::unit_converter($element, $value);
        }

        if ($elementvalue !== $value) {
            throw new Exception( $property . " with value " . (is_array($value) ? implode(",", $value) : $value)
                    . " was not found in element " . $element . ", instead "
                    . (is_array($elementvalue) ? implode(",", $elementvalue) : $elementvalue)
                    . " was found.");
        }
    }

    /**
     * Function to get a RGB array from a hex color (1, 3, or 6 digits).
     * @param string $color color in hex format
     * @return array|bool
     */
    private static function hex2rgb($color) {
        preg_match("/^#{0,1}([0-9a-f]{1,6})$/i", $color, $match);
        if (!isset($match[1])) {
            return false;
        }
        $hex = $match[1];
        if (strlen($match[1]) == 6) {
            list($r, $g, $b) = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
        } else if (strlen($match[1]) == 3) {
            list($r, $g, $b) = array($hex[0].$hex[0], $hex[1].$hex[1], $hex[2].$hex[2]);
        } else if (strlen($match[1]) == 2) {
            list($r, $g, $b) = array($hex[0].$hex[1], $hex[0].$hex[1], $hex[0].$hex[1]);
        } else if (strlen($match[1]) == 1) {
            list($r, $g, $b) = array($hex.$hex, $hex.$hex, $hex.$hex);
        } else {
            return false;
        }

        $color = array();
        $color['r'] = hexdec($r);
        $color['g'] = hexdec($g);
        $color['b'] = hexdec($b);

        return $color;
    }

    /**
     * Function to create a RGB array from string.
     * @param string $rgb rgb value in format rgb(R, G, B)
     * @return array|bool
     */
    private static function rgb2array($rgb) {
        if (strpos($rgb, 'rgba') !== false) {
            $pattern = '~^rgba?\((25[0-5]|2[0-4]\d|1\d{2}|\d\d?)\s*,\s*(25[0-5]|2[0-4]\d|1\d{2}|\d\d?)\s*,' .
            '\s*(25[0-5]|2[0-4]\d|1\d{2}|\d\d?)\s*(?:,\s*([01]\.?\d*?))?\)$~';
            preg_match($pattern, $rgb, $vals);
        } else {
            preg_match("/rgb\\((\\d{1,3}), (\\d{1,3}), (\\d{1,3})\\)/", $rgb, $vals);
        }
        if (!isset($vals[1])) {
            return false;
        }
        $color = array();
        $color['r'] = intval($vals[1]);
        $color['g'] = intval($vals[2]);
        $color['b'] = intval($vals[3]);

        return $color;
    }

    /**
     * Function to convert relative units to absolute units.
     *
     * @param string $element
     * @param string $value
     * @return string
     */
    private function unit_converter($element, $value) {
        $amount = floatval($value);
        $unit = explode($amount, $value)[1];
        if ($unit == 'em') { // Converts em to px.
            $session = $this->getSession();
            $fontsize = $session->getDriver()->evaluateScript(
                'window.getComputedStyle(document.querySelectorAll("'
                . $element . '")[0], null).getPropertyValue("font-size");'); // Return font size in px.
            $fontsize = floatval($fontsize);
            return ($amount * $fontsize).'px';
        }
        return $value;
    }
}
