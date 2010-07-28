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
        $exists = restore_controller_dbops::create_restore_temp_tables($this->get_restoreid()); // temp tables conditionally
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
        // Create the old-system-ctxid to new-system-ctxid mapping, we need that available since the beginning
        $itemid = $this->task->get_old_system_contextid();
        $newitemid = get_context_instance(CONTEXT_SYSTEM)->id;
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
        restore_controller_dbops::drop_restore_temp_tables($this->get_restoreid()); // Drop ids temp table
        backup_helper::delete_old_backup_dirs(time() - (4 * 60 * 60));              // Delete > 4 hours temp dirs
        if (empty($CFG->keeptempdirectoriesonbackup)) { // Conditionally
            backup_helper::delete_backup_dir($this->task->get_tempdir()); // Empty restore dir
        }
    }
}

/**
 * decode all the interlinks present in restored content
 * relying 100% in the restore_decode_processor that handles
 * both the contents to modify and the rules to be applied
 */
class restore_decode_interlinks extends restore_execution_step {

    protected function define_execution() {
        // Just that
        $this->task->get_decoder()->execute();
    }
}

/**
 * rebuid the course cache
 */
class restore_rebuild_course_cache extends restore_execution_step {

    protected function define_execution() {
        // Just that
        rebuild_course_cache($this->get_courseid());
    }
}


/**
 * Review all the (pending) block positions in backup_ids, matching by
 * contextid, creating positions as needed. This is executed by the
 * final task, once all the contexts have been created
 */
class restore_review_pending_block_positions extends restore_execution_step {

    protected function define_execution() {
        global $DB;

        // Get all the block_position objects pending to match
        $params = array('backupid' => $this->get_restoreid(), 'itemname' => 'block_position');
        $rs = $DB->get_recordset('backup_ids_temp', $params, '', 'itemid');
        // Process block positions, creating them or accumulating for final step
        foreach($rs as $posrec) {
            // Get the complete position object (stored as info)
            $position = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'block_position', $posrec->itemid)->info;
            // If position is for one already mapped (known) contextid
            // process it now, creating the position, else nothing to
            // do, position finally discarded
            if ($newctx = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'context', $position->contextid)) {
                $position->contextid = $newctx->newitemid;
                // Create the block position
                $DB->insert_record('block_positions', $position);
            }
        }
        $rs->close();
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
 * Execution step that will load all the needed files into backup_files_temp
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

        // load it if needed:
        //   - it it is one of the annotated inforef files (course/section/activity/block)
        //   - it is one "user", "group", "grouping" or "grade" component file (that aren't sent to inforef ever)
        $isfileref   = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'fileref', $data->id);
        $iscomponent = ($data->component == 'user' || $data->component == 'group' ||
                        $data->component == 'grouping' || $data->component == 'grade');
        if ($isfileref || $iscomponent) {
            restore_dbops::set_backup_files_record($this->get_restoreid(), $data);
        }
    }
}

/**
 * Execution step that, *conditionally* (if there isn't preloaded information),
 * will load all the needed roles to backup_temp_ids. They will be stored with
 * "role" itemname. Also it will perform one automatic mapping to roles existing
 * in the target site, based in permissions of the user performing the restore,
 * archetypes and other bits. At the end, each original role will have its associated
 * target role or 0 if it's going to be skipped. Note we wrap everything over one
 * restore_dbops method, as far as the same stuff is going to be also executed
 * by restore prechecks
 */
class restore_load_and_map_roles extends restore_execution_step {

