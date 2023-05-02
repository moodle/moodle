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
 * Plugin upgrade script.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

use local_o365\utils;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/o365/lib.php');

/**
 * Update plugin.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_local_o365_upgrade($oldversion) {
    global $DB, $USER, $SITE;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014111700) {
        if (!$dbman->table_exists('local_o365_token')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_token');
        }
        upgrade_plugin_savepoint(true, '2014111700', 'local', 'o365');
    }

    if ($oldversion < 2014111702) {
        if (!$dbman->table_exists('local_o365_calsub')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_calsub');
        }
        if (!$dbman->table_exists('local_o365_calidmap')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_calidmap');
        }
        upgrade_plugin_savepoint(true, '2014111702', 'local', 'o365');
    }

    if ($oldversion < 2014111703) {
        $table = new xmldb_table('local_o365_calidmap');
        $field = new xmldb_field('outlookeventid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'eventid');
        $dbman->change_field_type($table, $field);
        upgrade_plugin_savepoint(true, '2014111703', 'local', 'o365');
    }

    if ($oldversion < 2014111707) {
        if (!$dbman->table_exists('local_o365_cronqueue')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_cronqueue');
        }
        upgrade_plugin_savepoint(true, '2014111707', 'local', 'o365');
    }

    if ($oldversion < 2014111710) {
        if (!$dbman->table_exists('local_o365_coursespsite')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_coursespsite');
        }
        if (!$dbman->table_exists('local_o365_spgroupdata')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_spgroupdata');
        }
        if (!$dbman->table_exists('local_o365_aaduserdata')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_aaduserdata');
        }
        upgrade_plugin_savepoint(true, '2014111710', 'local', 'o365');
    }

    if ($oldversion < 2014111711) {
        $table = new xmldb_table('local_o365_spgroupdata');
        $field = new xmldb_field('permtype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'grouptitle');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, '2014111711', 'local', 'o365');
    }

    if ($oldversion < 2014111715) {
        if (!$dbman->table_exists('local_o365_spgroupassign')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_spgroupassign');
        }
        upgrade_plugin_savepoint(true, '2014111715', 'local', 'o365');
    }

    if ($oldversion < 2014111716) {
        // Drop old index.
        $table = new xmldb_table('local_o365_token');
        $index = new xmldb_index('usrresscp', XMLDB_INDEX_NOTUNIQUE, ['user_id', 'resource', 'scope']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Lengthen field.
        $table = new xmldb_table('local_o365_token');
        $field = new xmldb_field('scope', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'user_id');
        $dbman->change_field_type($table, $field);

        // Create new index.
        $table = new xmldb_table('local_o365_token');
        $index = new xmldb_index('usrres', XMLDB_INDEX_NOTUNIQUE, ['user_id', 'resource']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, '2014111716', 'local', 'o365');
    }

    if ($oldversion < 2015012702) {
        // Migrate settings.
        $config = get_config('local_o365');
        if (empty($config->sharepointlink) && isset($config->tenant) && isset($config->parentsiteuri)) {
            $sharepointlink = 'https://'.$config->tenant.'.sharepoint.com/'.$config->parentsiteuri;
            set_config('sharepointlink', $sharepointlink, 'local_o365');
        }
        upgrade_plugin_savepoint(true, '2015012702', 'local', 'o365');
    }

    if ($oldversion < 2015012704) {
        $config = get_config('local_o365');
        if (!empty($config->tenant)) {
            set_config('aadtenant', $config->tenant.'.onmicrosoft.com', 'local_o365');
            set_config('odburl', $config->tenant.'-my.sharepoint.com', 'local_o365');
        }
        upgrade_plugin_savepoint(true, '2015012704', 'local', 'o365');
    }

    if ($oldversion < 2015012707) {
        $table = new xmldb_table('local_o365_calsub');
        $field = new xmldb_field('o365calid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'caltypeid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('syncbehav', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'o365calid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, '2015012707', 'local', 'o365');
    }

    if ($oldversion < 2015012708) {
        $table = new xmldb_table('local_o365_calidmap');
        $field = new xmldb_field('origin', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'outlookeventid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $idmaps = $DB->get_recordset('local_o365_calidmap');
        foreach ($idmaps as $idmap) {
            $newidmap = new \stdClass;
            $newidmap->id = $idmap->id;
            if (empty($idmap->origin)) {
                $newidmap->origin = 'moodle';
                $DB->update_record('local_o365_calidmap', $newidmap);
            }
        }
        $idmaps->close();

        upgrade_plugin_savepoint(true, '2015012708', 'local', 'o365');
    }

    if ($oldversion < 2015012709) {
        $table = new xmldb_table('local_o365_calidmap');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'origin');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('local_o365_calsub');
        $field = new xmldb_field('isprimary', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'o365calid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Update the calidmap with the user id of the user who created the event. Before multi-cal syncing, the o365 event was
        // always created in the calendar of the event creating user (with others as attendees).
        $sql = 'SELECT idmap.id as idmapid,
                       ev.userid as eventuserid
                  FROM {local_o365_calidmap} idmap
                  JOIN {event} ev ON idmap.eventid = ev.id';
        $idmaps = $DB->get_recordset_sql($sql);
        foreach ($idmaps as $idmap) {
            $newidmap = new \stdClass;
            if (empty($idmap->userid)) {
                $newidmap->id = $idmap->idmapid;
                $newidmap->userid = $idmap->eventuserid;
                $DB->update_record('local_o365_calidmap', $newidmap);
            }
        }
        $idmaps->close();

        upgrade_plugin_savepoint(true, '2015012709', 'local', 'o365');
    }

    if ($oldversion < 2015012710) {
        $calsubs = $DB->get_recordset('local_o365_calsub');
        foreach ($calsubs as $i => $calsub) {
            if (empty($calsub->syncbehav)) {
                $newcalsub = new \stdClass;
                $newcalsub->id = $calsub->id;
                $newcalsub->syncbehav = 'out';
                $DB->update_record('local_o365_calsub', $newcalsub);
            }
        }
        $calsubs->close();
        upgrade_plugin_savepoint(true, '2015012710', 'local', 'o365');
    }

    if ($oldversion < 2015012712) {
        // Lengthen field.
        $table = new xmldb_table('local_o365_token');
        $field = new xmldb_field('scope', XMLDB_TYPE_TEXT, null, null, null, null, null, 'user_id');
        $dbman->change_field_type($table, $field);
        upgrade_plugin_savepoint(true, '2015012712', 'local', 'o365');
    }

    if ($oldversion < 2015012713) {
        if (!$dbman->table_exists('local_o365_objects')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_objects');
        }
        upgrade_plugin_savepoint(true, '2015012713', 'local', 'o365');
    }

    if ($oldversion < 2015012714) {
        $table = new xmldb_table('local_o365_objects');
        $field = new xmldb_field('subtype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'type');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, '2015012714', 'local', 'o365');
    }

    if ($oldversion < 2015012715) {
        // Lengthen field.
        $table = new xmldb_table('local_o365_token');
        $field = new xmldb_field('scope', XMLDB_TYPE_TEXT, null, null, null, null, null, 'user_id');
        $dbman->change_field_type($table, $field);
        upgrade_plugin_savepoint(true, '2015012715', 'local', 'o365');
    }

    if ($oldversion < 2015060102) {
        $usersync = get_config('local_o365', 'aadsync');
        if ($usersync === '1') {
            set_config('aadsync', 'create', 'local_o365');
        }
        upgrade_plugin_savepoint(true, '2015060102', 'local', 'o365');
    }

    if ($oldversion < 2015060103) {
        if (!$dbman->table_exists('local_o365_connections')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_connections');
        }
        upgrade_plugin_savepoint(true, '2015060103', 'local', 'o365');
    }

    if ($oldversion < 2015060104) {
        $table = new xmldb_table('local_o365_connections');
        $field = new xmldb_field('uselogin', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'aadupn');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, '2015060104', 'local', 'o365');
    }

    if ($oldversion < 2015060109) {
        if ($dbman->table_exists('local_o365_aaduserdata')) {
            $now = time();
            $aaduserdatars = $DB->get_recordset('local_o365_aaduserdata');
            foreach ($aaduserdatars as $aaduserdatarec) {
                $objectrec = (object)[
                    'type' => 'user',
                    'subtype' => '',
                    'objectid' => $aaduserdatarec->objectid,
                    'moodleid' => $aaduserdatarec->muserid,
                    'o365name' => $aaduserdatarec->userupn,
                    'timecreated' => $now,
                    'timemodified' => $now,
                ];
                $objectrec->id = $DB->insert_record('local_o365_objects', $objectrec);
                if (!empty($objectrec->id)) {
                    $DB->delete_records('local_o365_aaduserdata', ['id' => $aaduserdatarec->id]);
                }
            }
        }
        upgrade_plugin_savepoint(true, '2015060109', 'local', 'o365');
    }

    if ($oldversion < 2015060111) {
        // Clean up old "calendarsyncin" task record, if present. Replaced by \local_o365\feature\calsync\task\importfromoutlook.
        $conditions = ['component' => 'local_o365', 'classname' => '\local_o365\task\calendarsyncin'];
        $DB->delete_records('task_scheduled', $conditions);
        upgrade_plugin_savepoint(true, '2015060111', 'local', 'o365');
    }

    if ($oldversion < 2015111900.01) {
        $authoidcversion = get_config('auth_oidc', 'version');
        if ($authoidcversion && $authoidcversion < 2020110905) {
            $fieldmapconfig = $DB->get_record('config_plugins', ['plugin' => 'local_o365', 'name' => 'fieldmap']);
            if (empty($fieldmapconfig)) {
                $fieldmapdefault = [
                    'givenName/firstname/always',
                    'surname/lastname/always',
                    'mail/email/always',
                    'city/city/always',
                    'country/country/always',
                    'department/department/always',
                    'preferredLanguage/lang/always',
                ];
                set_config('fieldmap', serialize($fieldmapdefault), 'local_o365');
            }
        }

        upgrade_plugin_savepoint(true, '2015111900.01', 'local', 'o365');
    }

    if ($oldversion < 2015111900.02) {
        $table = new xmldb_table('local_o365_appassign');
        if (!$dbman->table_exists($table)) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_appassign');
        }
        upgrade_plugin_savepoint(true, '2015111900.02', 'local', 'o365');
    }

    if ($oldversion < 2015111901.01) {
        $table = new xmldb_table('local_o365_matchqueue');
        if (!$dbman->table_exists($table)) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_matchqueue');
        }
        upgrade_plugin_savepoint(true, '2015111901.01', 'local', 'o365');
    }

    if ($oldversion < 2015111901.03) {
        // Create new indexes.
        $table = new xmldb_table('auth_oidc_token');
        $index = new xmldb_index('oidcusername', XMLDB_INDEX_NOTUNIQUE, ['oidcusername']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $table = new xmldb_table('local_o365_matchqueue');
        $index = new xmldb_index('completed', XMLDB_INDEX_NOTUNIQUE, ['completed']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $index = new xmldb_index('o365username', XMLDB_INDEX_NOTUNIQUE, ['o365username']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_plugin_savepoint(true, '2015111901.03', 'local', 'o365');
    }

    if ($oldversion < 2015111903) {
        $table = new xmldb_table('local_o365_appassign');
        $index = new xmldb_index('userobjectid', XMLDB_INDEX_UNIQUE, array('userobjectid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        $field = new xmldb_field('userobjectid', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'muserid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        $field = new xmldb_field('photoid', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'assigned');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('photoupdated', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'photoid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, '2015111903', 'local', 'o365');
    }

    if ($oldversion < 2015111905) {
        // Delete custom profile fields for data type o365 and oidc which are no longer used.
        $fields = $DB->get_records_sql("SELECT * FROM {user_info_field} WHERE datatype IN ('o365', 'oidc')");
        foreach ($fields as $field) {
            $DB->delete_records('user_info_data', array('fieldid' => $field->id));
            $DB->delete_records('user_info_field', array('id' => $field->id));
        }
        upgrade_plugin_savepoint(true, '2015111905', 'local', 'o365');
    }

    if ($oldversion < 2015111911.01) {
        $table = new xmldb_table('local_o365_appassign');
        $field = new xmldb_field('photoupdated', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'photoid');
        if ($dbman->field_exists($table, $field)) {
            $field->setNotNull(false);
            $dbman->change_field_notnull($table, $field);
        }
        upgrade_plugin_savepoint(true, '2015111911.01', 'local', 'o365');
    }

    if ($oldversion < 2015111913) {
        if (!$dbman->table_exists('local_o365_coursegroupdata')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'local_o365_coursegroupdata');
        }
        upgrade_plugin_savepoint(true, '2015111913', 'local', 'o365');
    }

    if ($oldversion < 2015111913.02) {
        $config = get_config('local_o365');
        if (!empty($config->creategroups)) {
            set_config('creategroups', 'onall', 'local_o365');
        }
        upgrade_plugin_savepoint(true, '2015111913.02', 'local', 'o365');
    }

    if ($oldversion < 2015111914.02) {
        // Define field openidconnect to be added to local_o365_matchqueue.
        $table = new xmldb_table('local_o365_matchqueue');
        $field = new xmldb_field('openidconnect', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'o365username');

        // Conditionally launch add field openidconnect.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, '2015111914.02', 'local', 'o365');
    }

    if ($oldversion < 2015111914.03) {
        // Drop index.
        $table = new xmldb_table('local_o365_token');
        $index = new xmldb_index('user', XMLDB_INDEX_NOTUNIQUE, ['user_id']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $table = new xmldb_table('local_o365_calsub');
        $index = new xmldb_index('user', XMLDB_INDEX_NOTUNIQUE, ['user_id']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        upgrade_plugin_savepoint(true, '2015111914.03', 'local', 'o365');
    }

    if ($oldversion < 2015111916.01) {
        // Define table local_o365_calsettings to be created.
        $table = new xmldb_table('local_o365_calsettings');

        // Adding fields to table local_o365_calsettings.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('o365calid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_o365_calsettings.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_o365_calsettings.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $existingsubsrs = $DB->get_recordset('local_o365_calsub', ['user_id' => $USER->id]);
        if ($existingsubsrs->valid()) {
            // Create new outlook calender with site name.
            $calsync = new \local_o365\feature\calsync\main();
            $sitecalendar = $calsync->create_outlook_calendar($SITE->fullname);

            // Determine outlook calendar setting check.
            $usersetting = $DB->get_record('local_o365_calsettings', ['user_id' => $USER->id]);
            if (empty($usersetting)) {
                $newsetting = [
                        'user_id' => $USER->id,
                        'o365calid' => $sitecalendar['Id'],
                        'timecreated' => time()
                ];
                $newsetting['id'] = $DB->insert_record('local_o365_calsettings', (object)$newsetting);
            }
        }

        upgrade_plugin_savepoint(true, '2015111916.01', 'local', 'o365');
    }

    if ($oldversion < 2016062000.01) {
        set_config('sharepointcourseselect', 'off', 'local_o365');
        upgrade_plugin_savepoint(true, '2016062000.01', 'local', 'o365');
    }

    if ($oldversion < 2016062000.02) {
        // MSFTMPP-497: new capabilites split from auth_oidc:manageconnection* capabilities.
        if (get_config('local_o365', 'initconnectioncaps') === false) {
            set_config('initconnectioncaps', 'upgraded', 'local_o365');
            $caps = [
                'auth/oidc:manageconnection' => ['local/o365:manageconnectionlink', 'local/o365:manageconnectionunlink'],
                'auth/oidc:manageconnectionconnect' => ['local/o365:manageconnectionlink'],
                'auth/oidc:manageconnectiondisconnect' => ['local/o365:manageconnectionunlink']
            ];
            foreach ($caps as $cap => $addcaps) {
                $roles = get_roles_with_capability($cap, CAP_ALLOW);
                foreach ($roles as $role) {
                    $rolecaps = $DB->get_recordset('role_capabilities', ['roleid' => $role->id, 'capability' => $cap]);
                    foreach ($rolecaps as $rolecap) {
                        $newrolecap = $rolecap;
                        unset($newrolecap->id);
                        foreach ($addcaps as $addcap) {
                            if (!$DB->record_exists('role_capabilities', ['roleid' => $role->id,
                                'capability' => $addcap, 'contextid' => $newrolecap->contextid])) {
                                $newrolecap->capability = $addcap;
                                $DB->insert_record('role_capabilities', $newrolecap);
                            }
                        }
                    }
                    unset($rolecaps);
                }
            }
        }
        upgrade_plugin_savepoint(true, 2016062000.02, 'local', 'o365');
    }

    if ($oldversion < 2016062000.03) {
        if ($dbman->table_exists('local_o365_coursegroupdata')) {
            $table = new xmldb_table('local_o365_coursegroupdata');
            $field = new xmldb_field('classnotebook', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', null);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
        upgrade_plugin_savepoint(true, '2016062000.03', 'local', 'o365');
    }

    if ($oldversion < 2016062001.01) {
        $sharepointcourseselect = get_config('local_o365', 'sharepointcourseselect');
        // Setting value "Off" used to mean "sync all".
        if ($sharepointcourseselect === 'off') {
            set_config('sharepointcourseselect', 'onall', 'local_o365');
        } else if (empty($sharepointcourseselect) || $sharepointcourseselect !== 'oncustom') {
            set_config('sharepointcourseselect', 'none', 'local_o365');
        }
        upgrade_plugin_savepoint(true, '2016062001.01', 'local', 'o365');
    }

    if ($oldversion < 2016062004.01) {
        $enableunifiedapi = get_config('local_o365', 'enableunifiedapi');
        if (empty($enableunifiedapi)) {
            set_config('disablegraphapi', 1, 'local_o365');
        } else {
            set_config('disablegraphapi', 0, 'local_o365');
        }
        upgrade_plugin_savepoint(true, '2016062004.01', 'local', 'o365');
    }

    if ($oldversion < 2016120500.05) {
        if ($dbman->table_exists('local_o365_objects')) {
            $table = new xmldb_table('local_o365_objects');
            $field = new xmldb_field('tenant', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'o365name');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
        upgrade_plugin_savepoint(true, '2016120500.05', 'local', 'o365');
    }

    if ($oldversion < 2016120500.06) {
        if ($dbman->table_exists('local_o365_objects')) {
            $table = new xmldb_table('local_o365_objects');
            $field = new xmldb_field('metadata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'tenant');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
        upgrade_plugin_savepoint(true, '2016120500.06', 'local', 'o365');
    }

    if ($oldversion < 2017111301) {
        mtrace('Warning! This version removes the legacy Microsoft 365 API.');
        upgrade_plugin_savepoint(true, '2017111301', 'local', 'o365');
    }

    if ($oldversion < 2018051702) {
        $coursesyncsetting = get_config('local_o365', 'creategroups');
        set_config('createteams', $coursesyncsetting, 'local_o365');
        upgrade_plugin_savepoint(true, '2018051702', 'local', 'o365');
    }

    if ($oldversion < 2018051703) {
        if (!$dbman->table_exists('local_o365_notif')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__ . '/install.xml', 'local_o365_notif');
        }

        upgrade_plugin_savepoint(true, '2018051703', 'local', 'o365');
    }

    if ($oldversion < 2018051704) {
        $botappid = get_config('local_o365', 'bot_app_id');
        $botapppassword = get_config('local_o365', 'bot_app_password');
        $botwebhookendpoint = get_config('local_o365', 'bot_webhook_endpoint');
        if ($botappid && $botapppassword && $botwebhookendpoint) {
            set_config('bot_feature_enabled', '1', 'local_o365');
        } else {
            set_config('bot_feature_enabled', '0', 'local_o365');
        }
        upgrade_plugin_savepoint(true, '2018051704', 'local', 'o365');
    }

    if ($oldversion < 2018051705) {
        local_o365_check_sharedsecret();
        upgrade_plugin_savepoint(true, '2018051705', 'local', 'o365');
    }

    if ($oldversion < 2020020302) {
        $aadsyncsetting = get_config('local_o365', 'aadsync');
        if ($aadsyncsetting !== false) {
            if (strpos($aadsyncsetting, 'delete') === 0) {
                set_config('aadsync', substr($aadsyncsetting, 7), 'local_o365');
            } else if (strpos($aadsyncsetting, 'nodelta') === 0) {
                set_config('aadsync', substr($aadsyncsetting, 8), 'local_o365');
            }
        }

        upgrade_plugin_savepoint(true, 2020020302, 'local', 'o365');
    }

    if ($oldversion < 2020071503) {
        $authoidcversion = get_config('auth_oidc', 'version');
        if ($authoidcversion && $authoidcversion < 2020110905) {
            $fieldmapsettings = get_config('local_o365', 'fieldmap');
            if ($fieldmapsettings !== false) {
                $fieldmapsettings = unserialize($fieldmapsettings);
                foreach ($fieldmapsettings as $key => $setting) {
                    $fieldmapsettings[$key] = str_replace('facsimileTelephoneNumber', 'faxNumber', $setting);
                }
                set_config('fieldmap', serialize($fieldmapsettings), 'local_o365');
            }
        }

        upgrade_plugin_savepoint(true, 2020071503, 'local', 'o365');
    }

    if ($oldversion < 2020071504) {
        // Delete delta token, purge cache.
        unset_config('task_usersync_lastdeltatoken', 'local_o365');
        purge_all_caches();

        upgrade_plugin_savepoint(true, 2020071504, 'local', 'o365');
    }

    if ($oldversion < 2020110901) {
        // Part 1: create local_o365_teams_cache table.
        if (!$dbman->table_exists('local_o365_teams_cache')) {
            // Define table local_o365_teams_cache to be created.
            $table = new xmldb_table('local_o365_teams_cache');

            // Adding fields to table local_o365_teams_cache.
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('objectid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
            $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
            $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
            $table->add_field('url', XMLDB_TYPE_TEXT, null, null, null, null, null);

            // Adding keys to table local_o365_teams_cache.
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

            // Conditionally launch create table for local_o365_teams_cache.
            if (!$dbman->table_exists($table)) {
                $dbman->create_table($table);
            }
        }

        // Part 2: rename resource field.
        if ($dbman->field_exists('local_o365_token', 'resource')) {
            $table = new xmldb_table('local_o365_token');

            // Define index usrresscp (not unique) to be dropped form local_o365_token.
            $index = new xmldb_index('usrresscp', XMLDB_INDEX_NOTUNIQUE, ['user_id', 'resource']);

            // Conditionally launch drop index usrresscp.
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }

            // Rename field resource on table local_o365_token to tokenresource.
            $field = new xmldb_field('resource', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, null, 'scope');

            // Launch rename field resource.
            $dbman->rename_field($table, $field, 'tokenresource');

            // Define index usrresscp (not unique) to be added to local_o365_token.
            $index = new xmldb_index('usrresscp', XMLDB_INDEX_NOTUNIQUE, ['user_id', 'tokenresource']);

            // Conditionally launch add index usrresscp.
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        // Update apptokens config.
        $apptokensconfig = get_config('local_o365', 'apptokens');
        if ($apptokensconfig !== false) {
            $apptokensconfig = unserialize($apptokensconfig);
            foreach ($apptokensconfig as $resource => $tokenconfig) {
                if (array_key_exists('resource', $tokenconfig)) {
                    $apptokensconfig[$resource]['tokenresource'] = $tokenconfig['resource'];
                    unset($apptokensconfig[$resource]['resource']);
                }
            }
            $apptokensconfig = serialize($apptokensconfig);
            set_config('apptokens', $apptokensconfig, 'local_o365');
        }

        // Update systemtokens config.
        $systemtokensconfig = get_config('local_o365', 'systemtokens');
        if ($systemtokensconfig !== false) {
            $systemtokensconfig = unserialize($systemtokensconfig);
            foreach ($systemtokensconfig as $resource => $tokenconfig) {
                if (array_key_exists('resource', $tokenconfig)) {
                    $systemtokensconfig[$resource]['tokenresource'] = $tokenconfig['resource'];
                    unset($systemtokensconfig[$resource]['resource']);
                }
            }
            $systemtokensconfig = serialize($systemtokensconfig);
            set_config('systemtokens', $systemtokensconfig, 'local_o365');
        }

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, 2020110901, 'local', 'o365');
    }

    if ($oldversion < 2020110902) {
        // Update aadsync settings to replace 'delete' with 'suspend'.
        $aadsyncsetting = get_config('local_o365', 'aadsync');
        set_config('aadsync', str_replace('delete', 'suspend', $aadsyncsetting), 'local_o365');

        // Force clear user sync delta token.
        unset_config('local_o365', 'task_usersync_lastdeltatoken');
        purge_all_caches();

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, 2020110902, 'local', 'o365');
    }

    if ($oldversion < 2021051712) {
        // Update multi tenants setting.
        utils::updatemultitenantssettings();

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, 2021051712, 'local', 'o365');
    }

    if ($oldversion < 2021051713) {
        // Update "task_usersync_lastdelete" setting from timestamp to YYYYMMDD.
        set_config('task_usersync_lastdelete', date('Ymd', strtotime('yesterday')), 'local_o365');

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, 2021051713, 'local', 'o365');
    }

    if ($oldversion < 2021051714) {
        // Clean up SDS sync records.
        local_o365\feature\sds\task\sync::clean_up_sds_sync_records();

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, 2021051714, 'local', 'o365');
    }

    if ($oldversion < 2021051715) {
        // Remove duplicate entries for users in local_o365_objects.
        $sql = "SELECT DISTINCT(a.id)
                  FROM {local_o365_objects} a
                  JOIN {local_o365_objects} b ON b.moodleid = a.moodleid AND a.o365name = b.o365name AND a.objectid = b.objectid
                 WHERE a.type = :user1
                   AND b.type = :user2
                   AND a.id > b.id";
        $duplicateentries = $DB->get_fieldset_sql($sql, ['user1' => 'user', 'user2' => 'user']);

        if ($duplicateentries) {
            $DB->delete_records_list('local_o365_objects', 'id', $duplicateentries);
        }

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, 2021051715, 'local', 'o365');
    }

    if ($oldversion < 2021051717) {
        // Reset last calendar sync run task.
        set_config('calsyncinlastrun', 0, 'local_o365');

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, 2021051717, 'local', 'o365');
    }

    if ($oldversion < 2021051718) {
        $pluginsettings = get_config('local_o365');

        // Delete local_o365_coursegroupdata / local_o365_groupdata table.
        $table = new xmldb_table('local_o365_coursegroupdata');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $table = new xmldb_table('local_o365_groupdata');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Rename "createteams" to "coursesync".
        if (isset($pluginsettings->createteams)) {
            if (!isset($pluginsettings->coursesync)) {
                set_config('coursesync', $pluginsettings->createteams, 'local_o365');
            }
            unset_config('createteams', 'local_o365');
        }

        // Rename "usergroupcustom" to "coursesynccustom".
        if (isset($pluginsettings->usergroupcustom)) {
            if (!isset($pluginsettings->coursesynccustom)) {
                set_config('coursesynccustom', $pluginsettings->usergroupcustom, 'local_o365');
            }
            unset_config('usergroupcustom', 'local_o365');
        }

        // Temporarily rename "usergroupcustomfeatures" to "coursesynccustomfeatures" - to be deleted.
        if (isset($pluginsettings->usergroupcustomfeatures)) {
            if (!isset($pluginsettings->coursesynccustomfeatures)) {
                set_config('coursesynccustomfeatures', $pluginsettings->usergroupcustomfeatures, 'local_o365');
            }
            unset_config('usergroupcustomfeatures', 'local_o365');
        }

        // Rename "createteams_per_course" to "course_sync_per_course".
        if (isset($pluginsettings->createteams_per_course)) {
            if (!isset($pluginsettings->course_sync_per_course)) {
                set_config('course_sync_per_course', $pluginsettings->createteams_per_course, 'local_o365');
            }
            unset_config('createteams_per_course', 'local_o365');
        }

        // Delete setting "coursesynccustomfeatures".
        if (isset($pluginsettings->coursesynccustomfeatures)) {
            unset_config('coursesynccustomfeatures', 'local_o365');
        }

        // Delete setting "group_creation_fallback".
        if (isset($pluginsettings->group_creation_fallback)) {
            unset_config('group_creation_fallback', 'local_o365');
        }

        // Delete setting "prefer_class_team".
        if (isset($pluginsettings->prefer_class_team)) {
            unset_config('prefer_class_team', 'local_o365');
        }

        // If the tenant has education license, stamp with class details.
        if (!isset($pluginsettings->education_group_params_set)) {
            set_config('education_group_params_set', time(), 'local_o365');
            local_o365\feature\coursesync\utils::migrate_existing_groups();
        }

        // Define field locked to be added to local_o365_teams_cache.
        $table = new xmldb_table('local_o365_teams_cache');
        $field = new xmldb_field('locked', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'url');

        // Conditionally launch add field locked.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, 2021051718, 'local', 'o365');
    }

    if ($oldversion < 2022041901) {
        // Set default user sync suspension feature schedule.
        local_o365_set_default_user_sync_suspension_feature_schedule();

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, 2022041901, 'local', 'o365');
    }

    if ($oldversion < 2022041906) {
        // Remove SharePoint feature and settings.
        unset_config('sharepointlink', 'local_o365');
        unset_config('sharepointcourseselect', 'local_o365');

        // Define table local_o365_spgroupdata to be dropped.
        $table = new xmldb_table('local_o365_spgroupdata');

        // Conditionally launch drop table for local_o365_spgroupdata.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_o365_spgroupassign to be dropped.
        $table = new xmldb_table('local_o365_spgroupassign');

        // Conditionally launch drop table for local_o365_spgroupassign.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table local_o365_coursespsite to be dropped.
        $table = new xmldb_table('local_o365_coursespsite');

        // Conditionally launch drop table for local_o365_coursespsite.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // O365 savepoint reached.
        upgrade_plugin_savepoint(true, 2022041906, 'local', 'o365');
    }

    return true;
}
