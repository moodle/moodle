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
 * Base test case class.
 *
 * @package    core
 * @category   test
 * @author     Tony Levi <tony.levi@blackboard.com>
 * @copyright  2015 Blackboard (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Base class for PHPUnit test cases customised for Moodle
 *
 * It is intended for functionality common to both basic and advanced_testcase.
 *
 * @package    core
 * @category   test
 * @author     Tony Levi <tony.levi@blackboard.com>
 * @copyright  2015 Blackboard (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_testcase extends PHPUnit_Framework_TestCase {
    /**
     * Note: we are overriding this method to remove the deprecated error
     * @see https://tracker.moodle.org/browse/MDL-47129
     *
     * @param  array   $matcher
     * @param  string  $actual
     * @param  string  $message
     * @param  boolean $ishtml
     *
     * @deprecated 3.0
     */
    public static function assertTag($matcher, $actual, $message = '', $ishtml = true) {
        $dom = PHPUnit_Util_XML::load($actual, $ishtml);
        $tags = PHPUnit_Util_XML::findNodes($dom, $matcher, $ishtml);
        $matched = count($tags) > 0 && $tags[0] instanceof DOMNode;
        self::assertTrue($matched, $message);
    }

    /**
     * Note: we are overriding this method to remove the deprecated error
     * @see https://tracker.moodle.org/browse/MDL-47129
     *
     * @param  array   $matcher
     * @param  string  $actual
     * @param  string  $message
     * @param  boolean $ishtml
     *
     * @deprecated 3.0
     */
    public static function assertNotTag($matcher, $actual, $message = '', $ishtml = true) {
        $dom = PHPUnit_Util_XML::load($actual, $ishtml);
        $tags = PHPUnit_Util_XML::findNodes($dom, $matcher, $ishtml);
        $matched = count($tags) > 0 && $tags[0] instanceof DOMNode;
        self::assertFalse($matched, $message);
    }
}