    protected function define_execution() {
        if ($this->task->get_preloaded_information()) { // if info is already preloaded
            return;
        }

        $file = $this->get_basepath() . '/roles.xml';
        // Load needed toles to temp_ids
        restore_dbops::load_roles_to_tempids($this->get_restoreid(), $file);

        // Process roles, mapping/skipping. Any error throws exception
        // Note we pass controller's info because it can contain role mapping information
        // about manual mappings performed by UI
        restore_dbops::process_included_roles($this->get_restoreid(), $this->task->get_courseid(), $this->task->get_userid(), $this->task->is_samesite(), $this->task->get_info()->role_mappings);
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
        if (!$this->task->get_setting_value('users')) { // No userinfo being restored, nothing to do
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
        if (!$this->task->get_setting_value('users')) { // No userinfo being restored, nothing to do
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
        global $DB;

        $data = (object)$data; // handy
        $data->courseid = $this->get_courseid();

        $oldid = $data->id;    // need this saved for later
        $restorefiles = false; // Only if we end creating the grouping

        // Search if the grouping already exists (by name & description) in the target course
        $description_clause = '';
        $params = array('courseid' => $this->get_courseid(), 'grname' => $data->name);
        if (!empty($data->description)) {
            $description_clause = ' AND ' .
                                  $DB->sql_compare_text('description') . ' = ' . $DB->sql_compare_text(':desc');
           $params['desc'] = $data->description;
        }
        if (!$groupingdb = $DB->get_record_sql("SELECT *
                                                  FROM {groupings}
                                                 WHERE courseid = :courseid
                                                   AND name = :grname $description_clause", $params)) {
            // grouping doesn't exist, create
            $newitemid = $DB->insert_record('groupings', $data);
            $restorefiles = true; // We'll restore the files
        } else {
            // grouping exists, use it
            $newitemid = $groupingdb->id;
        }
        // Save the id mapping
        $this->set_mapping('grouping', $oldid, $newitemid, $restorefiles);
    }

    public function process_grouping_group($data) {
        global $DB;

        $data = (object)$data;

        $data->groupingid = $this->get_new_parentid('grouping'); // Use new parentid
        $data->groupid    = $this->get_mappingid('group', $data->groupid); // Get from mappings
        $DB->insert_record('groupings_groups', $data);  // No need to set this mapping (no child info nor files)
    }

    protected function after_execute() {
        // Add group related files, matching with "group" mappings
        $this->add_related_files('group', 'icon', 'group');
        $this->add_related_files('group', 'description', 'group');
        // Add grouping related files, matching with "grouping" mappings
        $this->add_related_files('grouping', 'description', 'grouping');
    }

}

/**
 * Structure step that will create all the needed scales
 * by loading them from the scales.xml
 */
class restore_scales_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array(); // Add paths here
        $paths[] = new restore_path_element('scale', '/scales_definition/scale');
        return $paths;
    }

    protected function process_scale($data) {
        global $DB;

        $data = (object)$data;

        $restorefiles = false; // Only if we end creating the group

        $oldid = $data->id;    // need this saved for later

        // Look for scale (by 'scale' both in standard (course=0) and current course
        // with priority to standard scales (ORDER clause)
        // scale is not course unique, use get_record_sql to suppress warning
        // Going to compare LOB columns so, use the cross-db sql_compare_text() in both sides
        $compare_scale_clause = $DB->sql_compare_text('scale')  . ' = ' . $DB->sql_compare_text(':scaledesc');
        $params = array('courseid' => $this->get_courseid(), 'scaledesc' => $data->scale);
        if (!$scadb = $DB->get_record_sql("SELECT *
                                            FROM {scale}
                                           WHERE courseid IN (0, :courseid)
                                             AND $compare_scale_clause
                                        ORDER BY courseid", $params, IGNORE_MULTIPLE)) {
            // Remap the user if possible, defaut to user performing the restore if not
            $userid = $this->get_mappingid('user', $data->userid);
            $data->userid = $userid ? $userid : $this->task->get_userid();
            // Remap the course if course scale
            $data->courseid = $data->courseid ? $this->get_courseid() : 0;
            // If global scale (course=0), check the user has perms to create it
            // falling to course scale if not
            $systemctx = get_context_instance(CONTEXT_SYSTEM);
            if ($data->courseid == 0 && !has_capability('moodle/course:managescales', $systemctx , $this->task->get_userid())) {
                $data->courseid = $this->get_courseid();
            }
            // scale doesn't exist, create
            $newitemid = $DB->insert_record('scale', $data);
            $restorefiles = true; // We'll restore the files
        } else {
            // scale exists, use it
            $newitemid = $scadb->id;
        }
        // Save the id mapping (with files support at system context)
        $this->set_mapping('scale', $oldid, $newitemid, $restorefiles, $this->task->get_old_system_contextid());
    }

    protected function after_execute() {
        // Add scales related files, matching with "scale" mappings
        $this->add_related_files('grade', 'scale', 'scale', $this->task->get_old_system_contextid());
    }
}


/**
 * Structure step that will create all the needed outocomes
 * by loading them from the outcomes.xml
 */
class restore_outcomes_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array(); // Add paths here
        $paths[] = new restore_path_element('outcome', '/outcomes_definition/outcome');
        return $paths;
    }

