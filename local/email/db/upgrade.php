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
 * @package   local_email
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

function xmldb_local_email_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2011111400) {

        // Define table email to be created.
        $table = new xmldb_table('email');

        // Adding fields to table email.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           XMLDB_SEQUENCE, null);
        $table->add_field('templatename', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('modifiedtime', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           null, null);
        $table->add_field('sent', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('subject', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('body', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);
        $table->add_field('varsreplaced', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           null, null);
        $table->add_field('invoiceid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           null, null, null);
        $table->add_field('classroomid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           null, null, null);

        // Adding keys to table email.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for email.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2011111400, 'local', 'email');
    }

    if ($oldversion < 2012011300) {

        // Define field senderid to be added to email.
        $table = new xmldb_table('email');
        $field = new xmldb_field('senderid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                                  null, null, null, 'classroomid');

        // Conditionally launch add field senderid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2012011300, 'local', 'email');
    }

    if ($oldversion < 2012092600) {

        // Define field headers to be added to email.
        $table = new xmldb_table('email');
        $field = new xmldb_field('headers', XMLDB_TYPE_TEXT, 'big',
                                  null, null, null, null, 'senderid');

        // Conditionally launch add field headers.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2012092600, 'local', 'email');
    }

    if ($oldversion < 2016051601) {

        // Define field due to be added to email.
        $table = new xmldb_table('email');
        $field = new xmldb_field('due', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'headers');

        // Conditionally launch add field due.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2016051601, 'local', 'email');
    }

    if ($oldversion < 2017080700) {

        // Define field lang to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('lang', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'en', 'name');

        // Conditionally launch add field lang.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2017080700, 'local', 'email');
    }

    if ($oldversion < 2017080701) {

        // Define field disabled to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('disabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'body');

        // Conditionally launch add field disabled.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field disabledmanager to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('disabledmanager', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'disabled');

        // Conditionally launch add field disabledmanager.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field disabledsupervisor to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('disabledsupervisor', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'disabledmanager');

        // Conditionally launch add field disabledsupervisor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field repeatperiod to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('repeatperiod', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'disabledsupervisor');

        // Conditionally launch add field repeatperiod.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2017080701, 'local', 'email');
    }

    if ($oldversion < 2017080702) {

        // Define field repeatvalue to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('repeatvalue', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'repeatperiod');

        // Conditionally launch add field repeatvalue.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field repeateday to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('repeateday', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'repeatvalue');

        // Conditionally launch add field repeateday.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table email_templateset to be created.
        $table = new xmldb_table('email_templateset');

        // Adding fields to table email_templateset.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table email_templateset.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for email_templateset.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table email_templateset_templates to be created.
        $table = new xmldb_table('email_templateset_templates');

        // Adding fields to table email_templateset_templates.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('templateset', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lang', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subject', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('body', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('disabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('disabledmanager', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('disabledsupervisor', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('repeatperiod', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('repeatvalue', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('repeateday', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table email_templateset_templates.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for email_templateset_templates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2017080702, 'local', 'email');
    }

    if ($oldversion < 2017080703) {

        // Rename field name on table email_templateset to templatesetname.
        $table = new xmldb_table('email_templateset');
        $field = new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'id');

        // Launch rename field templatesetname.
        $dbman->rename_field($table, $field, 'templatesetname');

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2017080703, 'local', 'email');
    }

    if ($oldversion < 2017080704) {

        // Define field companyid to be added to email.
        $table = new xmldb_table('email');
        $field = new xmldb_field('companyid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'varsreplaced');

        // Conditionally launch add field companyid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // get all emails
        $emails = $DB->get_recordset('email', [], '', 'id, userid');

        foreach ($emails as $email) {
            $company = company::by_userid($email->userid);
            if (!empty($company->id)) {
                $DB->set_field('email','companyid', $company->id, array('id' => $email->id));
            }
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2017080704, 'local', 'email');
    }

    if ($oldversion < 2017080705) {

        // Define field signature to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('signature', XMLDB_TYPE_TEXT, null, null, null, null, null, 'body');

        // Conditionally launch add field signature.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field emailto to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('emailto', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'disabledsupervisor');

        // Conditionally launch add field emailto.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field emailtoother to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('emailtoother', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'emailto');

        // Conditionally launch add field emailtoother.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field emailcc to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('emailcc', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'emailtoother');

        // Conditionally launch add field emailcc.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field emailccother to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('emailccother', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'emailcc');

        // Conditionally launch add field emailccother.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field emailfrom to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('emailfrom', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'emailccother');

        // Conditionally launch add field emailfrom.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field emailfromother to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('emailfromother', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'emailfrom');

        // Conditionally launch add field emailfromother.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field emailreplyto to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('emailreplyto', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'emailfromother');

        // Conditionally launch add field emailreplyto.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field emailreplytoother to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('emailreplytoother', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'emailreplyto');

        // Conditionally launch add field emailreplytoother.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2017080705, 'local', 'email');
    }

    if ($oldversion < 2017080707) {

        // Rename field repeateday on table email_template to repeatday.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('repeateday', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'repeatvalue');

        // Launch rename field repeatday.
        $dbman->rename_field($table, $field, 'repeatday');

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2017080707, 'local', 'email');
    }

    if ($oldversion < 2018112400) {

        // Define field emailfromothername to be added to email_template.
        $table = new xmldb_table('email_template');
        $field = new xmldb_field('emailfromothername', XMLDB_TYPE_TEXT, null, null, null, null, null, 'repeatday');

        // Conditionally launch add field emailfromothername.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2018112400, 'local', 'email');
    }

    if ($oldversion < 2018112401) {

        // Changing type of field subject on table email_templateset_templates to char.
        $table = new xmldb_table('email_templateset_templates');
        $field = new xmldb_field('subject', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'lang');

        // Launch change of type for field subject.
        $dbman->change_field_type($table, $field);

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2018112401, 'local', 'email');
    }

    if ($oldversion < 2018112402) {

        // Changing precision of field subject on table email_templateset_templates to (255).
        $table = new xmldb_table('email_templateset_templates');
        $field = new xmldb_field('subject', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'lang');

        // Launch change of precision for field subject.
        $dbman->change_field_precision($table, $field);

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2018112402, 'local', 'email');
    }

    if ($oldversion < 2023022400) {

        // Define field default to be added to email_templateset.
        $table = new xmldb_table('email_templateset');
        $field = new xmldb_field('isdefault', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'templatesetname');

        // Conditionally launch add field default.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Email savepoint reached.
        upgrade_plugin_savepoint(true, 2023022400, 'local', 'email');
    }

    return $result;

}
