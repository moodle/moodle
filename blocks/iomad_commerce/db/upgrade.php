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
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_iomad_commerce_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2012012800) {

        // Changing type of field invoiceableitemtype on table invoiceitem to char.
        $table = new xmldb_table('invoiceitem');
        $field = new xmldb_field('invoiceableitemtype',
                                  XMLDB_TYPE_CHAR,
                                  '20',
                                  null,
                                  XMLDB_NOTNULL,
                                  null,
                                  null,
                                  'invoiceableitemid');

        // Launch change of type for field invoiceableitemtype.
        $dbman->change_field_type($table, $field);

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2012012800, 'iomad_commerce');
    }

    if ($oldversion < 2012012801) {

        // Define field date to be added to invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('date', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'pp_reason');

        // Conditionally launch add field date.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2012012801, 'iomad_commerce');
    }

    if ($oldversion < 2012012802) {

        // Define field single_purchase_shelflife to be added to course_shopsettings.
        $table = new xmldb_table('course_shopsettings');
        $field = new xmldb_field('single_purchase_shelflife',
                                 XMLDB_TYPE_INTEGER,
                                 '20',
                                 XMLDB_UNSIGNED,
                                 XMLDB_NOTNULL,
                                 null,
                                 '0',
                                 'single_purchase_validlength');

        // Conditionally launch add field single_purchase_shelflife.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2012012802, 'iomad_commerce');
    }

    if ($oldversion < 2017011000) {

        // Define field state to be added to invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('state', XMLDB_TYPE_CHAR, '120', null, null, null, null, 'city');

        // Conditionally launch add field state.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2017011000, 'iomad_commerce');
    }

    if ($oldversion < 2017030700) {

        // Changing type of field company on table invoice to char.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('company', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'pp_payerstatus');

        // Launch change of type for field company.
        $dbman->change_field_type($table, $field);

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2017030700, 'iomad_commerce');
    }
    return $result;
}