    protected function process_outcome($data) {
        global $DB;

        $data = (object)$data;

        $restorefiles = false; // Only if we end creating the group

        $oldid = $data->id;    // need this saved for later

        // Look for outcome (by shortname both in standard (courseid=null) and current course
        // with priority to standard outcomes (ORDER clause)
        // outcome is not course unique, use get_record_sql to suppress warning
        $params = array('courseid' => $this->get_courseid(), 'shortname' => $data->shortname);
        if (!$outdb = $DB->get_record_sql('SELECT *
                                             FROM {grade_outcomes}
                                            WHERE shortname = :shortname
                                              AND (courseid = :courseid OR courseid IS NULL)
                                         ORDER BY COALESCE(courseid, 0)', $params, IGNORE_MULTIPLE)) {
            // Remap the user
            $userid = $this->get_mappingid('user', $data->usermodified);
            $data->usermodified = $userid ? $userid : $this->task->get_userid();
            // Remap the scale
            $data->scaleid = $this->get_mappingid('scale', $data->scaleid);
            // Remap the course if course outcome
            $data->courseid = $data->courseid ? $this->get_courseid() : null;
            // If global outcome (course=null), check the user has perms to create it
            // falling to course outcome if not
            $systemctx = get_context_instance(CONTEXT_SYSTEM);
            if (is_null($data->courseid) && !has_capability('moodle/grade:manageoutcomes', $systemctx , $this->task->get_userid())) {
                $data->courseid = $this->get_courseid();
            }
            // outcome doesn't exist, create
            $newitemid = $DB->insert_record('grade_outcomes', $data);
            $restorefiles = true; // We'll restore the files
        } else {
            // scale exists, use it
            $newitemid = $outdb->id;
        }
        // Set the corresponding grade_outcomes_courses record
        $outcourserec = new stdclass();
        $outcourserec->courseid  = $this->get_courseid();
        $outcourserec->outcomeid = $newitemid;
        if (!$DB->record_exists('grade_outcomes_courses', (array)$outcourserec)) {
            $DB->insert_record('grade_outcomes_courses', $outcourserec);
        }
        // Save the id mapping (with files support at system context)
        $this->set_mapping('outcome', $oldid, $newitemid, $restorefiles, $this->task->get_old_system_contextid());
    }

    protected function after_execute() {
        // Add outcomes related files, matching with "outcome" mappings
        $this->add_related_files('grade', 'outcome', 'outcome', $this->task->get_old_system_contextid());
    }
}

/**
 * Structure step that will read the section.xml creating/updating sections
 * as needed, rebuilding course cache and other friends
 */
class restore_section_structure_step extends restore_structure_step {

    protected function define_structure() {
        return array(new restore_path_element('section', '/section'));
    }

    public function process_section($data) {
        global $DB;
        $data = (object)$data;
        $oldid = $data->id; // We'll need this later

        $restorefiles = false;

        // Look for the section
        $section = new stdclass();
        $section->course  = $this->get_courseid();
        $section->section = $data->number;
        // Section doesn't exist, create it with all the info from backup
        if (!$secrec = $DB->get_record('course_sections', (array)$section)) {
            $section->name = $data->name;
            $section->summary = $data->summary;
            $section->summaryformat = $data->summaryformat;
            $section->sequence = '';
            $section->visible = $data->visible;
            $newitemid = $DB->insert_record('course_sections', $section);
            $restorefiles = true;

        // Section exists, update non-empty information
        } else {
            $section->id = $secrec->id;
            if (empty($secrec->name)) {
                $section->name = $data->name;
            }
            if (empty($secrec->summary)) {
                $section->summary = $data->summary;
                $section->summaryformat = $data->summaryformat;
                $restorefiles = true;
            }
            $DB->update_record('course_sections', $section);
            $newitemid = $secrec->id;
        }

        // Annotate the section mapping, with restorefiles option if needed
        $this->set_mapping('course_section', $oldid, $newitemid, $restorefiles);

        // If needed, adjust course->numsections
        if ($numsections = $DB->get_field('course', 'numsections', array('id' => $this->get_courseid()))) {
            if ($numsections < $section->section) {
                $DB->set_field('course', 'numsections', $section->section, array('id' => $this->get_courseid()));
            }
        }
    }

