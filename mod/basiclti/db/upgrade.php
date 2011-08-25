<?php
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
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file keeps track of upgrades to the basiclti module
 *
 * @package basiclti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * xmldb_basiclti_upgrade is the function that upgrades Moodle's
 * database when is needed
 *
 * This function is automaticly called when version number in
 * version.php changes.
 *
 * @param int $oldversion New old version number.
 *
 * @return boolean
 */

function xmldb_basiclti_upgrade($oldversion=0) {

    global $DB;

    $dbman = $DB->get_manager();
    $result = true;

    if ($result && $oldversion < 2008090201) {

        $table = new xmldb_table('basiclti_types');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        upgrade_mod_savepoint($result, 2008090201, 'basiclti_types');

        $table = new xmldb_table('basiclti_types_config');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('typeid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('value', XMLDB_TYPE_CHAR, '255', XMLDB_NOTNULL, null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        upgrade_mod_savepoint($result, 2008090201, 'basiclti_types_config');

        $table = new xmldb_table('basiclti');
        $field = new xmldb_field('typeid');

        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null);
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint($result, 2008090201, 'basiclti');
    }

    if ($result && $oldversion < 2008091201) {
        $table = new xmldb_table('basiclti_types');
        $field = new xmldb_field('rawname');

        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '100', null,  null, null, null, null);
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint($result, 2008091202, 'basiclti_types');
    }

    if ($result && $oldversion < 2011011200) {
        $table = new xmldb_table('basiclti');

        $field = new xmldb_field('acceptgrades');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,  XMLDB_NOTNULL, null, '0', null);
            $result = $result && $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('instructorchoiceacceptgrades');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,  XMLDB_NOTNULL, null, '0', null);
            $result = $result && $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('allowroster');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,  XMLDB_NOTNULL, null, '0', null);
            $result = $result && $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('instructorchoiceallowroster');
        if (!$dbman->field_exists($table, $field)) {
               $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,  XMLDB_NOTNULL, null, '0', null);
            $result = $result && $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('allowsetting');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,  XMLDB_NOTNULL, null, '0', null);
            $result = $result && $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('instructorchoiceallowsetting');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,  XMLDB_NOTNULL, null, '0', null);
            $result = $result && $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('setting');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '8192', null, null, null, '', null);
            $result = $result && $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('placementsecret');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '1024', null,  null, null, '', null);
            $result = $result && $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timeplacementsecret');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,  XMLDB_NOTNULL, null, '0', null);
            $result = $result && $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('oldplacementsecret');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '1024', null,  null, null, '', null);
            $result = $result && $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2011011200, 'basiclti');
    }

    if ($result && $oldversion < 2011011304) {
        $table = new xmldb_table('basiclti');
        $field = new xmldb_field('grade');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,  XMLDB_NOTNULL, null, '100', null);
            $result = $result && $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2011011304, 'basiclti');
    }

    if ($result && $oldversion < 2011052600) {
        $table = new xmldb_table('basiclti');

        $field = new xmldb_field('resourcekey');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('password');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('sendname');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('sendemailaddr');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('allowroster');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('allowsetting');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('acceptgrades');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('customparameters');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2011052600, 'basiclti');
    }

    if($result && $oldversion < 2011070100) {
        $table = new xmldb_table('basiclti');

        $field = new xmldb_field('instructorcustomparameters');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '255', null,  null, null, '', null);
            $result = $result && $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2011070100, 'basiclti');
    }

    return $result;
}

