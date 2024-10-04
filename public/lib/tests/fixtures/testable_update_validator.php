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
 * Provides testable_core_update_validator class.
 *
 * @package     core_plugin
 * @category    test
 * @copyright   2013, 2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Provides access to protected methods we want to explicitly test
 *
 * @copyright 2013, 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_core_update_validator extends \core\update\validator {

    public function testable_parse_version_php($fullpath) {
        return parent::parse_version_php($fullpath);
    }

    public function get_plugintype_location($plugintype) {

        $testableroot = make_temp_directory('testable_core_update_validator/plugintypes');
        if (!file_exists($testableroot.'/'.$plugintype)) {
            make_temp_directory('testable_core_update_validator/plugintypes/'.$plugintype);
        }

        return $testableroot.'/'.$plugintype;
    }
}
