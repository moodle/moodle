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
 * This file defines tasks performed by the plugin.
 *
 * @package    ltiservice_gradebookservices
 * @copyright  2020 Cengage Learning http://www.cengage.com
 * @author     Claude Vervoort
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die;

/**
 * xmldb_ltiservice_gradebookservices_upgrade is the function that upgrades
 * the gradebook lti service subplugin database when is needed.
 *
 * This function is automatically called when version number in
 * version.php changes.
 *
 * @param int $oldversion New old version number.
 *
 * @return boolean
 */
function xmldb_ltiservice_gradebookservices_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020042401) {
        // Define field typeid to be added to lti_tool_settings.
        $table = new xmldb_table('ltiservice_gradebookservices');
        $field = new xmldb_field('resourceid', XMLDB_TYPE_CHAR, "512", null, null, null, null);

        // Conditionally launch add field typeid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2020042401, 'ltiservice', 'gradebookservices');
    }

    if ($oldversion < 2020042402) {
        // Now that we have added the new column let's migrate it'
        // Prior implementation was storing the resourceid under the grade item idnumber, so moving it to lti_gradebookservices.
        // We only care for mod/lti grade items as manual columns would already have a matching gradebookservices record.

        $DB->execute("INSERT INTO {ltiservice_gradebookservices}
                (gradeitemid, courseid, typeid, ltilinkid, resourceid, baseurl, toolproxyid)
         SELECT gi.id, courseid, lti.typeid, lti.id, gi.idnumber, t.baseurl, t.toolproxyid
           FROM {grade_items} gi
           JOIN {lti} lti ON lti.id=gi.iteminstance AND gi.itemtype='mod' AND gi.itemmodule='lti'
           JOIN {lti_types} t ON t.id = lti.typeid
          WHERE gi.id NOT IN ( SELECT gradeitemid
                                 FROM {ltiservice_gradebookservices} )
             AND gi.idnumber IS NOT NULL
             AND gi.idnumber <> ''");

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2020042402, 'ltiservice', 'gradebookservices');
    }

    if ($oldversion < 2020042403) {
        // Here updating the resourceid of pre-existing lti_gradebookservices.
        $DB->execute("UPDATE {ltiservice_gradebookservices}
                         SET resourceid = (SELECT idnumber FROM {grade_items} WHERE id=gradeitemid)
                       WHERE gradeitemid in (SELECT id FROM {grade_items}
                                             WHERE ((itemtype='mod' AND itemmodule='lti') OR itemtype='manual')
                                               AND idnumber IS NOT NULL
                                               AND idnumber <> '')
                         AND (resourceid is null OR resourceid = '')");

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2020042403, 'ltiservice', 'gradebookservices');
    }

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022051900) {
        $table = new xmldb_table('ltiservice_gradebookservices');
        $field = new xmldb_field('subreviewurl', XMLDB_TYPE_TEXT, null, null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('subreviewparams', XMLDB_TYPE_TEXT, null, null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2022051900, 'ltiservice', 'gradebookservices');
    }

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
