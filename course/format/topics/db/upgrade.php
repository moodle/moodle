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
 * Upgrade scripts for Topics course format.
 *
 * @package    format_topics
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade script for Topics course format.
 *
 * @param int|float $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_format_topics_upgrade($oldversion) {
    global $DB;

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023030700) {
        // For sites migrating from 4.0.x or 4.1.x where the indentation was removed,
        // we are disabling 'indentation' value by default.
        if ($oldversion >= 2022041900) {
            set_config('indentation', 0, 'format_topics');
        } else {
            set_config('indentation', 1, 'format_topics');
        }
        upgrade_plugin_savepoint(true, 2023030700, 'format', 'topics');
    }

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023100901) {
        // During the migration to version 4.4, ensure that sections with null names are renamed to their corresponding
        // previous 'Topic X' for continuity.
        $newsectionname = $DB->sql_concat("'Topic '", 'section');
        $sql = <<<EOF
                    UPDATE {course_sections}
                       SET name = $newsectionname
                     WHERE section > 0 AND (name IS NULL OR name = '')
                           AND course IN (SELECT id FROM {course} WHERE format = 'topics')
        EOF;
        $DB->execute(
            sql: $sql,
        );

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2023100901, 'format', 'topics');
    }

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