    protected function after_execute() {
        // Add section related files, with 'course_section' itemid to match
        $this->add_related_files('course', 'section', 'course_section');
    }
}


/**
 * Structure step that will read the course.xml file, loading it and performing
 * various actions depending of the site/restore settings. Note that target
 * course always exist before arriving here so this step will be updating
 * the course record (never inserting)
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
        global $CFG, $DB;

        $data = (object)$data;
        $coursetags = isset($data->tags['tag']) ? $data->tags['tag'] : array();
        $coursemodules = isset($data->allowed_modules['module']) ? $data->allowed_modules['module'] : array();
        $oldid = $data->id; // We'll need this later

        $fullname  = $this->get_setting_value('course_fullname');
        $shortname = $this->get_setting_value('course_shortname');
        $startdate = $this->get_setting_value('course_startdate');

        // Calculate final course names, to avoid dupes
        list($fullname, $shortname) = restore_dbops::calculate_course_names($this->get_courseid(), $fullname, $shortname);

        // Need to change some fields before updating the course record
        $data->id = $this->get_courseid();
        $data->fullname = $fullname;
        $data->shortname= $shortname;
        $data->idnumber = '';
        // TODO: Set category from the UI, its not a setting just a param
        $data->category = get_course_category()->id;
        $data->startdate= $this->apply_date_offset($data->startdate);
        if ($data->defaultgroupingid) {
            $data->defaultgroupingid = $this->get_mappingid('grouping', $data->defaultgroupingid);
        }
        if (empty($CFG->enablecompletion) || !$this->get_setting_value('userscompletion')) {
            $data->enablecompletion = 0;
            $data->completionstartonenrol = 0;
            $data->completionnotify = 0;
        }
        $languages = get_string_manager()->get_list_of_translations(); // Get languages for quick search
        if (!array_key_exists($data->lang, $languages)) {
            $data->lang = '';
        }
        $themes = get_list_of_themes(); // Get themes for quick search later
        if (!in_array($data->theme, $themes) || empty($CFG->allowcoursethemes)) {
            $data->theme = '';
        }

        // Course record ready, update it
        $DB->update_record('course', $data);

        // Set course mapping
        $this->set_mapping('course', $oldid, $data->id);

        // Course tags
        if (!empty($CFG->usetags) && isset($coursetags)) { // if enabled in server and present in backup
            $tags = array();
            foreach ($coursetags as $coursetag) {
                $coursetag = (object)$coursetag;
                $tags[] = $coursetag->rawname;
            }
            tag_set('course', $this->get_courseid(), $tags);
        }
        // Course allowed modules
        if (!empty($data->restrictmodules) && !empty($coursemodules)) {
            $available = get_plugin_list('mod');
            foreach ($coursemodules as $coursemodule) {
                $mname = $coursemodule['modulename'];
                if (array_key_exists($mname, $available)) {
                    if ($module = $DB->get_record('modules', array('name' => $mname, 'visible' => 1))) {
                        $rec = new stdclass();
                        $rec->course = $this->get_courseid();
                        $rec->module = $module->id;
                        if (!$DB->record_exists('course_allowed_modules', (array)$rec)) {
                            $DB->insert_record('course_allowed_modules', $rec);
                        }
                    }
                }
            }
        }
        // Role name aliases
        restore_dbops::set_course_role_names($this->get_restoreid(), $this->get_courseid());
    }

    protected function after_execute() {
        // Add course related files, without itemid to match
        $this->add_related_files('course', 'summary', null);
        $this->add_related_files('course', 'legacy', null);
    }
}


/*
 * Structure step that will read the roles.xml file (at course/activity/block levels)
 * containig all the role_assignments and overrides for that context. If corresponding to
 * one mapped role, they will be applied to target context. Will observe the role_assignments
 * setting to decide if ras are restored.
 * Note: only ras with component == null are restored as far as the any ra with component
 * is handled by one enrolment plugin, hence it will createt the ras later
 */
class restore_ras_and_caps_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        // Observe the role_assignments setting
        if ($this->get_setting_value('role_assignments')) {
            $paths[] = new restore_path_element('assignment', '/roles/role_assignments/assignment');
        }
        $paths[] = new restore_path_element('override', '/roles/role_overrides/override');

