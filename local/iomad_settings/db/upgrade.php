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
 * @package   local_iomad_settings
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * As of the implementation of this block and the general navigation code
 * in Moodle 2.0 the body of immediate upgrade work for this block and
 * settings is done in core upgrade {@see lib/db/upgrade.php}
 *
 * There were several reasons that they were put there and not here, both becuase
 * the process for the two blocks was very similar and because the upgrade process
 * was complex due to us wanting to remvoe the outmoded blocks that this
 * block was going to replace.
 *
 * @global moodle_database $DB
 * @param int $oldversion
 * @param object $block
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_iomad_settings_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2011110201) {

        // Define fields to be added to certificate.
        $table = new xmldb_table('certificate');

        $field = new xmldb_field('serialnumberformat', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'timemodified');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('reset_sequence', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, 'serialnumberformat');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customtext2', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'reset_sequence');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customtext3', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'customtext2');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_block_savepoint(true, 2011110201, 'iomad_settings');

    }

    if ($oldversion < 2011110300) {
        $table = new xmldb_table('certificate_serialnumber');

        $index = new xmldb_index('certificate_year_sequenceno_unique', XMLDB_INDEX_UNIQUE,
                                  array('certificateid', 'year', 'sequenceno'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        if ($dbman->field_exists($table, 'year')) {
            $field = new xmldb_field('year', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'timecreated');
            $dbman->rename_field($table, $field, 'sequence');
        }

        $field = new xmldb_field('sequence', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'timecreated');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        } else {
            $dbman->change_field_precision($table, $field);
        }
        $index = new xmldb_index('certificate_sequence_sequenceno_unique', XMLDB_INDEX_UNIQUE,
                                  array('certificateid', 'sequence', 'sequenceno'));
    }

    if ($oldversion < 2016031700) {
        // Change the default settings for extended username chars to be true.
        $DB->execute("update {config} set value=1 where name='extendedusernamechars'");

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2016031700, 'local', 'iomad_settings');
    }
    return $result;
}
