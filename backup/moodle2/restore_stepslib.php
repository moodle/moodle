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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by common tasks in restore
 */

/**
 * delete old directories and conditionally create backup_temp_ids table
 */
class restore_create_and_clean_temp_stuff extends restore_execution_step {

    protected function define_execution() {
        $exists = restore_controller_dbops::create_backup_ids_temp_table($this->get_restoreid()); // Create temp table conditionally
        // If the table already exists, it's because restore_prechecks have been executed in the same
        // request (without problems) and it already contains a bunch of preloaded information (users...)
        // that we aren't going to execute again
        if ($exists) { // Inform plan about preloaded information
            $this->task->set_preloaded_information();
        }
        // Create the old-course-ctxid to new-course-ctxid mapping, we need that available since the beginning
        $itemid = $this->task->get_old_contextid();
        $newitemid = get_context_instance(CONTEXT_COURSE, $this->get_courseid())->id;
        restore_dbops::set_backup_ids_record($this->get_restoreid(), 'context', $itemid, $newitemid);
    }
}

/**
 * delete the temp dir used by backup/restore (conditionally),
 * delete old directories and drop temp ids table
 */
class restore_drop_and_clean_temp_stuff extends restore_execution_step {

    protected function define_execution() {
        global $CFG;
        backup_controller_dbops::drop_backup_ids_temp_table($this->get_restoreid()); // Drop ids temp table
        backup_helper::delete_old_backup_dirs(time() - (4 * 60 * 60));               // Delete > 4 hours temp dirs
        if (empty($CFG->keeptempdirectoriesonbackup)) { // Conditionally
            backup_helper::delete_backup_dir($this->get_restoreid()); // Empty backup dir
        }
    }
}

/*
 * Execution step that, *conditionally* (if there isn't preloaded information)
 * will load the inforef files for all the included course/section/activity tasks
 * to backup_temp_ids. They will be stored with "xxxxref" as itemname
 */
class restore_load_included_inforef_records extends restore_execution_step {

    protected function define_execution() {

        if ($this->task->get_preloaded_information()) { // if info is already preloaded, nothing to do
            return;
        }

        // Get all the included inforef files
        $files = restore_dbops::get_needed_inforef_files($this->get_restoreid());
        foreach ($files as $file) {
            restore_dbops::load_inforef_to_tempids($this->get_restoreid(), $file); // Load each inforef file to temp_ids
        }
    }
}

/*
 * Execution step that will load all the needed files into backup_temp_ids.
 *   - itemname: contains "file*component*fileara"
 *   - itemid: contains the original id of the file
 *   - newitemid: contains the itemid of the file
 *   - parentitemid: contains the context of the file
 *   - info: contains the whole original object (times, names...)
 * (all them being original ids as loaded from xml)
 */
class restore_load_included_files extends restore_structure_step {

    protected function define_structure() {

        $file = new restore_path_element('file', '/files/file');

        return array($file);
    }

    // Processing functions go here
    public function process_file($data) {

        $data = (object)$data; // handy

        $itemname = 'file*' . $data->component . '*' . $data->filearea;
        $itemid   = $data->id;
        $newitemid = $data->itemid;
        $parentitemid = $data->contextid;
        $info = $data;

        // load it if needed:
        //   - it it is one of the annotated inforef files (course/section/activity/block)
        //   - it is one "user", "group" or "grade" component file
        $isfileref   = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'fileref', $itemid);
        $iscomponent = ($data->component == 'user' || $data->component == 'group' || $data->component == 'grade');
        if ($isfileref || $iscomponent) {
            restore_dbops::set_backup_ids_record($this->get_restoreid(), $itemname, $itemid, $newitemid, $parentitemid, $info);
        }
    }
}

/**
 * Execution step that, *conditionally* (if there isn't preloaded information
 * and users have been selected in settings, will load all the needed users
 * to backup_temp_ids. They will be stored with "user" itemname and with
 * their original contextid as paremitemid
 */
class restore_load_included_users extends restore_execution_step {

    protected function define_execution() {

        if ($this->task->get_preloaded_information()) { // if info is already preloaded, nothing to do
            return;
        }
        if (!$this->task->get_setting('users')) { // No userinfo being restored, nothing to do
            return;
        }
        $file = $this->get_basepath() . '/users.xml';
        restore_dbops::load_users_to_tempids($this->get_restoreid(), $file); // Load needed users to temp_ids
    }
}

