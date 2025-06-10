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
 * Kaltura video presentation upgrade file.
 *
 * @package    mod_kalvidpres
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

function xmldb_kalvidpres_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011111112) {

        // Changing type of field intro on table kalvidpres to text
        $table = new xmldb_table('kalvidpres');
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'name');

        // Launch change of type for field intro
        $dbman->change_field_type($table, $field);

        // kalvidpres savepoint reached
        upgrade_mod_savepoint(true, 2011111112, 'kalvidpres');
    }

    if ($oldversion < 2012010301) {

            // Define index doc_entry_id_idx (not unique) to be dropped form kalvidpres
        $table = new xmldb_table('kalvidpres');
        $index = new xmldb_index('doc_entry_id_idx', XMLDB_INDEX_UNIQUE, array('doc_entry_id'));

        // Conditionally launch drop index doc_entry_id_idx
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define index doc_entry_id_idx (not unique) to be added to kalvidpres
        $index = new xmldb_index('doc_entry_id_idx', XMLDB_INDEX_NOTUNIQUE, array('doc_entry_id'));

        // Conditionally launch add index doc_entry_id_idx
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // kalvidpres savepoint reached
        upgrade_mod_savepoint(true, 2012010301, 'kalvidpres');
    }

    if ($oldversion < 2014013000) {

        // Define field source to be added to kalvidpres.
        $table = new xmldb_table('kalvidpres');
        $field = new xmldb_field('source', XMLDB_TYPE_TEXT, null, null, null, null, null, 'width');

        // Conditionally launch add field source.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Kalvidpres savepoint reached.
        upgrade_mod_savepoint(true, 2014013000, 'kalvidpres');
    }

    if ($oldversion < 2014023000.01) {

        // Define field metadata to be added to kalvidpres.
        $table = new xmldb_table('kalvidpres');
        $field = new xmldb_field('metadata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'source');

        // Conditionally launch add field metadata.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Kalvidassign savepoint reached.
        upgrade_mod_savepoint(true, 2014023000.01, 'kalvidpres');
    }

    return true;
}