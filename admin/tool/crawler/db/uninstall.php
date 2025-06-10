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
 * Link checker robot plugin uninstall script.
 *
 * @package    tool_crawler
 * @copyright  2019 Nicolas Roeser
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Hook called just before the plugin is uninstalled. Removes the cookie jar which had been used by the plugin if it exists.
 *
 * @return bool Whether the function was successful.
 */
function xmldb_tool_crawler_uninstall() {
    global $CFG;

    $cookiefile = $CFG->dataroot . '/tool_crawler_cookies.txt';
    if (file_exists($cookiefile)) {
        @unlink($cookiefile);
    }

    return true;
}