        return $paths;
    }

    public function process_assignment($data) {
        $data = (object)$data;

        // Check roleid, userid are one of the mapped ones
        $newroleid = $this->get_mappingid('role', $data->roleid);
        $newuserid = $this->get_mappingid('user', $data->userid);
        // If newroleid and newuserid and component is empty and context valid assign via API (handles dupes and friends)
        if ($newroleid && $newuserid && empty($data->component) && $this->task->get_contextid()) {
            // TODO: role_assign() needs one userid param to be able to specify our restore userid
            role_assign($newroleid, $newuserid, $this->task->get_contextid());
        }
    }

    public function process_override($data) {
        $data = (object)$data;

        // Check roleid is one of the mapped ones
        $newroleid = $this->get_mappingid('role', $data->roleid);
        // If newroleid and context are valid assign it via API (it handles dupes and so on)
        if ($newroleid && $this->task->get_contextid()) {
            // TODO: assign_capability() needs one userid param to be able to specify our restore userid
            // TODO: it seems that assign_capability() doesn't check for valid capabilities at all ???
            assign_capability($data->capability, $data->permission, $newroleid, $this->task->get_contextid());
        }
    }
}

/**
 * This structure steps restores the enrol plugins and their underlying
 * enrolments, performing all the mappings and/or movements required
 */
class restore_enrolments_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('enrol', '/enrolments/enrols/enrol');
        $paths[] = new restore_path_element('enrolment', '/enrolments/enrols/enrol/user_enrolments/enrolment');

        return $paths;
    }

    public function process_enrol($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id; // We'll need this later

        // TODO: Just one quick process of manual enrol_plugin. Add the rest (complex ones) and fix this
        if ($data->enrol !== 'manual') {
            debugging("Skipping '{$data->enrol}' enrolment plugin. Must be implemented", DEBUG_DEVELOPER);
            return;
        }

        // Perform various checks to decide what to do with the enrol plugin
        $installed = array_key_exists($data->enrol, enrol_get_plugins(false));
        $enabled   = enrol_is_enabled($data->enrol);
        $exists    = 0;
        $roleid    = $this->get_mappingid('role', $data->roleid);
        if ($rec = $DB->get_record('enrol', array('courseid' => $this->get_courseid(), 'enrol' => $data->enrol))) {
            $exists = $rec->id;
        }
        // If installed and enabled, continue processing
        if ($installed && $enabled) {
            // If not exists in course and we have a target role mapping
            if (!$exists && $roleid) {
                $data->roleid = $roleid;
                $enrol = enrol_get_plugin($data->enrol);
                $courserec = $DB->get_record('course', array('id' => $this->get_courseid())); // Requires object, uses only id!!
                $newitemid = $enrol->add_instance($courserec, array($data));

            // Already exists, user it for enrolments
            } else {
                $newitemid = $exists;
            }

        // Not installed and enabled, map to 0
        } else {
            $newitemid = 0;
        }
        // Perform the simple mapping and done
        $this->set_mapping('enrol', $oldid, $newitemid);
    }

    public function process_enrolment($data) {
        global $DB;

        $data = (object)$data;

        // Process only if parent instance have been mapped
        if ($enrolid = $this->get_new_parentid('enrol')) {
            // And only if user is a mapped one
            if ($userid = $this->get_mappingid('user', $data->userid)) {
                // TODO: Surely need to use API (enrol_user) here, instead of the current low-level impl
                // TODO: Note enrol_user() sticks to $USER->id (need to add userid param)
                $enrolment = new stdclass();
                $enrolment->enrolid = $enrolid;
                $enrolment->userid  = $userid;
                if (!$DB->record_exists('user_enrolments', (array)$enrolment)) {
                    $enrolment->status = $data->status;
                    $enrolment->timestart = $data->timestart;
                    $enrolment->timeend = $data->timeend;
                    $enrolment->modifierid = $this->task->get_userid();
                    $enrolment->timecreated = time();
                    $enrolment->timemodified = 0;
                    $DB->insert_record('user_enrolments', $enrolment);
                }
            }
        }
    }
}


