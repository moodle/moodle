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
 * Random question type upgrade code.
 *
 * @package    qtype_random
 * @copyright  2014 Eric Merrill
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the random question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_random_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this.

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this.

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this.

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014060200) {
        $sql = "UPDATE {question}
                   SET questiontext = '0'
                 WHERE qtype = 'random'
                   AND " . $DB->sql_compare_text('questiontext') . " = ?";
        $DB->execute($sql, array(''));

        // Record that qtype_random savepoint was reached.
        upgrade_plugin_savepoint(true, 2014060200, 'qtype', 'random');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
