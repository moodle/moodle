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
 * Keeps track of upgrades to the global search block
 *
 * @package    blocks
 * @subpackage search
 * @copyright  2010 Aparup Banerjee <aparup@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_search_upgrade($oldversion) {
    global $CFG, $DB;

    require('upgradelib.php');
    $result = TRUE;
    $dbman = $DB->get_manager();

    if ($oldversion < 2010101800) {
        // See MDL-24374
        // Changing type of field docdate on table block_search_documents to int
        // Changing type of field updated on table block_search_documents to int
        $table = new xmldb_table('block_search_documents');

        $field_docdate_new = new xmldb_field('docdate_new', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'docdate');
        $field_updated_new = new xmldb_field('updated_new', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'updated');
        $field_docdate_old = new xmldb_field('docdate');
        $field_updated_old = new xmldb_field('updated');

        // Conditionally launch add temporary fields
        if (!$dbman->field_exists($table, $field_docdate_new)) {
            $dbman->add_field($table, $field_docdate_new);
        }
        if (!$dbman->field_exists($table, $field_updated_new)) {
            $dbman->add_field($table, $field_updated_new);
        }

        $sql = "SELECT id, docdate, updated FROM {block_search_documents}";
        $search_documents = $DB->get_records_sql($sql);
        if ($search_documents) {
            foreach ($search_documents as $sd) {
                $sd->docdate_new = convert_datetime_upgrade($sd->docdate);
                $sd->updated_new = convert_datetime_upgrade($sd->updated);
                $DB->update_record('block_search_documents', $sd);
            }
        }
        // Conditionally launch drop the old fields
        if ($dbman->field_exists($table, $field_docdate_old)) {
            $dbman->drop_field($table, $field_docdate_old);
        }
        if ($dbman->field_exists($table, $field_updated_old)) {
            $dbman->drop_field($table, $field_updated_old);
        }

        //rename the new fields to the original field names.
        $dbman->rename_field($table, $field_docdate_new, 'docdate');
        $dbman->rename_field($table, $field_updated_new, 'updated');

        // search savepoint reached
        upgrade_block_savepoint(true, 2010101800, 'search');
    }

    if ($oldversion < 2010110900) {
        unset_config('block_search_text');
        unset_config('block_search_button');
        upgrade_block_savepoint(true, 2010110900, 'search');
    }

    if ($oldversion < 2010111100) {
        // set block to hidden if global search is disabled.
        if ($CFG->enableglobalsearch != 1) {
            $DB->set_field('block', 'visible', 0, array('name'=>'search'));     // Hide block
        }
        upgrade_block_savepoint(true, 2010111100, 'search');
    }
    return $result;
}
