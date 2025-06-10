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
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/plagiarism/turnitin/lib.php');

/**
 * @global moodle_database $DB
 * @param int $oldversion
 * @return bool
 */
function xmldb_plagiarism_turnitin_upgrade($oldversion) {
    global $DB, $CFG;

    $dbman = $DB->get_manager();
    $result = true;

    if ($oldversion < 2013081202) {
        $table = new xmldb_table('plagiarism_turnitin_files');
        $field1 = new xmldb_field('transmatch', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, 0, 'legacyteacher');
        $field2 = new xmldb_field('lastmodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, 0, 'transmatch');
        $field3 = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'lastmodified');
        $field4 = new xmldb_field('submissiontype', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'grade');
        $field5 = new xmldb_field('similarityscore', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'statuscode');

        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }
        if (!$dbman->field_exists($table, $field3)) {
            $dbman->add_field($table, $field3);
        }
        if (!$dbman->field_exists($table, $field4)) {
            $dbman->add_field($table, $field4);
        }
        if (!$dbman->field_exists($table, $field5)) {
            $dbman->add_field($table, $field5);
        } else {
            $dbman->change_field_type($table, $field5);
            $dbman->change_field_default($table, $field5);
        }

        upgrade_dm_successful_uploads();

        upgrade_plugin_savepoint(true, 2013081202, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2014012403) {
        $table = new xmldb_table('plagiarism_turnitin_files');
        $field = new xmldb_field('orcapable', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'submissiontype');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        } else {
            $dbman->change_field_default($table, $field);
        }
        upgrade_plugin_savepoint(true, 2014012403, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2014012405) {
        if ($turnitinsetting = $DB->get_record('config_plugins', array('name' => 'turnitin_use', 'plugin' => 'plagiarism'))) {
            if ($turnitinsetting->value == 1) {
                $supportedmods = array('assign', 'forum', 'workshop');
                foreach ($supportedmods as $mod) {
                    $configfield = new stdClass();
                    $configfield->value = 1;
                    $configfield->plugin = 'plagiarism_turnitin';
                    $configfield->name = 'turnitin_use_mod_'.$mod;
                    if (!$DB->get_record('config_plugins', array('name' => 'turnitin_use_mod_'.$mod))) {
                        $DB->insert_record('config_plugins', $configfield);
                    }
                }
            }
        }
        upgrade_plugin_savepoint(true, 2014012405, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2014012406) {
        $table = new xmldb_table('plagiarism_turnitin_files');
        $field = new xmldb_field('errorcode', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'orcapable');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('errormsg', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'errorcode');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2014012406, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2014012413) {
        // Add new indexes to tables.
        $table = new xmldb_table('plagiarism_turnitin_files');
        $index = new xmldb_index('externalid', XMLDB_INDEX_NOTUNIQUE, array('externalid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Alter certain datatypes incase install was made before install.xml was updated.
        $field1 = new xmldb_field('transmatch', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, 0, 'legacyteacher');
        $field2 = new xmldb_field('lastmodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, 0, 'transmatch');
        $field3 = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'lastmodified');
        $field4 = new xmldb_field('submissiontype', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'grade');
        $field5 = new xmldb_field('similarityscore', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'statuscode');

        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        } else {
            $dbman->change_field_precision($table, $field1);
        }

        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        } else {
            $dbman->change_field_precision($table, $field2);
        }

        if (!$dbman->field_exists($table, $field3)) {
            $dbman->add_field($table, $field3);
        } else {
            $dbman->change_field_precision($table, $field3);
        }

        if (!$dbman->field_exists($table, $field4)) {
            $dbman->add_field($table, $field4);
        } else {
            $dbman->change_field_precision($table, $field4);
        }

        if (!$dbman->field_exists($table, $field5)) {
            $dbman->add_field($table, $field5);
        } else {
            $dbman->change_field_type($table, $field5);
            $dbman->change_field_default($table, $field5);
            $dbman->change_field_precision($table, $field5);
        }

        upgrade_plugin_savepoint(true, 2014012413, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2015040107) {
        upgrade_dm_successful_uploads();
    }

    if ($oldversion < 2015040107) {
        $table = new xmldb_table('plagiarism_turnitin_files');
        $field = new xmldb_field('submitter', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'userid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2015040107, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2015040110) {
        $table = new xmldb_table('plagiarism_turnitin_files');
        $field = new xmldb_field('student_read', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'errormsg');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2015040110, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2016011101) {
        $table = new xmldb_table('plagiarism_turnitin_files');
        $field = new xmldb_field('gm_feedback', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, true, false, 0, 'student_read');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2016011101, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2016011104) {
        $table = new xmldb_table('plagiarism_turnitin_files');
        $field = new xmldb_field('duedate_report_refresh', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, true, false, 0, 'gm_feedback');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2016011104, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2016011105) {
        $table = new xmldb_table('plagiarism_turnitin_config');

        // Drop cm key so that we can modify the field to allow null values.
        $key = new xmldb_key('cm', XMLDB_KEY_FOREIGN, array('cm'), 'course_modules', array('id'));
        $dbman->drop_key($table, $key);

        // Alter cm to allow null values.
        $field = new xmldb_field('cm', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, false, null, null, 'id');
        $dbman->change_field_notnull($table, $field);

        // Update 0 to null for defaults.
        $DB->execute("UPDATE ".$CFG->prefix."plagiarism_turnitin_config SET cm = NULL WHERE cm = 0");

        // Re-add foreign key for cm field.
        $dbman->add_key($table, $key);

        upgrade_plugin_savepoint(true, 2016011105, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2016091402) {
        $table = new xmldb_table('plagiarism_turnitin_files');
        // Remove fields no longer needed.
        $field1 = new xmldb_field('externalstatus');
        $field2 = new xmldb_field('apimd5');
        $field3 = new xmldb_field('legacyteacher');
        if ($dbman->field_exists($table, $field1)) {
            $dbman->drop_field($table, $field1);
        }
        if ($dbman->field_exists($table, $field2)) {
            $dbman->drop_field($table, $field2);
        }
        if ($dbman->field_exists($table, $field3)) {
            $dbman->drop_field($table, $field3);
        }

        // Add new itemid field.
        $field = new xmldb_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'externalid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2016091402, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2017012601) {
        $table = new xmldb_table('plagiarism_turnitin_files');

        // Due to an inconsistency with install and upgrade scripts, some users will
        // have submitter and student_read defaulting to 0 and not allowing null.
        // Alter submitter to allow null values.
        $field = new xmldb_field('submitter', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'userid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        } else {
            $dbman->add_field($table, $field);
        }

        // Alter student_read to allow null values.
        $field = new xmldb_field('student_read', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'errormsg');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        } else {
            $dbman->add_field($table, $field);
        }

        // Update fields to NULL as per default if necessary.
        $DB->set_field('plagiarism_turnitin_files', 'submitter', null, array('submitter' => 0));
        $DB->set_field('plagiarism_turnitin_files', 'student_read', null, array('student_read' => 0));

        // Remove old PP event from the database if it exists.
        $DB->delete_records('task_scheduled', array('component' => 'plagiarism_turnitin', 'classname' => '\plagiarism_turnitin\task\plagiarism_turnitin_task'));

        upgrade_plugin_savepoint(true, 2017012601, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2017013101) {
        // Add new column that has to be unique.
        $table = new xmldb_table('plagiarism_turnitin_config');
        $field = new xmldb_field('config_hash', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'value');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Retrospectively update the new column to be id for previous configs.
        $DB->execute("UPDATE ".$CFG->prefix."plagiarism_turnitin_config SET config_hash = id WHERE config_hash IS NULL");

        // Add hash as key after update.
        $key = new xmldb_key('config_hash', XMLDB_KEY_UNIQUE, array('config_hash'));
        $dbman->add_key($table, $key);

        upgrade_plugin_savepoint(true, 2017013101, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2019031301) {
        // Reset all error code 13s so that we are on a clean slate with the new implementation.
        $DB->execute("UPDATE ".$CFG->prefix."plagiarism_turnitin_files SET statuscode = 'success', errorcode = NULL WHERE errorcode = 13");

        upgrade_plugin_savepoint(true, 2019031301, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2019050201) {
        // If V2 is installed, copy the settings across to PP.
        if ($DB->get_record('config_plugins', array('plugin' => 'mod_turnitintooltwo'))) {
            // Get the settings for the V2 plugin.
            $data = get_config('turnitintooltwo');

            $properties = array("accountid", "secretkey", "apiurl", "enablediagnostic", "usegrademark",
                "enablepeermark", "transmatch", "repositoryoption", "agreement", "enablepseudo", "pseudofirstname",
                "pseudolastname", "lastnamegen", "pseudosalt", "pseudoemaildomain", "useanon");

            foreach ($properties as $property) {
                plagiarism_plugin_turnitin::plagiarism_set_config($data, $property);
            }
        }

        // Define table plagiarism_turnitin_courses to be created.
        $table = new xmldb_table('plagiarism_turnitin_courses');

        // Adding fields to table plagiarism_turnitin_courses.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, null, 'id');
        $table->add_field('ownerid', XMLDB_TYPE_INTEGER, '10', null, false, null, null, 'courseid');
        $table->add_field('turnitin_ctl', XMLDB_TYPE_TEXT, null, null, false, null, null, 'ownerid');
        $table->add_field('turnitin_cid', XMLDB_TYPE_INTEGER, '10', null, false, null, null, 'turnitin_ctl');

        // Adding keys and indexes to table plagiarism_turnitin_courses.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('courseid', XMLDB_INDEX_UNIQUE, array('courseid'));

        // Conditionally launch create table for plagiarism_turnitin_courses.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // If V2 is installed, copy the courses across from V2.
        if ($DB->get_record('config_plugins', array('plugin' => 'mod_turnitintooltwo'))) {
            $ppcourses = $DB->get_records('turnitintooltwo_courses', array('course_type' => 'PP'), 'id ASC', 'courseid, ownerid, turnitin_ctl, turnitin_cid');
            try {
                $DB->insert_records('plagiarism_turnitin_courses', $ppcourses);
            } catch (Exception $e) {
                plagiarism_turnitin_activitylog('Unable to copy course tables during version upgrade because they already exist.', 'PP_UPGRADE');
            }

            // Clean up old data, but only if the number of courses inserted matches the number of courses we wanted to insert.
            if ($DB->count_records('plagiarism_turnitin_courses') == count($ppcourses)) {
                $DB->delete_records('turnitintooltwo_courses', array("course_type" => "PP"));
            }
        }

        // Define table file_conversion to be created.
        $table = new xmldb_table('plagiarism_turnitin_peermark');

        // Adding fields to table plagiarism_turnitin_peermark.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('parent_tii_assign_id', XMLDB_TYPE_INTEGER, '10', null, true, null, null, 'id');
        $table->add_field('title', XMLDB_TYPE_TEXT, null, null, false, null, null, 'parent_tii_assign_id');
        $table->add_field('tiiassignid', XMLDB_TYPE_INTEGER, '10', null, true, null, null, 'title');
        $table->add_field('dtstart', XMLDB_TYPE_INTEGER, '10', null, true, null, null, 'tiiassignid');
        $table->add_field('dtdue', XMLDB_TYPE_INTEGER, '10', null, true, null, null, 'dtstart');
        $table->add_field('dtpost', XMLDB_TYPE_INTEGER, '10', null, true, null, null, 'dtdue');
        $table->add_field('maxmarks', XMLDB_TYPE_INTEGER, '10', null, true, null, null, 'maxmarks');

        // Adding keys and indexes to table plagiarism_turnitin_peermark.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('parent_tii_assign_id', XMLDB_INDEX_NOTUNIQUE, array('parent_tii_assign_id'));
        $table->add_index('tiiassignid', XMLDB_INDEX_NOTUNIQUE, array('tiiassignid'));

        // Conditionally launch create table for plagiarism_turnitin_peermark.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table plagiarism_turnitin_users to be created.
        $table = new xmldb_table('plagiarism_turnitin_users');

        // Adding fields to table plagiarism_turnitin_users.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('turnitin_uid', XMLDB_TYPE_INTEGER, '10', null, false, null, null, 'userid');
        $table->add_field('turnitin_utp', XMLDB_TYPE_INTEGER, '10', null, false, null, 0, 'turnitin_uid');
        $table->add_field('instructor_rubrics', XMLDB_TYPE_TEXT, null, null, false, null, null, 'turnitin_utp');
        $table->add_field('user_agreement_accepted', XMLDB_TYPE_INTEGER, '1', null, false, null, 0, 'instructor_rubrics');

        // Adding keys and indexes to table plagiarism_turnitin_users.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('userid', XMLDB_INDEX_UNIQUE, array('userid'));

        // Conditionally launch create table for plagiarism_turnitin_users.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // If V2 is installed, copy the users across from V2.
        if ($DB->get_record('config_plugins', array('plugin' => 'mod_turnitintooltwo'))) {
            $ppusers = $DB->get_records('turnitintooltwo_users', null, 'id ASC', 'userid, turnitin_uid, turnitin_utp, instructor_rubrics, user_agreement_accepted');
            try {
                $DB->insert_records('plagiarism_turnitin_users', $ppusers);
            } catch (Exception $e) {
                plagiarism_turnitin_activitylog('Unable to copy users table during version upgrade because they already exist.', 'PP_UPGRADE');
            }
        }
        upgrade_plugin_savepoint(true, 2019050201, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2019121719) {
        // Update plagiarism to plagiarism_turnitin for consistency.
        $data = get_config('plagiarism');

        foreach ($data as $key => $value) {
            if ((strpos($key, 'turnitin_') !== false) && ($key != 'turnitin_use')) {
                set_config($key, $value, 'plagiarism_turnitin');
                unset_config($key, 'plagiarism');
            }
        }

        upgrade_plugin_savepoint(true, 2019121719, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2020070801) {
        // Convert plugin _use settings as they are being deprecated.
        $data = get_config('plagiarism');
        $value = (empty($data->turnitin_use)) ? 0 : 1;
        set_config('enabled', $value, 'plagiarism_turnitin');
        // TODO: Delete the turnitin_use setting completely when support for 3.8 is dropped.

        $data = get_config('plagiarism_turnitin');
        foreach ($data as $key => $value) {
            if (strpos($key, 'turnitin_use_') !== false) {
                // Modify key e.g. turnitin_use_mod_assign to plagiarism_turnitin_mod_assign.
                $splitstr = explode("_", $key);
                $newkey = 'plagiarism_turnitin_mod_' . $splitstr[3];
                set_config($newkey, $value, 'plagiarism_turnitin');
                // Remove old key.
                set_config($key, null, 'plagiarism_turnitin');
            }
        }

        upgrade_plugin_savepoint(true, 2020070801, 'plagiarism', 'turnitin');
    }

    // Delete the _use value for post 3.9 environments irrespective of plugin version.
    // TODO: Delete completely when we remove 3.8 support.
    if ($CFG->branch >= 39) {
        set_config('turnitin_use', null, 'plagiarism');
    }

    // This block is to solve a number of inconsistencies between the install and upgrade scripts
    if ($oldversion < 2020091401) {

        // Set itemid to default to null
        $table = new xmldb_table('plagiarism_turnitin_files');
        $field = new xmldb_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, false, false, null, 'externalid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
        }

        // Set student_read to default to null
        $field = new xmldb_field('student_read', XMLDB_TYPE_INTEGER, '10', false, false, false, null, 'errormsg');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
        }

        // Set turnitin_uid to allow null
        $table = new xmldb_table('plagiarism_turnitin_users');
        $field = new xmldb_field('turnitin_uid', XMLDB_TYPE_INTEGER, '10', null, false, false, null, 'userid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        }

        // Set turnitin_utp to allow null
        $field = new xmldb_field('turnitin_utp', XMLDB_TYPE_INTEGER, '10', null, false, false, 0, 'turnitin_uid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        }

        // Set turnitin_ctl to allow null
        $table = new xmldb_table('plagiarism_turnitin_courses');
        $field = new xmldb_field('turnitin_ctl', XMLDB_TYPE_TEXT, null, null, false, null, null, 'ownerid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        }

        // Set turnitin_cid to allow null
        $field = new xmldb_field('turnitin_cid', XMLDB_TYPE_INTEGER, '10', null, false, null, null, 'turnitin_ctl');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        }

        upgrade_plugin_savepoint(true, 2020091401, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2021081301) {

        $table = new xmldb_table('plagiarism_turnitin_courses');
        $field = new xmldb_field('ownerid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2021081301, 'plagiarism', 'turnitin');
    }

    if ($oldversion < 2022072501) {
        $table = new xmldb_table('plagiarism_turnitin_config');
        $index = new xmldb_index('config_hash', XMLDB_INDEX_UNIQUE, array('config_hash'));
        $field = new xmldb_field('config_hash', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'value');
        if ($dbman->field_exists($table, $field)) {
            // Double-check that there is no records with a NULL value.
            if ($DB->count_records_select('plagiarism_turnitin_config', 'config_hash IS NULL') == 0) {
                // Delete index if exists.
                if ($dbman->index_exists($table, $index)) {
                    $dbman->drop_index($table, $index);
                }
                // Set config_hash to be NOT NULL, also set precision 255.
                $dbman->change_field_notnull($table, $field);
            }
        }

        // Set userid to be NOT NULL.
        $table = new xmldb_table('plagiarism_turnitin_users');
        $index = new xmldb_index('userid', XMLDB_INDEX_UNIQUE, array('userid'));
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        if ($dbman->field_exists($table, $field)) {
            // Double-check that there is no records with a NULL value.
            if ($DB->count_records_select('plagiarism_turnitin_users', 'userid IS NULL') == 0) {
                // Drop and then recreate unique index, otherwise Moodle will throw dependency exception.
                if ($dbman->index_exists($table, $index)) {
                    $dbman->drop_index($table, $index);
                }
                $dbman->change_field_notnull($table, $field);
                $dbman->add_index($table, $index);
            }
        }

        // Set courseid to be NOT NULL.
        $table = new xmldb_table('plagiarism_turnitin_courses');
        $index = new xmldb_index('courseid', XMLDB_INDEX_UNIQUE, array('courseid'));
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        if ($dbman->field_exists($table, $field)) {
            // Double-check that there is no records with a NULL value.
            if ($DB->count_records_select('plagiarism_turnitin_courses', 'courseid IS NULL') == 0) {
                // Drop and then recreate unique index, otherwise Moodle will throw dependency exception.
                if ($dbman->index_exists($table, $index)) {
                    $dbman->drop_index($table, $index);
                }
                $dbman->change_field_notnull($table, $field);
                $dbman->add_index($table, $index);
            }
        }

        upgrade_plugin_savepoint(true, 2022072501, 'plagiarism', 'turnitin');
    }

    return $result;
}

function upgrade_dm_successful_uploads() {
    global $DB, $CFG;

    // Update successful submissions from Dan Marsden's plugin with incorrect statuscode.
    $DB->execute("UPDATE ".$CFG->prefix."plagiarism_turnitin_files SET statuscode = 'success',
        lastmodified = ".time()." WHERE statuscode = '51'");

    // Update the lastmodified timestamp from all successful submissions from Dan Marsden's plugin.
    $DB->execute("UPDATE ".$CFG->prefix."plagiarism_turnitin_files SET lastmodified = ".time()."
        WHERE statuscode = 'success' AND lastmodified = 0");

    // Update error codes with submissions from Dan Marsden's plugin.
    $DB->execute("UPDATE ".$CFG->prefix."plagiarism_turnitin_files SET statuscode = 'error'
        WHERE statuscode != 'success' AND statuscode != 'pending'");
}
