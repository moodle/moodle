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
 * LSU workdaystudent enrolment plugin installation.
 *
 * @package    enrol_workdaystudent
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Performs upgrade of the database structure and data
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool true
 */
function xmldb_enrol_workdaystudent_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    // START OF MODIFICATIONS FOR VERSION 2025062000.
    if ($oldversion < 2025062000) {
        $tablesections = new xmldb_table('enrol_wds_sections');

        $keytodrop = new xmldb_key('wds_sec_sdi_uq');

        $dbman->drop_key($tablesections, $keytodrop);

        $newkeysections = new xmldb_key('wds_sec_sdi_uq', XMLDB_KEY_UNIQUE, ['course_section_definition_id', 'course_listing_id']);

        $dbman->add_key($tablesections, $newkeysections);

        $tablesport = new xmldb_table('enrol_wds_sport');

        $keysportcodeuq = new xmldb_key('wds_spo_cod_uq', XMLDB_KEY_UNIQUE, ['code']);

        $dbman->drop_key($tablesport, $keysportcodeuq);

        $fieldcode = new xmldb_field('code', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, false, null, 'id');

        $dbman->change_field_type($tablesport, $fieldcode);

        $dbman->add_key($tablesport, $keysportcodeuq);

        upgrade_plugin_savepoint(true, 2025062000, 'enrol', 'workdaystudent');
    }
    // END OF MODIFICATIONS FOR VERSION 2025062000.

    return true;
}
