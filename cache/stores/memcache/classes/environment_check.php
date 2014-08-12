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
 * Validate that the current db structure matches the install.xml files.
 *
 * @package   core
 * @copyright 2014 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */

class cachestore_memcache_environment_check {
    public static function check_memcache_version(environment_results $result) {
        global $CFG;

        if (empty($CFG->version) or !get_config('cachestore_memcache', 'testservers')) {
            // Do not display warnings when plugin not configured,
            // admins will see the warning after setting memcache server.
            return null;
        }
        if (!extension_loaded('memcache') or !$version = phpversion('memcache')) {
            // No need to mention this when not used.
            return null;
        }
        $minversion = '3.0.3';
        $result->setInfo(get_string('memcacheversioncheck', 'cachestore_memcache'));
        if (version_compare($version, $minversion) < 0) {
            $result->setStatus(false);
            $result->setFeedbackStr(array('memcacheversionwarning', 'cachestore_memcache', array('minversion' => $minversion, 'version' => $version)));
        } else {
            $result->setStatus(true);
        }
        return $result;
    }
}
