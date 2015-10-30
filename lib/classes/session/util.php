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
 * Shared utility functions for session handlers.
 *
 * This contains functions that are shared between two or more handlers.
 *
 * @package core
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\session;

defined('MOODLE_INTERNAL') || die();

/**
 * Shared utility functions for session handlers.
 *
 * This contains functions that are shared between two or more handlers.
 *
 * @package core
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class util {
    /**
     * Convert a connection string to an array of servers
     *
     * EG: Converts: "abc:123, xyz:789" to
     *
     *  array(
     *      array('abc', '123'),
     *      array('xyz', '789'),
     *  )
     *
     * @copyright  2013 Moodlerooms Inc. (http://www.moodlerooms.com)
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     * @author     Mark Nielsen
     *
     * @param string $str save_path value containing memcached connection string
     * @return array
     */
    public static function connection_string_to_memcache_servers($str) {
        $servers = array();
        $parts   = explode(',', $str);
        foreach ($parts as $part) {
            $part = trim($part);
            $pos  = strrpos($part, ':');
            if ($pos !== false) {
                $host = substr($part, 0, $pos);
                $port = substr($part, ($pos + 1));
            } else {
                $host = $part;
                $port = 11211;
            }
            $servers[] = array($host, $port);
        }
        return $servers;
    }
}
