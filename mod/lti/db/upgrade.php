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
    global $CFG, $DB;

    require_once(__DIR__ . '/upgradelib.php');

    $dbman = $DB->get_manager();

    if ($oldversion < 2014060201) {

        // Changing type of field grade on table lti to int.
        $table = new xmldb_table('lti');
        $field = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '100',
                'instructorchoiceacceptgrades');

        // Launch change of type for field grade.
        $dbman->change_field_type($table, $field);

        // Lti savepoint reached.
        upgrade_mod_savepoint(true, 2014060201, 'lti');
    }

    if ($oldversion < 2014061200) {

        // Define table lti_tool_proxies to be created.
        $table = new xmldb_table('lti_tool_proxies');

        // Adding fields to table lti_tool_proxies.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'Tool Provider');
        $table->add_field('regurl', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('state', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('guid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('secret', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('vendorcode', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('capabilityoffered', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('serviceoffered', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('toolproxy', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table lti_tool_proxies.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table lti_tool_proxies.
        $table->add_index('guid', XMLDB_INDEX_UNIQUE, array('guid'));

        // Conditionally launch create table for lti_tool_proxies.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table lti_tool_settings to be created.
        $table = new xmldb_table('lti_tool_settings');

        // Adding fields to table lti_tool_settings.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('toolproxyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('coursemoduleid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('settings', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table lti_tool_settings.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('toolproxy', XMLDB_KEY_FOREIGN, array('toolproxyid'), 'lti_tool_proxies', array('id'));
        $table->add_key('course', XMLDB_KEY_FOREIGN, array('course'), 'course', array('id'));
        $table->add_key('coursemodule', XMLDB_KEY_FOREIGN, array('coursemoduleid'), 'lti', array('id'));

        // Conditionally launch create table for lti_tool_settings.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table lti_types to be updated.
        $table = new xmldb_table('lti_types');

        // Adding fields to table lti_types.
        $field = new xmldb_field('toolproxyid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('enabledcapability', XMLDB_TYPE_TEXT, null, null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('parameter', XMLDB_TYPE_TEXT, null, null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('icon', XMLDB_TYPE_TEXT, null, null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('secureicon', XMLDB_TYPE_TEXT, null, null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lti savepoint reached.
        upgrade_mod_savepoint(true, 2014061200, 'lti');
    }

    if ($oldversion < 2014100300) {

        mod_lti_upgrade_custom_separator();

        // Lti savepoint reached.
        upgrade_mod_savepoint(true, 2014100300, 'lti');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016041800) {

        // Define field description to be added to lti_types.
        $table = new xmldb_table('lti_types');
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'timemodified');

        // Conditionally launch add field description.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lti savepoint reached.
        upgrade_mod_savepoint(true, 2016041800, 'lti');
    }

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016052301) {

        // Changing type of field value on table lti_types_config to text.
        $table = new xmldb_table('lti_types_config');
        $field = new xmldb_field('value', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'name');

        // Launch change of type for field value.
        $dbman->change_field_type($table, $field);

        // Lti savepoint reached.
        upgrade_mod_savepoint(true, 2016052301, 'lti');
    }

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017051501) {

        // A bug in the LTI plugin incorrectly inserted a grade item for
        // LTI instances which were set to not allow grading.
        // The change finds any LTI which does not have grading enabled,
        // and updates any grades to delete them.

        $ltis = $DB->get_recordset_sql("
                SELECT
                       l.id,
                       l.course,
                       l.instructorchoiceacceptgrades,
                       t.enabledcapability,
                       t.toolproxyid,
                       tc.value AS acceptgrades
                  FROM {lti} l
            INNER JOIN {grade_items} gt
                    ON l.id = gt.iteminstance
             LEFT JOIN {lti_types} t
                    ON t.id = l.typeid
             LEFT JOIN {lti_types_config} tc
                    ON tc.typeid = t.id AND tc.name = 'acceptgrades'
                 WHERE gt.itemmodule = 'lti'
                   AND gt.itemtype = 'mod'
        ");

        foreach ($ltis as $lti) {
            $acceptgrades = true;
            if (empty($lti->toolproxyid)) {
                $typeacceptgrades = isset($lti->acceptgrades) ? $lti->acceptgrades : 2;
                if (!($typeacceptgrades == 1 ||
                        ($typeacceptgrades == 2 && $lti->instructorchoiceacceptgrades == 1))) {
                    $acceptgrades = false;
                }
            } else {
                $enabledcapabilities = explode("\n", $lti->enabledcapability);
                $acceptgrades = in_array('Result.autocreate', $enabledcapabilities);
            }

            if (!$acceptgrades) {
                // Required when doing CLI upgrade.
                require_once($CFG->libdir . '/gradelib.php');
                grade_update('mod/lti', $lti->course, 'mod', 'lti', $lti->id, 0, null, array('deleted' => 1));
            }

        }

        $ltis->close();

        upgrade_mod_savepoint(true, 2017051501, 'lti');
    }

    return true;

}