/**
 * This structure steps restores the filters and their configs
 */
class restore_filters_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('active', '/filters/filter_actives/filter_active');
        $paths[] = new restore_path_element('config', '/filters/filter_configs/filter_config');

        return $paths;
    }

    public function process_active($data) {

        $data = (object)$data;

        if (!filter_is_enabled($data->filter)) { // Not installed or not enabled, nothing to do
            return;
        }
        filter_set_local_state($data->filter, $this->task->get_contextid(), $data->active);
    }

    public function process_config($data) {

        $data = (object)$data;

        if (!filter_is_enabled($data->filter)) { // Not installed or not enabled, nothing to do
            return;
        }
        filter_set_local_config($data->filter, $this->task->get_contextid(), $data->name, $data->value);
    }
}


/**
 * This structure steps restores the comments
 * Note: Cannot use the comments API because defaults to USER->id.
 * That should change allowing to pass $userid
 */
class restore_comments_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('comment', '/comments/comment');

        return $paths;
    }

    public function process_comment($data) {
        global $DB;

        $data = (object)$data;

        // First of all, if the comment has some itemid, ask to the task what to map
        $mapping = false;
        $newitemid = 0;
        if ($data->itemid) {
            $mapping = $this->task->get_comment_mapping_itemname();
            $newitemid = $this->get_mappingid($mapping, $data->itemid);
        }
        // Only restore the comment if has no mapping OR we have found the matching mapping
        if (!$mapping || $newitemid) {
            // Only if user mapping and context
            $data->userid = $this->get_mappingid('user', $data->userid);
            if ($data->userid && $this->task->get_contextid()) {
                $data->contextid = $this->task->get_contextid();
                // Only if there is another comment with same context/user/timecreated
                $params = array('contextid' => $data->contextid, 'userid' => $data->userid, 'timecreated' => $data->timecreated);
                if (!$DB->record_exists('comments', $params)) {
                    $DB->insert_record('comments', $data);
                }
            }
        }
    }
}


/**
 * This structure steps restores one instance + positions of one block
 * Note: Positions corresponding to one existing context are restored
 * here, but all the ones having unknown contexts are sent to backup_ids
 * for a later chance to be restored at the end (final task)
 */
