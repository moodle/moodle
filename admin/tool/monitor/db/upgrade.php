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
 * Upgrade scirpt for tool_monitor.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_tool_monitor_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014102000) {

        // Define field lastnotificationsent to be added to tool_monitor_subscriptions.
        $table = new xmldb_table('tool_monitor_subscriptions');
        $field = new xmldb_field('lastnotificationsent', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');

        // Conditionally launch add field lastnotificationsent.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Monitor savepoint reached.
        upgrade_plugin_savepoint(true, 2014102000, 'tool', 'monitor');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016052305) {

        // Define field inactivedate to be added to tool_monitor_subscriptions.
        $table = new xmldb_table('tool_monitor_subscriptions');
        $field = new xmldb_field('inactivedate', XMLDB_TYPE_INTEGER, '10', null, true, null, 0, 'lastnotificationsent');

        // Conditionally launch add field inactivedate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Monitor savepoint reached.
        upgrade_plugin_savepoint(true, 2016052305, 'tool', 'monitor');
    }

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016120501) {

        // Delete "orphaned" subscriptions.
        $sql = "SELECT DISTINCT s.courseid
                  FROM {tool_monitor_subscriptions} s
       LEFT OUTER JOIN {course} c ON c.id = s.courseid
                 WHERE s.courseid <> 0 and c.id IS NULL";
        $deletedcourses = $DB->get_field_sql($sql);
        if ($deletedcourses) {
            list($sql, $params) = $DB->get_in_or_equal($deletedcourses);
            $DB->execute("DELETE FROM {tool_monitor_subscriptions} WHERE courseid " . $sql, $params);
        }

        // Monitor savepoint reached.
        upgrade_plugin_savepoint(true, 2016120501, 'tool', 'monitor');
    }

    return true;
}
