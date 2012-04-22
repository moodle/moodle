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
 * @package    moodlecore
 * @subpackage backup-dbops
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Non instantiable helper class providing DB support to the @restore_controller
 *
 * This class contains various static methods available for all the DB operations
 * performed by the restore_controller class
 *
 * TODO: Finish phpdocs
 */
abstract class restore_controller_dbops extends restore_dbops {

    /**
     * Send one restore controller to DB
     *
     * @param restore_controller $controller controller to send to DB
     * @param string $checksum hash of the controller to be checked
     * @param bool $includeobj to decide if the object itself must be updated (true) or no (false)
     * @param bool $cleanobj to decide if the object itself must be cleaned (true) or no (false)
     * @return int id of the controller record in the DB
     * @throws backup_controller_exception|restore_dbops_exception
     */
    public static function save_controller($controller, $checksum, $includeobj = true, $cleanobj = false) {
        global $DB;
        // Check we are going to save one backup_controller
        if (! $controller instanceof restore_controller) {
            throw new backup_controller_exception('restore_controller_expected');
        }
        // Check checksum is ok. Only if we are including object info. Sounds silly but it isn't ;-).
        if ($includeobj and !$controller->is_checksum_correct($checksum)) {
            throw new restore_dbops_exception('restore_controller_dbops_saving_checksum_mismatch');
        }
        // Cannot request to $includeobj and $cleanobj at the same time.
        if ($includeobj and $cleanobj) {
            throw new restore_dbops_exception('restore_controller_dbops_saving_cannot_include_and_delete');
        }
        // Get all the columns
        $rec = new stdclass();
        $rec->backupid     = $controller->get_restoreid();
        $rec->operation    = $controller->get_operation();
        $rec->type         = $controller->get_type();
        $rec->itemid       = $controller->get_courseid();
        $rec->format       = $controller->get_format();
        $rec->interactive  = $controller->get_interactive();
        $rec->purpose      = $controller->get_mode();
        $rec->userid       = $controller->get_userid();
        $rec->status       = $controller->get_status();
        $rec->execution    = $controller->get_execution();
        $rec->executiontime= $controller->get_executiontime();
        $rec->checksum     = $checksum;
        // Serialize information
        if ($includeobj) {
            $rec->controller = base64_encode(serialize($controller));
        } else if ($cleanobj) {
            $rec->controller = '';
        }
        // Send it to DB
        if ($recexists = $DB->get_record('backup_controllers', array('backupid' => $rec->backupid))) {
            $rec->id = $recexists->id;
            $rec->timemodified = time();
            $DB->update_record('backup_controllers', $rec);
        } else {
            $rec->timecreated = time();
            $rec->timemodified = 0;
            $rec->id = $DB->insert_record('backup_controllers', $rec);
        }
        return $rec->id;
    }

    public static function load_controller($restoreid) {
        global $DB;
        if (! $controllerrec = $DB->get_record('backup_controllers', array('backupid' => $restoreid))) {
            throw new backup_dbops_exception('restore_controller_dbops_nonexisting');
        }
        $controller = unserialize(base64_decode($controllerrec->controller));
        // Check checksum is ok. Sounds silly but it isn't ;-)
        if (!$controller->is_checksum_correct($controllerrec->checksum)) {
            throw new backup_dbops_exception('restore_controller_dbops_loading_checksum_mismatch');
        }
        return $controller;
    }

    public static function create_restore_temp_tables($restoreid) {
        global $CFG, $DB;
        $dbman = $DB->get_manager(); // We are going to use database_manager services

        if ($dbman->table_exists('backup_ids_temp')) { // Table exists, from restore prechecks
            // TODO: Improve this by inserting/selecting some record to see there is restoreid match
            // TODO: If not match, exception, table corresponds to another backup/restore operation
            return true;
        }
        backup_controller_dbops::create_temptable_from_real_table($restoreid, 'backup_ids_template', 'backup_ids_temp');
        backup_controller_dbops::create_temptable_from_real_table($restoreid, 'backup_files_template', 'backup_files_temp');
        return false;
    }

    public static function drop_restore_temp_tables($backupid) {
        global $DB;
        $dbman = $DB->get_manager(); // We are going to use database_manager services

        $targettablenames = array('backup_ids_temp', 'backup_files_temp');
        foreach ($targettablenames as $targettablename) {
            $table = new xmldb_table($targettablename);
            $dbman->drop_temp_table($table); // And drop it
        }
    }
}