class restore_block_instance_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('block', '/block', true); // Get the whole XML together
        $paths[] = new restore_path_element('block_position', '/block/block_positions/block_position');

        return $paths;
    }

    public function process_block($data) {
        global $DB;

        $data = (object)$data; // Handy
        $oldcontextid = $data->contextid;
        $oldid        = $data->id;
        $positions = isset($data->block_positions['block_position']) ? $data->block_positions['block_position'] : array();

        // Look for the parent contextid
        if (!$data->parentcontextid = $this->get_mappingid('context', $data->parentcontextid)) {
            throw new restore_step_exception('restore_block_missing_parent_ctx', $data->parentcontextid);
        }

        // If there is already one block of that type in the parent context
        // and the block is not multiple, stop processing
        if ($DB->record_exists_sql("SELECT bi.id
                                      FROM {block_instances} bi
                                      JOIN {block} b ON b.name = bi.blockname
                                     WHERE bi.parentcontextid = ?
                                       AND bi.blockname = ?
                                       AND b.multiple = 0", array($data->parentcontextid, $data->blockname))) {
            return false;
        }

        // If there is already one block of that type in the parent context
        // with the same showincontexts, pagetypepattern, subpagepattern, defaultregion and configdata
        // stop processing
        $params = array(
            'blockname' => $data->blockname, 'parentcontextid' => $data->parentcontextid,
            'showinsubcontexts' => $data->showinsubcontexts, 'pagetypepattern' => $data->pagetypepattern,
            'subpagepattern' => $data->subpagepattern, 'defaultregion' => $data->defaultregion);
        if ($birecs = $DB->get_records('block_instances', $params)) {
            foreach($birecs as $birec) {
                if ($birec->configdata == $data->configdata) {
                    return false;
                }
            }
        }

        // Set task old contextid, blockid and blockname once we know them
        $this->task->set_old_contextid($oldcontextid);
        $this->task->set_old_blockid($oldid);
        $this->task->set_blockname($data->blockname);

        // Let's look for anything within configdata neededing processing
        // (nulls and uses of legacy file.php)
        if ($attrstotransform = $this->task->get_configdata_encoded_attributes()) {
            $configdata = (array)unserialize(base64_decode($data->configdata));
            foreach ($configdata as $attribute => $value) {
                if (in_array($attribute, $attrstotransform)) {
                    $configdata[$attribute] = $this->contentprocessor->process_cdata($value);
                }
            }
            $data->configdata = base64_encode(serialize((object)$configdata));
        }

        // Create the block instance
        $newitemid = $DB->insert_record('block_instances', $data);
        // Save the mapping (with restorefiles support)
        $this->set_mapping('block_instance', $oldid, $newitemid, true);
        // Create the block context
        $newcontextid = get_context_instance(CONTEXT_BLOCK, $newitemid)->id;
        // Save the block contexts mapping and sent it to task
        $this->set_mapping('context', $oldcontextid, $newcontextid);
        $this->task->set_contextid($newcontextid);
        $this->task->set_blockid($newitemid);

        // Restore block fileareas if declared
        $component = 'block_' . $this->task->get_blockname();
        foreach ($this->task->get_fileareas() as $filearea) { // Simple match by contextid. No itemname needed
            $this->add_related_files($component, $filearea, null);
        }

        // Process block positions, creating them or accumulating for final step
        foreach($positions as $position) {
            $position = (object)$position;
            $position->blockinstanceid = $newitemid; // The instance is always the restored one
            // If position is for one already mapped (known) contextid
            // process it now, creating the position
            if ($newpositionctxid = $this->get_mappingid('context', $position->contextid)) {
                $position->contextid = $newpositionctxid;
                // Create the block position
                $DB->insert_record('block_positions', $position);

            // The position belongs to an unknown context, send it to backup_ids
            // to process them as part of the final steps of restore. We send the
            // whole $position object there, hence use the low level method.
            } else {
                restore_dbops::set_backup_ids_record($this->get_restoreid(), 'block_position', $position->id, 0, null, $position);
            }
        }
    }
}

/**
 * Structure step to restore common course_module information
 *
 * This step will process the module.xml file for one activity, in order to restore
 * the corresponding information to the course_modules table, skipping various bits
 * of information based on CFG settings (groupings, completion...) in order to fullfill
 * all the reqs to be able to create the context to be used by all the rest of steps
 * in the activity restore task
 */
class restore_module_structure_step extends restore_structure_step {

    protected function define_structure() {
        global $CFG;

        $paths = array();

        $paths[] = new restore_path_element('module', '/module');
        if ($CFG->enableavailability) {
            $paths[] = new restore_path_element('availability', '/module/availability_info/availability');
        }

        return $paths;
    }

