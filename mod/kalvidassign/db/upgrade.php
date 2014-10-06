<?php
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
 * Kaltura video assignment upgrade script.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

function xmldb_kalvidassign_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011091301) {

        // Changing type of field intro on table kalvidassign to text
        $table = new xmldb_table('kalvidassign');
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'name');

        // Launch change of type for field intro
        $dbman->change_field_type($table, $field);

        // kalvidassign savepoint reached
        upgrade_mod_savepoint(true, 2011091301, 'kalvidassign');
    }

    if ($oldversion < 2014013000) {

        // Define field source to be added to kalvidassign_submission.
        $table = new xmldb_table('kalvidassign_submission');
        $field = new xmldb_field('source', XMLDB_TYPE_TEXT, null, null, null, null, null, 'entry_id');

        // Conditionally launch add field source.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field width to be added to kalvidassign_submission.
        $field = new xmldb_field('width', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'source');

        // Conditionally launch add field width.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field height to be added to kalvidassign_submission.
        $field = new xmldb_field('height', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'width');

        // Conditionally launch add field height.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Kalvidassign savepoint reached.
        upgrade_mod_savepoint(true, 2014013000, 'kalvidassign');
    }

    if ($oldversion < 2014013001) {

        // Define field metadata to be added to kalvidassign_submission.
        $table = new xmldb_table('kalvidassign_submission');
        $field = new xmldb_field('metadata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'timemarked');

        // Conditionally launch add field metadata.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Kalvidassign savepoint reached.
        upgrade_mod_savepoint(true, 2014013001, 'kalvidassign');
    }

    return true;
}
