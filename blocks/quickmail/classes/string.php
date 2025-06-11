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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_quickmail_string {

    /**
     * Shortcut for get_string() for this plugin's lang strings
     *
     * @param  string $key
     * @param  mixed $a  optional attributes
     * @return string
     */
    public static function get($key, $a = null) {
        return self::get_block_string($key, $a);
    }

    /**
     * Returns a lang string for this plugin
     *
     * @param  string $key
     * @param  mixed $a  optional attributes
     * @return string
     */
    private static function get_block_string($key, $a = null) {
        return get_string($key, \block_quickmail_plugin::$name, $a);
    }

}
