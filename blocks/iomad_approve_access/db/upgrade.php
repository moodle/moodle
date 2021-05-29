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
 * @package    Block Approve Enroll
 * @copyright  2011 onwards E-Learn Design Limited
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_iomad_approve_access_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2013061100) {

        // Define field companyid to be added to block_iomad_approve_access.
        $table = new xmldb_table('block_iomad_approve_access');
        $field = new xmldb_field('companyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Conditionally launch add field companyid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad_approve_access savepoint reached.
        upgrade_block_savepoint(true, 2013061100, 'iomad_approve_access');
    }

    if ($oldversion < 2013071000) {

        // Define field activityid to be added to block_iomad_approve_access.
        $table = new xmldb_table('block_iomad_approve_access');
        $field = new xmldb_field('activityid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'courseid');

        // Conditionally launch add field activityid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad_approve_access savepoint reached.
        upgrade_block_savepoint(true, 2013071000, 'iomad_approve_access');
    }

    return true;
}
