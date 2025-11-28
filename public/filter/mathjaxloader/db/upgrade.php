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
    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2025022700) {
        // Set value of "httpsurl" to the latest MathJax cdn version 3.2.2.
        set_config('httpsurl', 'https://cdn.jsdelivr.net/npm/mathjax@3.2.2/es5/tex-mml-chtml.js', 'filter_mathjaxloader');

        // Set the "mathjaxconfig" value to empty due to default config has been set by default.
        // We can always set new configs from the setting page in site admin.
        set_config('mathjaxconfig', '', 'filter_mathjaxloader');

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2025022700, 'filter', 'mathjaxloader');
    }

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2025111100) {
        // Set value of "httpsurl" to the latest MathJax cdn version 4.0.0.
        set_config('httpsurl', 'https://cdn.jsdelivr.net/npm/mathjax@4.0.0/tex-mml-chtml.js', 'filter_mathjaxloader');

        // Set the "mathjaxconfig" value to empty due to default config has been set by default.
        set_config('mathjaxconfig', '', 'filter_mathjaxloader');

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2025111100, 'filter', 'mathjaxloader');
    }

    return true;
}
