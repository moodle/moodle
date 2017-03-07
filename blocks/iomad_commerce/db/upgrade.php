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
 * This file keeps track of upgrades to the navigation block
 *
 * Sometimes, changes between versions involve alterations to database structures
 * and other major things that may break installations.
 *
 * The upgrade function in this file will attempt to perform all the necessary
 * actions to upgrade your older installation to the current version.
 *
 * If there's something it cannot do itself, it will tell you what you need to do.
 *
 * The commands in here will all be database-neutral, using the methods of
 * database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @since 2.0
 * @package blocks
 * @copyright 2009 Sam Hemelryk
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
