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

/**
 * This file keeps track of upgrades to the lti enrolment plugin
 *
 * @package enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
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
function xmldb_enrol_lti_upgrade($oldversion) {
    global $CFG, $OUTPUT, $DB;
    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021052501) {
        // LTI 1.3: Set a private key for this site (which is acting as a tool in LTI 1.3).
        require_once($CFG->dirroot . '/enrol/lti/upgradelib.php');

        $warning = enrol_lti_verify_private_key();
        if (!empty($warning)) {
            echo $OUTPUT->notification($warning, 'notifyproblem');
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2021052501, 'enrol', 'lti');
    }

    if ($oldversion < 2021052502) {
        // Define table enrol_lti_app_registration to be created.
        $table = new xmldb_table('enrol_lti_app_registration');

        // Adding fields to table enrol_lti_app_registration.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('platformid', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('clientid', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
        $table->add_field('platformclienthash', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('authenticationrequesturl', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('jwksurl', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('accesstokenurl', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_app_registration.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Add unique index on platformclienthash.
        $table->add_index('platformclienthash', XMLDB_INDEX_UNIQUE, ['platformclienthash']);

        // Conditionally launch create table for enrol_lti_app_registration.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2021052502, 'enrol', 'lti');
    }

    if ($oldversion < 2021052503) {
        // Add a new column 'ltiversion' to the enrol_lti_tools table.
        $table = new xmldb_table('enrol_lti_tools');

        // Define field ltiversion to be added to enrol_lti_tools.
        $field = new xmldb_field('ltiversion', XMLDB_TYPE_CHAR, 15, null, XMLDB_NOTNULL, null, "LTI-1p3", 'contextid');

        // Conditionally launch add field ltiversion, setting it to the legacy value for all published content.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $DB->set_field('enrol_lti_tools', 'ltiversion', 'LTI-1p0/LTI-2p0');
        }

        // Define field uuid to be added to enrol_lti_tools.
        $field = new xmldb_field('uuid', XMLDB_TYPE_CHAR, 36, null, null, null, null, 'ltiversion');

        // Conditionally launch add field uuid, setting it to null for existing rows.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $key = new xmldb_key('uuid', XMLDB_KEY_UNIQUE, ['uuid']);
            $dbman->add_key($table, $key);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2021052503, 'enrol', 'lti');
    }

    if ($oldversion < 2021052504) {
        // Define table enrol_lti_deployment to be created.
        $table = new xmldb_table('enrol_lti_deployment');

        // Adding fields to table enrol_lti_deployment.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('deploymentid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('platformid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_deployment.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('platformid', XMLDB_KEY_FOREIGN, ['platformid'], 'enrol_lti_app_registration', ['id']);

        // Add unique index on platformid (issuer), deploymentid.
        $table->add_index('platformid-deploymentid', XMLDB_INDEX_UNIQUE, ['platformid', 'deploymentid']);

        // Conditionally launch create table for enrol_lti_deployment.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2021052504, 'enrol', 'lti');
    }

    if ($oldversion < 2021052505) {
        // Add a new column 'ltideploymentid' to the enrol_lti_users table.
        $table = new xmldb_table('enrol_lti_users');

        // Define field ltideploymentid to be added to enrol_lti_users.
        $field = new xmldb_field('ltideploymentid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'sourceid');

        // Conditionally launch add field deploymentid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Launch add key ltideploymentid.
        $key = new xmldb_key('ltideploymentid', XMLDB_KEY_FOREIGN, ['ltideploymentid'], 'enrol_lti_deployment', ['id']);
        $dbman->add_key($table, $key);

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2021052505, 'enrol', 'lti');
    }

    if ($oldversion < 2021052506) {
        // Define table enrol_lti_resource_link to be created.
        $table = new xmldb_table('enrol_lti_resource_link');

        // Adding fields to table enrol_lti_resource_link.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('resourcelinkid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resourceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ltideploymentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lticontextid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('lineitemsservice', XMLDB_TYPE_CHAR, '1333', null, null, null, null);
        $table->add_field('lineitemservice', XMLDB_TYPE_CHAR, '1333', null, null, null, null);
        $table->add_field('lineitemscope', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('resultscope', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('scorescope', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('contextmembershipsurl', XMLDB_TYPE_CHAR, '1333', null, null, null, null);
        $table->add_field('nrpsserviceversions', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_resource_link.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('ltideploymentid', XMLDB_KEY_FOREIGN, ['ltideploymentid'], 'enrol_lti_deployment', ['id']);
        $table->add_key('lticontextid', XMLDB_KEY_FOREIGN, ['lticontextid'], 'enrol_lti_context', ['id']);

        // Add unique index on resourcelinkid, ltideploymentid.
        $table->add_index('resourcelinkdid-ltideploymentid', XMLDB_INDEX_UNIQUE, ['resourcelinkid', 'ltideploymentid']);

        // Conditionally launch create table for enrol_lti_resource_link.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2021052506, 'enrol', 'lti');
    }

    if ($oldversion < 2021052507) {
        // Define table enrol_lti_context to be created.
        $table = new xmldb_table('enrol_lti_context');

        // Adding fields to table enrol_lti_context.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ltideploymentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_context.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('ltideploymentid', XMLDB_KEY_FOREIGN, ['ltideploymentid'], 'enrol_lti_deployment', ['id']);

        // Add unique index on ltideploymentid, contextid.
        $table->add_index('ltideploymentid-contextid', XMLDB_INDEX_UNIQUE, ['ltideploymentid', 'contextid']);

        // Conditionally launch create table for enrol_lti_context.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2021052507, 'enrol', 'lti');
    }

    if ($oldversion < 2021052508) {
        // Define table enrol_lti_user_resource_link to be created.
        $table = new xmldb_table('enrol_lti_user_resource_link');

        // Adding fields to table enrol_lti_user_resource_link.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('ltiuserid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resourcelinkid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_user_resource_link.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('ltiuserid', XMLDB_KEY_FOREIGN, ['ltiuserid'], 'enrol_lti_users', ['id']);
        $table->add_key('resourcelinkid', XMLDB_KEY_FOREIGN, ['resourcelinkid'], 'enrol_lti_resource_link', ['id']);

        // Add unique index on userid, resourcelinkid.
        $table->add_index('ltiuserid-resourcelinkid', XMLDB_INDEX_UNIQUE, ['ltiuserid', 'resourcelinkid']);

        // Conditionally launch create table for enrol_lti_user_resource_link.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_plugin_savepoint(true, 2021052508, 'enrol', 'lti');
    }

    if ($oldversion < 2021052512) {
        // Define field legacyconsumerkey to be added to enrol_lti_deployment.
        $table = new xmldb_table('enrol_lti_deployment');
        $field = new xmldb_field('legacyconsumerkey', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'platformid');

        // Conditionally launch add field legacyconsumerkey.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2021052512, 'enrol', 'lti');
    }

    if ($oldversion < 2021052513) {
        // Define table enrol_lti_reg_token to be created.
        $table = new xmldb_table('enrol_lti_reg_token');

        // Adding fields to table enrol_lti_reg_token.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('token', XMLDB_TYPE_CHAR, '60', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expirytime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table enrol_lti_reg_token.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for enrol_lti_reg_token.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2021052513, 'enrol', 'lti');
    }

    if ($oldversion < 2021052514) {
        // Add a new column 'provisioningmodelearner' to the enrol_lti_tools table.
        $table = new xmldb_table('enrol_lti_tools');

        // Define field provisioningmodelearner to be added to enrol_lti_tools.
        $field = new xmldb_field('provisioningmodelearner', XMLDB_TYPE_INTEGER, 2, null, null, null, null, 'uuid');

        // Conditionally launch add field provisioningmodelearner.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field provisioningmodeinstructor to be added to enrol_lti_tools.
        $field = new xmldb_field('provisioningmodeinstructor', XMLDB_TYPE_INTEGER, 2, null, null, null, null,
            'provisioningmodelearner');

        // Conditionally launch add field provisioningmodeinstructor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2021052514, 'enrol', 'lti');
    }

    if ($oldversion < 2022031400) {
        // Changing the default of field platformid on table enrol_lti_app_registration to null.
        $table = new xmldb_table('enrol_lti_app_registration');
        $field = new xmldb_field('platformid', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');

        // Launch change of nullability for field platformid.
        $dbman->change_field_notnull($table, $field);

        // Changing the default of field clientid on table enrol_lti_app_registration to null.
        $field = new xmldb_field('clientid', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'platformid');

        // Launch change of nullability for field clientid.
        $dbman->change_field_notnull($table, $field);

        // Drop the platformclienthash index, so the field can be modified.
        $index = new xmldb_index('platformclienthash', XMLDB_INDEX_UNIQUE, ['platformclienthash']);

        // Conditionally launch drop index platformclienthash.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Changing the default of field platformclienthash on table enrol_lti_app_registration to null.
        $field = new xmldb_field('platformclienthash', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'clientid');

        // Launch change of nullability for field platformclienthash.
        $dbman->change_field_notnull($table, $field);

        // Recreate the platformclienthash index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Changing the default of field authenticationrequesturl on table enrol_lti_app_registration to null.
        $field = new xmldb_field('authenticationrequesturl', XMLDB_TYPE_TEXT, null, null, null, null, null, 'platformclienthash');

        // Launch change of nullability for field authenticationrequesturl.
        $dbman->change_field_notnull($table, $field);

        // Changing the default of field jwksurl on table enrol_lti_app_registration to null.
        $field = new xmldb_field('jwksurl', XMLDB_TYPE_TEXT, null, null, null, null, null, 'authenticationrequesturl');

        // Launch change of nullability for field jwksurl.
        $dbman->change_field_notnull($table, $field);

        // Changing the default of field accesstokenurl on table enrol_lti_app_registration to null.
        $field = new xmldb_field('accesstokenurl', XMLDB_TYPE_TEXT, null, null, null, null, null, 'jwksurl');

        // Launch change of nullability for field accesstokenurl.
        $dbman->change_field_notnull($table, $field);

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2022031400, 'enrol', 'lti');
    }

    if ($oldversion < 2022031401) {
        // Define field uniqueid to be added to enrol_lti_app_registration (defined as null so it can be set for existing rows).
        $table = new xmldb_table('enrol_lti_app_registration');
        $field = new xmldb_field('uniqueid', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'accesstokenurl');

        // Conditionally launch add field uniqueid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Set existing values to use a suitable unique id.
            $recordset = $DB->get_recordset('enrol_lti_app_registration');
            foreach ($recordset as $record) {
                // Create a unique id for the registration. This will be used by:
                // a) The initiate_login endpoint (enrol/lti/login.php), as a stand in for client_id, when that's not provided.
                // b) The dynamic registration endpoint, where it'll be used to identify the incomplete registration to update
                // with the platform details.
                do {
                    $bytes = random_bytes(30);
                    $record->uniqueid = bin2hex($bytes);
                } while ($DB->record_exists('enrol_lti_app_registration', ['uniqueid' => $record->uniqueid]));

                $DB->update_record('enrol_lti_app_registration', $record);
            }
            $recordset->close();

            // Now make the field notnull.
            $field = new xmldb_field('uniqueid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'accesstokenurl');
            $dbman->change_field_notnull($table, $field);
        }

        // Launch add unique key uniqueid.
        $key = new xmldb_key('uniqueid', XMLDB_KEY_UNIQUE, ['uniqueid']);
        $dbman->add_key($table, $key);

        // Define field status to be added to enrol_lti_app_registration with a default value of 1 (to set existing rows).
        $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'uniqueid');

        // Conditionally launch add field status.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Now change the default value to '0'.
            $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'uniqueid');
            $dbman->change_field_default($table, $field);
        }

        // Define field platformuniqueidhash to be added to enrol_lti_app_registration.
        $field = new xmldb_field('platformuniqueidhash', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'status');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            $recordset = $DB->get_recordset('enrol_lti_app_registration');
            foreach ($recordset as $record) {
                $record->platformuniqueidhash = hash('sha256', $record->platformid . ':' . $record->uniqueid);
                $DB->update_record('enrol_lti_app_registration', $record);
            }
            $recordset->close();
        }

        // Add index platformuniqueidhash to enrol_lti_app_registration.
        $index = new xmldb_index('platformuniqueidhash', XMLDB_INDEX_UNIQUE, ['platformuniqueidhash']);

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2022031401, 'enrol', 'lti');
    }

    if ($oldversion < 2022031402) {
        // Define table enrol_lti_reg_token to be dropped.
        $table = new xmldb_table('enrol_lti_reg_token');

        // Conditionally launch drop table for enrol_lti_reg_token.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2022031402, 'enrol', 'lti');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022061500) {
        // Disable all orphaned enrolment method instances.
        $sql = "id IN (SELECT t.enrolid
                         FROM {enrol_lti_tools} t
                    LEFT JOIN {context} c ON (t.contextid = c.id)
                        WHERE c.id IS NULL)";
        $DB->set_field_select('enrol', 'status', 1, $sql);

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2022061500, 'enrol', 'lti');
    }

    if ($oldversion < 2022103100) {
        // Update lti user information for LTI 2.0 users having the wrong consumer secret recorded.
        // This applies to any LTI 2.0 user who has launched the tool (i.e. has lastaccess) and fixes a non-functional grade sync
        // for LTI 2.0 consumers.
        $sql = "SELECT lu.id, lc.secret
                  FROM {enrol_lti_users} lu
                  JOIN {enrol_lti_lti2_consumer} lc
                    ON (" . $DB->sql_compare_text('lu.consumerkey', 255) . " = lc.consumerkey256)
                 WHERE lc.ltiversion = :ltiversion
                   AND " . $DB->sql_compare_text('lu.consumersecret') . " != lc.secret
                   AND lu.lastaccess IS NOT NULL";
        $affectedltiusersrs = $DB->get_recordset_sql($sql, ['ltiversion' => 'LTI-2p0']);
        foreach ($affectedltiusersrs as $ltiuser) {
            $DB->set_field('enrol_lti_users', 'consumersecret', $ltiuser->secret, ['id' => $ltiuser->id]);
        }
        $affectedltiusersrs->close();

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2022103100, 'enrol', 'lti');
    }

    if ($oldversion < 2022110300) {
        // Update lti user information for any users missing a consumer secret.
        // This applies to any user who has launched the tool (i.e. has lastaccess) but who doesn't have a secret recorded.
        // This fixes a bug where enrol_lti_users records are created first during a member sync, and are missing the secret,
        // even despite having launched the tool subsequently.
        $sql = "SELECT lu.id, lc.secret
                  FROM {enrol_lti_users} lu
                  JOIN {enrol_lti_lti2_consumer} lc
                    ON (" . $DB->sql_compare_text('lu.consumerkey', 255) . " = lc.consumerkey256)
                 WHERE lu.consumersecret IS NULL
                   AND lu.lastaccess IS NOT NULL";
        $affectedltiusersrs = $DB->get_recordset_sql($sql);
        foreach ($affectedltiusersrs as $ltiuser) {
            $DB->set_field('enrol_lti_users', 'consumersecret', $ltiuser->secret, ['id' => $ltiuser->id]);
        }
        $affectedltiusersrs->close();

        // Lti savepoint reached.
        upgrade_plugin_savepoint(true, 2022110300, 'enrol', 'lti');
    }

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
