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
 * Contains an abstract base class definition for curl security helpers.
 *
 * @package   core
 * @copyright 2016 Jake Dallimore
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jake Dallimore <jrhdallimore@gmail.com>
 */

namespace core\files;

defined('MOODLE_INTERNAL') || exit();

/**
 * Security helper for the curl class.
 *
 * This class is intended as a base class for all curl security helpers. A curl security helper should provide a means to check
 * a URL to determine whether curl should be allowed to request its content. It must also be able to return a simple string to
 * explain that the URL is blocked, e.g. 'This URL is blocked'.
 *
 * Curl security helpers are currently used by the 'curl' wrapper class in lib/filelib.php.
 *
 * This class depends on:
 *  - nothing.
 *
 * @package   core
 * @copyright 2016 Jake Dallimore
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jake Dallimore <jrhdallimore@gmail.com>
 */
abstract class curl_security_helper_base {

    /**
     * Check whether the input url should be blocked or not.
     *
     * @param string $url the url to check.
     * @return bool true if the url is deemed to be blocked, false otherwise.
     */
    abstract public function url_is_blocked($url);

    /**
     * Returns a string, explaining that a URL is blocked.
     *
     * @return string the lang string indicating that the url has been blocked.
     */
    abstract public function get_blocked_url_string();
}