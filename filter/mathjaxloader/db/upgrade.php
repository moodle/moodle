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
 * MathJAX filter upgrade code.
 *
 * @package    filter_mathjaxloader
 * @copyright  2014 Damyon Wiese (damyon@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_filter_mathjaxloader_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014081100) {

        $sslcdnurl = get_config('filter_mathjaxloader', 'httpsurl');
        if ($sslcdnurl === "https://c328740.ssl.cf1.rackcdn.com/mathjax/2.3-latest/MathJax.js") {
            set_config('httpsurl', 'https://cdn.mathjax.org/mathjax/2.3-latest/MathJax.js', 'filter_mathjaxloader');
        }

        upgrade_plugin_savepoint(true, 2014081100, 'filter', 'mathjaxloader');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
