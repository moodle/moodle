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
 * Scripts used for upgrading database when upgrading block from an older version.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016 with contributions from Spenser Jones (sjones@ambrose.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrades Panopto for xmldb.
 *
 * @param int $oldversion the previous version Panopto is being upgraded from
 */
function xmldb_block_panopto_upgrade($oldversion = 0) {
    global $CFG, $DB, $USER;
    $dbman = $DB->get_manager();

    if ($oldversion < 2014121502) {

        // Add db fields for servername and application key per course.
        if (isset($CFG->block_panopto_server_name)) {
            $oldservername = $CFG->block_panopto_server_name;
        }
        if (isset($CFG->block_panopto_application_key)) {
            $oldappkey = $CFG->block_panopto_application_key;
        }

        // Define field panopto_server to be added to block_panopto_foldermap.
        $table = new xmldb_table('block_panopto_foldermap');
        $field = new xmldb_field('panopto_server', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'panopto_id');

        // Conditionally launch add field panopto_server.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            if (isset($oldservername)) {
                $DB->set_field('block_panopto_foldermap', 'panopto_server', $oldservername, null);
            }
        }

        // Define field panopto_app_key to be added to block_panopto_foldermap.
        $table = new xmldb_table('block_panopto_foldermap');
        $field = new xmldb_field('panopto_app_key', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'panopto_server');

        // Conditionally launch add field panopto_app_key.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            if (isset($oldappkey)) {
                $DB->set_field('block_panopto_foldermap', 'panopto_app_key', $oldappkey, null);
            }
        }

        // Panopto savepoint reached.
        upgrade_block_savepoint(true, 2014121502, 'panopto');
    }

    if ($oldversion < 2015012901) {

        // Define field publisher_mapping to be added to block_panopto_foldermap.
        $table = new xmldb_table('block_panopto_foldermap');
        $field = new xmldb_field('publisher_mapping', XMLDB_TYPE_CHAR, '20', null, null, null, '1', 'panopto_app_key');

        // Conditionally launch add field publisher_mapping.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field creator_mapping to be added to block_panopto_foldermap.
        $table = new xmldb_table('block_panopto_foldermap');
        $field = new xmldb_field('creator_mapping', XMLDB_TYPE_CHAR, '20', null, null, null, '3,4', 'publisher_mapping');

        // Conditionally launch add field creator_mapping.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Panopto savepoint reached.
        upgrade_block_savepoint(true, 2015012901, 'panopto');
    }

    if ($oldversion < 2016101227) {
        // Move block global settings to <prefix>_config_plugin table.
        // First, move each server configuration. We are not relying here on
        // block_panopto_server_number to determine number of servers, as there
        // could be more. Moving all that we will find in order not to leave
        // any abandoned config values in global configuration.
        for ($x = 1; $x <= 10; $x++) {
            if (isset($CFG->{'block_panopto_server_name' . $x})) {
                set_config('server_name' . $x, $CFG->{'block_panopto_server_name' . $x}, 'block_panopto');
                unset_config('block_panopto_server_name' . $x);
            }
            if (isset($CFG->{'block_panopto_application_key' . $x})) {
                set_config('application_key' . $x, $CFG->{'block_panopto_application_key' . $x}, 'block_panopto');
                unset_config('block_panopto_application_key' . $x);
            }
        }
        // Now move block_panopto_server_number setting value.
        if (isset($CFG->block_panopto_server_number)) {
            set_config('server_number', $CFG->block_panopto_server_number, 'block_panopto');
            unset_config('block_panopto_server_number');
        }
        // Move block_panopto_instance_name.
        if (isset($CFG->block_panopto_instance_name)) {
            set_config('instance_name', $CFG->block_panopto_instance_name, 'block_panopto');
            unset_config('block_panopto_instance_name');
        }
        // Move block_panopto_async_tasks.
        if (isset($CFG->block_panopto_async_tasks)) {
            set_config('async_tasks', $CFG->block_panopto_async_tasks, 'block_panopto');
            unset_config('block_panopto_async_tasks');
        }
        // Panopto savepoint reached.
        upgrade_block_savepoint(true, 2016101227, 'panopto');
    }

    if ($oldversion < 2016102709) {
        // Define table importmap where we will place all of our imports.
        $table = new xmldb_table('block_panopto_importmap');

        if (!$dbman->table_exists($table)) {
            $importfields = [];
            $importfields[] = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, true);
            $importfields[] = new xmldb_field('target_moodle_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
            $importfields[] = new xmldb_field('import_moodle_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);

            $importkey = new xmldb_key('primary', XMLDB_KEY_PRIMARY, ['id'], null, null);

            foreach ($importfields as $importfield) {
                // Conditionally launch add field import_moodle_id.
                $table->addField($importfield);
            }

            $table->addKey($importkey);

            $dbman->create_table($table);
        }

        // Panopto savepoint reached.
        upgrade_block_savepoint(true, 2016102709, 'panopto');
    }

    if ($oldversion < 2017031303) {

        // Get the roles using the old method so we can update current customers to the new tables.
        $pubroles = [];
        $creatorroles = [];

         // Get publisher roles as string and explode to array.
        $existingcoursemappings = $DB->get_records(
            'block_panopto_foldermap',
            null,
            'id,moodleid,publisher_mapping,creator_mapping'
        );

        // Define table table where we will place all of our creator mappings.
        $creatortable = new xmldb_table('block_panopto_creatormap');

        if (!$dbman->table_exists($creatortable)) {
            $mappingfields = [];
            $mappingfields[] = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, true);
            $mappingfields[] = new xmldb_field('moodle_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
            $mappingfields[] = new xmldb_field('role_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);

            $mappingkey = new xmldb_key('primary', XMLDB_KEY_PRIMARY, ['id'], null, null);

            foreach ($mappingfields as $mappingfield) {
                $creatortable->addField($mappingfield);
            }

            $creatortable->addKey($mappingkey);

            $dbman->create_table($creatortable);

            foreach ($existingcoursemappings as $existingmapping) {
                if (isset($existingmapping->creator_mapping) && !empty($existingmapping->creator_mapping)) {
                    $creatorroles = explode(",", $existingmapping->creator_mapping);

                    foreach ($creatorroles as $creatorrole) {
                        if (!empty($creatorrole)) {
                            $row = (object) ['moodle_id' => $existingmapping->moodleid, 'role_id' => $creatorrole];
                            $DB->insert_record('block_panopto_creatormap', $row);
                        }
                    }
                }
            }
        }

        $publishertable = new xmldb_table('block_panopto_publishermap');

        if (!$dbman->table_exists($publishertable)) {
            $mappingfields = [];
            $mappingfields[] = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, true);
            $mappingfields[] = new xmldb_field('moodle_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
            $mappingfields[] = new xmldb_field('role_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);

            $mappingkey = new xmldb_key('primary', XMLDB_KEY_PRIMARY, ['id'], null, null);

            foreach ($mappingfields as $mappingfield) {
                $publishertable->addField($mappingfield);
            }

            $publishertable->addKey($mappingkey);

            $dbman->create_table($publishertable);

            foreach ($existingcoursemappings as $existingmapping) {
                if (isset($existingmapping->publisher_mapping) && !empty($existingmapping->publisher_mapping)) {
                    $pubroles = explode("," , $existingmapping->publisher_mapping);

                    foreach ($pubroles as $pubrole) {
                        if (!empty($pubrole)) {
                            $row = (object) ['moodle_id' => $existingmapping->moodleid, 'role_id' => $pubrole];
                            $DB->insert_record('block_panopto_publishermap', $row);
                        }
                    }
                }
            }
        }

        // Panopto savepoint reached.
        upgrade_block_savepoint(true, 2017031303, 'panopto');
    }

    if ($oldversion < 2017110600) {

        // Define table table where we will place all of our old/broken folder mappings. So customers can keep the data if needed.
        $oldfoldermaptable = new xmldb_table('block_panopto_old_foldermap');
        if (!$dbman->table_exists($oldfoldermaptable)) {
            $mappingfields = [];
            $mappingfields[] =
                new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, true);
            $mappingfields[] =
                new xmldb_field('moodleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'id');
            $mappingfields[] =
                new xmldb_field('panopto_id', XMLDB_TYPE_CHAR, '36', null, XMLDB_NOTNULL, null, null, 'moodleid');
            $mappingfields[] =
                new xmldb_field('panopto_server', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'panopto_id');
            $mappingfields[] =
                new xmldb_field('panopto_app_key', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'panopto_server');
            $mappingfields[] =
                new xmldb_field('publisher_mapping', XMLDB_TYPE_CHAR, '20', null, null, null, '1', 'panopto_app_key');
            $mappingfields[] =
                new xmldb_field('creator_mapping', XMLDB_TYPE_CHAR, '20', null, null, null, '3,4', 'publisher_mapping');
            $mappingkey = new xmldb_key('primary', XMLDB_KEY_PRIMARY, ['id'], null, null);
            foreach ($mappingfields as $mappingfield) {
                $oldfoldermaptable->addField($mappingfield);
            }
            $oldfoldermaptable->addKey($mappingkey);
            $dbman->create_table($oldfoldermaptable);
        }

        // Delete any existing tasks since those would be from the old plug-in generation.
        $DB->delete_records_select('task_adhoc', $DB->sql_like('classname', '?'), ['%block_panopto%task%']);

        // Panopto savepoint reached.
        upgrade_block_savepoint(true, 2017110600, 'panopto');
    }

    if ($oldversion < 2018030200) {

        // Since this toggle got changed/removed for a select,
        // get the old value and if it's set then set the new feature as appropriate.
        if (get_config('block_panopto', 'prefix_new_folder_names')) {
            set_config('folder_name_style', 'combination', 'block_panopto');
        }

        // Panopto savepoint reached.
        upgrade_block_savepoint(true, 2018030200, 'panopto');
    }

    if ($oldversion < 2019070100) {

        // Define table table where we will place all of our category mappings.
        // So we can know which categories are linked to Panopto folders.
        $categorymaptable = new xmldb_table('block_panopto_categorymap');
        if (!$dbman->table_exists($categorymaptable)) {
            $mappingfields = [];
            $mappingfields[] = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, true);
            $mappingfields[] =
                new xmldb_field('category_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'id');
            $mappingfields[] =
                new xmldb_field('panopto_id', XMLDB_TYPE_CHAR, '36', null, XMLDB_NOTNULL, null, null, 'category_id');
            $mappingfields[] =
                new xmldb_field('panopto_server', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'panopto_id');
            $mappingkey = new xmldb_key('primary', XMLDB_KEY_PRIMARY, ['id'], null, null);
            foreach ($mappingfields as $mappingfield) {
                $categorymaptable->addField($mappingfield);
            }
            $categorymaptable->addKey($mappingkey);
            $dbman->create_table($categorymaptable);
        }

        // Panopto savepoint reached.
        upgrade_block_savepoint(true, 2019070100, 'panopto');
    }

    if ($oldversion < 2020072736) {
        // This toggle is getting changed from a checkbox to a select with a name value, update it during the upgrade if its set.
        if (get_config('block_panopto', 'auto_provision_new_courses')) {
            set_config('auto_provision_new_courses', 'oncoursecreation', 'block_panopto');
        } else {
            set_config('auto_provision_new_courses', 'off', 'block_panopto');
        }

        // This toggle got changed in the 2018030200 upgrade so we should just unset it if it still exists.
        if (get_config('block_panopto', 'prefix_new_folder_names')) {
            unset_config('prefix_new_folder_names', 'block_panopto');
        }

        // Panopto savepoint reached.
        upgrade_block_savepoint(true, 2020072736, 'panopto');
    }

    if ($oldversion < 2021063000) {

        $foldermaptable = new xmldb_table('block_panopto_foldermap');
        $importmaptable = new xmldb_table('block_panopto_importmap');
        $creatormaptable = new xmldb_table('block_panopto_creatormap');
        $publishermaptable = new xmldb_table('block_panopto_publishermap');
        $oldfoldermaptable = new xmldb_table('block_panopto_old_foldermap');
        $categorymaptable = new xmldb_table('block_panopto_categorymap');

        if ($dbman->table_exists($foldermaptable)) {
            $moodleidindex = new xmldb_index('mdl_blocpanofold_moo_ix', XMLDB_INDEX_NOTUNIQUE, ['moodleid'], []);
            $serverindex = new xmldb_index('mdl_blocpanofold_pan_ix', XMLDB_INDEX_NOTUNIQUE, ['panopto_server'], []);

            if (!$dbman->index_exists($foldermaptable, $moodleidindex)) {
                $dbman->add_index($foldermaptable, $moodleidindex);
            }

            if (!$dbman->index_exists($foldermaptable, $serverindex)) {
                $dbman->add_index($foldermaptable, $serverindex);
            }
        } else {
            return false;
        }

        if ($dbman->table_exists($importmaptable)) {
            $targetidindex = new xmldb_index('mdl_blocpanoimpo_tar_ix', XMLDB_INDEX_NOTUNIQUE, ['target_moodle_id'], []);
            $importidindex = new xmldb_index('mdl_blocpanoimpo_imp_ix', XMLDB_INDEX_NOTUNIQUE, ['import_moodle_id'], []);

            if (!$dbman->index_exists($importmaptable, $targetidindex)) {
                $dbman->add_index($importmaptable, $targetidindex);
            }

            if (!$dbman->index_exists($importmaptable, $importidindex)) {
                $dbman->add_index($importmaptable, $importidindex);
            }
        } else {
            return false;
        }

        if ($dbman->table_exists($creatormaptable)) {
            $moodleidindex = new xmldb_index('mdl_blocpanocrea_moo_ix', XMLDB_INDEX_NOTUNIQUE, ['moodle_id'], []);

            if (!$dbman->index_exists($creatormaptable, $moodleidindex)) {
                $dbman->add_index($creatormaptable, $moodleidindex);
            }
        } else {
            return false;
        }

        if ($dbman->table_exists($publishermaptable)) {
            $moodleidindex = new xmldb_index('mdl_blocpanopubl_moo_ix', XMLDB_INDEX_NOTUNIQUE, ['moodle_id'], []);

            if (!$dbman->index_exists($publishermaptable, $moodleidindex)) {
                $dbman->add_index($publishermaptable, $moodleidindex);
            }
        } else {
            return false;
        }

        if ($dbman->table_exists($oldfoldermaptable)) {
            $moodleidindex = new xmldb_index('mdl_blocpanooldfold_moo_ix', XMLDB_INDEX_NOTUNIQUE, ['moodleid'], []);
            $serverindex = new xmldb_index('mdl_blocpanooldfold_pan_ix', XMLDB_INDEX_NOTUNIQUE, ['panopto_server'], []);

            if (!$dbman->index_exists($oldfoldermaptable, $moodleidindex)) {
                $dbman->add_index($oldfoldermaptable, $moodleidindex);
            }

            if (!$dbman->index_exists($oldfoldermaptable, $serverindex)) {
                $dbman->add_index($oldfoldermaptable, $serverindex);
            }
        } else {
            return false;
        }

        if ($dbman->table_exists($categorymaptable)) {
            $serverindex = new xmldb_index('mdl_blocpanocate_cat_ix', XMLDB_INDEX_NOTUNIQUE, ['category_id'], []);

            if (!$dbman->index_exists($categorymaptable, $serverindex)) {
                $dbman->add_index($categorymaptable, $serverindex);
            }
        } else {
            return false;
        }

        // Panopto savepoint reached.
        upgrade_block_savepoint(true, 2021063000, 'panopto');
    }

    return true;
}