    protected function process_module($data) {
        global $CFG, $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->course = $this->task->get_courseid();
        $data->module = $DB->get_field('modules', 'id', array('name' => $data->modulename));
        // Map section (first try by course_section mapping match. Useful in course and section restores)
        $data->section = $this->get_mappingid('course_section', $data->sectionid);
        if (!$data->section) { // mapping failed, try to get section by sectionnumber matching
            $params = array(
                'course' => $this->get_courseid(),
                'section' => $data->sectionnumber);
            $data->section = $DB->get_field('course_sections', 'id', $params);
        }
        if (!$data->section) { // sectionnumber failed, try to get first section in course
            $params = array(
                'course' => $this->get_courseid());
            $data->section = $DB->get_field('course_sections', 'MIN(id)', $params);
        }
        if (!$data->section) { // no sections in course, create section 0 and 1 and assign module to 1
            $sectionrec = array(
                'course' => $this->get_courseid(),
                'section' => 0);
            $DB->insert_record('course_sections', $sectionrec); // section 0
            $sectionrec = array(
                'course' => $this->get_courseid(),
                'section' => 1);
            $data->section = $DB->insert_record('course_sections', $sectionrec); // section 1
        }
        $data->groupingid= $this->get_mappingid('grouping', $data->groupingid);      // grouping
        if (!$CFG->enablegroupmembersonly) {                                         // observe groupsmemberonly
            $data->groupmembersonly = 0;
        }
        if (!grade_verify_idnumber($data->idnumber, $this->get_courseid())) {        // idnumber uniqueness
            $data->idnumber = '';
        }
        if (empty($CFG->enablecompletion) || !$this->get_setting_value('userscompletion')) { // completion
            $data->completion = 0;
            $data->completiongradeitemnumber = null;
            $data->completionview = 0;
            $data->completionexpected = 0;
        } else {
            $data->completionexpected = $this->apply_date_offset($data->completionexpected);
        }
        if (empty($CFG->enableavailability)) {
            $data->availablefrom = 0;
            $data->availableuntil = 0;
            $data->showavailability = 0;
        } else {
            $data->availablefrom = $this->apply_date_offset($data->availablefrom);
            $data->availableuntil= $this->apply_date_offset($data->availableuntil);
        }
        $data->instance = 0; // Set to 0 for now, going to create it soon (next step)

        // course_module record ready, insert it
        $newitemid = $DB->insert_record('course_modules', $data);
        // save mapping
        $this->set_mapping('course_module', $oldid, $newitemid);
        // set the new course_module id in the task
        $this->task->set_moduleid($newitemid);
        // we can now create the context safely
        $ctxid = get_context_instance(CONTEXT_MODULE, $newitemid)->id;
        // set the new context id in the task
        $this->task->set_contextid($ctxid);
        // update sequence field in course_section
        if ($sequence = $DB->get_field('course_sections', 'sequence', array('id' => $data->section))) {
            $sequence .= ',' . $newitemid;
        } else {
            $sequence = $newitemid;
        }
        $DB->set_field('course_sections', 'sequence', $sequence, array('id' => $data->section));
    }


    protected function process_availability($data) {
        // TODO: Process module availavility records
        $data = (object)$data;
    }
}

/**
 * Abstract structure step, parent of all the activity structure steps. Used to suuport
 * the main <activity ...> tag and process it. Also provides subplugin support for
 * activities.
 */
abstract class restore_activity_structure_step extends restore_structure_step {

    protected function add_subplugin_structure() {
        // TODO: Implement activities subplugin support (similar if possible to backup)
    }

    /**
     * Adds support for the 'activity' path that is common to all the activities
     * and will be processed globally here
     */
    protected function prepare_activity_structure($paths) {

        $paths[] = new restore_path_element('activity', '/activity');

        return $paths;
    }

    /**
     * Process the activity path, informing the task about various ids, needed later
     */
    protected function process_activity($data) {
        $data = (object)$data;
        $this->task->set_old_contextid($data->contextid); // Save old contextid in task
        $this->set_mapping('context', $data->contextid, $this->task->get_contextid()); // Set the mapping
        $this->task->set_old_activityid($data->id); // Save old activityid in task
    }

    /**
     * This must be invoked inmediately after creating the "module" activity record (forum, choice...)
     * and will adjust the new activity id (the instance) in various places
     */
    protected function apply_activity_instance($newitemid) {
        global $DB;

        $this->task->set_activityid($newitemid); // Save activity id in task
        // Apply the id to course_sections->instanceid
        $DB->set_field('course_modules', 'instance', $newitemid, array('id' => $this->task->get_moduleid()));
        // Do the mapping for modulename, preparing it for files by oldcontext
        $modulename = $this->task->get_modulename();
        $oldid = $this->task->get_old_activityid();
        $this->set_mapping($modulename, $oldid, $newitemid, true);
    }
}