/**
 * Execution step that, *conditionally* (if there isn't preloaded information
 * and users have been selected in settings, will process all the needed users
 * in order to decide and perform any action with them (create / map / error)
 * Note: Any error will cause exception, as far as this is the same processing
 * than the one into restore prechecks (that should have stopped process earlier)
 */
class restore_process_included_users extends restore_execution_step {

    protected function define_execution() {

        if ($this->task->get_preloaded_information()) { // if info is already preloaded, nothing to do
            return;
        }
        if (!$this->task->get_setting('users')) { // No userinfo being restored, nothing to do
            return;
        }
        restore_dbops::process_included_users($this->get_restoreid(), $this->task->get_courseid(), $this->task->get_userid(), $this->task->is_samesite());
    }
}

/**
 * Execution step that will create all the needed users as calculated
 * by @restore_process_included_users (those having newiteind = 0)
 */
class restore_create_included_users extends restore_execution_step {

    protected function define_execution() {

        restore_dbops::create_included_users($this->get_basepath(), $this->get_restoreid(), $this->get_setting_value('user_files'));
    }
}

/**
 * Structure step that will create all the needed groups and groupings
 * by loading them from the groups.xml file performing the required matches.
 * Note group members only will be added if restoring user info
 */
class restore_groups_structure_step extends restore_structure_step {

     protected function define_structure() {

         $paths = array(); // Add paths here

         $paths[] = new restore_path_element('group', '/groups/group');
         if ($this->get_setting_value('users')) {
             $paths[] = new restore_path_element('member', '/groups/group/group_members/group_member');
         }
         $paths[] = new restore_path_element('grouping', '/groups/groupings/grouping');
         $paths[] = new restore_path_element('grouping_group', '/groups/groupings/grouping/grouping_groups/grouping_group');

         return $paths;
     }

     // Processing functions go here
     public function process_group($data) {
         global $DB;

         $data = (object)$data; // handy
         $data->courseid = $this->get_courseid();

         $oldid = $data->id;    // need this saved for later

         $restorefiles = false; // Only if we end creating the group

         // Search if the group already exists (by name & description) in the target course
         $description_clause = '';
         $params = array('courseid' => $this->get_courseid(), 'grname' => $data->name);
         if (!empty($data->description)) {
             $description_clause = ' AND ' .
                                   $DB->sql_compare_text('description') . ' = ' . $DB->sql_compare_text(':desc');
             $params['desc'] = $data->description;
         }
         if (!$groupdb = $DB->get_record_sql("SELECT *
                                                FROM {groups}
                                               WHERE courseid = :courseid
                                                 AND name = :grname $description_clause", $params)) {
             // group doesn't exist, create
             $newitemid = $DB->insert_record('groups', $data);
             $restorefiles = true; // We'll restore the files
         } else {
             // group exists, use it
             $newitemid = $groupdb->id;
         }
         // Save the id mapping
         $this->set_mapping('group', $oldid, $newitemid, $restorefiles);
     }

     public function process_member($data) {
         global $DB;

         $data = (object)$data; // handy

         // get parent group->id
         $data->groupid = $this->get_new_parentid('group');

         // map user newitemid and insert if not member already
         if ($data->userid = $this->get_mappingid('user', $data->userid)) {
             if (!$DB->record_exists('groups_members', array('groupid' => $data->groupid, 'userid' => $data->userid))) {
                 $DB->insert_record('groups_members', $data);
             }
         }
     }

     public function process_grouping($data) {
         debugging('TODO: Grouping restore not implemented. Detected grouping', DEBUG_DEVELOPER);
     }

     public function process_grouping_group($data) {
         debugging('TODO: Grouping restore not implemented. Detected grouping group', DEBUG_DEVELOPER);
     }

     protected function after_execute() {
         return;
         $this->add_related_files('group', 'icon', 'group');
         $this->add_related_files('group', 'description', 'group');
         restore_dbops::send_files_to_pool($basepath, $restoreid, 'user', 'private', $recuser->parentitemid);
     }

}

/*
 * Structure step that will read the course.xml file, loading it and performing
 * various actions depending of the site/restore settings
 */
class restore_course_structure_step extends restore_structure_step {

    protected function define_structure() {

        $course = new restore_path_element('course', '/course', true); // Grouped
        $category = new restore_path_element('category', '/course/category');
        $tag = new restore_path_element('tag', '/course/tags/tag');
        $allowed = new restore_path_element('allowed', '/course/allowed_modules/module');

        return array($course, $category, $tag, $allowed);
    }

    // Processing functions go here
    public function process_course($data) {
        print_object('stopped before processing course. Continue here');
    }

}
