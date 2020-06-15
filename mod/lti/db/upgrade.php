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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

/**
 * This file keeps track of upgrades to the lti module
 *
 * @package mod_lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die;

/**
 * xmldb_lti_upgrade is the function that upgrades
 * the lti module database when is needed
 *
 * This function is automaticly called when version number in
 * version.php changes.
 *
 * @param int $oldversion New old version number.
 *
 * @return boolean
 */
function xmldb_lti_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019031300) {
        // Define table lti_access_tokens to be updated.
        $table = new xmldb_table('lti_types');

        // Define field ltiversion to be added to lti_types.
        $field = new xmldb_field('ltiversion', XMLDB_TYPE_CHAR, 10, null, XMLDB_NOTNULL, null, null, 'coursevisible');

        // Conditionally launch add field ltiversion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $DB->set_field_select('lti_types', 'ltiversion', 'LTI-1p0', 'toolproxyid IS NULL');
            $DB->set_field_select('lti_types', 'ltiversion', 'LTI-2p0', 'toolproxyid IS NOT NULL');
        }

        // Define field clientid to be added to lti_types.
        $field = new xmldb_field('clientid', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'ltiversion');

        // Conditionally launch add field clientid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index clientid (unique) to be added to lti_types.
        $index = new xmldb_index('clientid', XMLDB_INDEX_UNIQUE, array('clientid'));

        // Conditionally launch add index clientid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        require_once($CFG->dirroot . '/mod/lti/upgradelib.php');

        $warning = mod_lti_verify_private_key();
        if (!empty($warning)) {
            echo $OUTPUT->notification($warning, 'notifyproblem');
        }

        // Lti savepoint reached.
        upgrade_mod_savepoint(true, 2019031300, 'lti');
    }

    if ($oldversion < 2019031301) {
        // Define table lti_access_tokens to be created.
        $table = new xmldb_table('lti_access_tokens');

        // Adding fields to table lti_access_tokens.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('typeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('scope', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('token', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('validuntil', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lastaccess', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table lti_access_tokens.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('typeid', XMLDB_KEY_FOREIGN, array('typeid'), 'lti_types', array('id'));

        // Add an index.
        $table->add_index('token', XMLDB_INDEX_UNIQUE, array('token'));

        // Conditionally launch create table for lti_access_tokens.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Lti savepoint reached.
        upgrade_mod_savepoint(true, 2019031301, 'lti');
    }

    if ($oldversion < 2019031302) {
        // Define field typeid to be added to lti_tool_settings.
        $table = new xmldb_table('lti_tool_settings');
        $field = new xmldb_field('typeid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'toolproxyid');

        // Conditionally launch add field typeid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define key typeid (foreign) to be added to lti_tool_settings.
        $table = new xmldb_table('lti_tool_settings');
        $key = new xmldb_key('typeid', XMLDB_KEY_FOREIGN, ['typeid'], 'lti_types', ['id']);

        // Launch add key typeid.
        $dbman->add_key($table, $key);

        // Lti savepoint reached.
        upgrade_mod_savepoint(true, 2019031302, 'lti');
    }

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
