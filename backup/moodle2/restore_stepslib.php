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
            $data->userid = $userid ? $userid : $this->get_userid();
            // Remap the course if course scale
            $data->courseid = $data->courseid ? $this->get_courseid() : 0;
            // If global scale (course=0), check the user has perms to create it
            // falling to course scale if not
            $systemctx = get_context_instance(CONTEXT_SYSTEM);
            if ($data->courseid == 0 && !has_capability('moodle/course:managescales', $systemctx , $data->userid)) {
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
            $data->usermodified = $userid ? $userid : $this->get_userid();
            // Remap the scale
            $data->scaleid = $this->get_mappingid('scale', $data->scaleid);
            // Remap the course if course outcome
            $data->courseid = $data->courseid ? $this->get_courseid() : null;
            // If global outcome (course=null), check the user has perms to create it
            // falling to course outcome if not
            $systemctx = get_context_instance(CONTEXT_SYSTEM);
            if (is_null($data->courseid) && !has_capability('moodle/grade:manageoutcomes', $systemctx , $data->userid)) {
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
        debugging ('review the these lines of process_course() to change to settings once available', DEBUG_DEVELOPER);
        // TODO: Get fullname, shortname, startdate and category from settings
        // $fullname  = $this->get_setting_value('course_fullname');
        // $shortname = $this->get_setting_value('course_shortname');
        // $category  = $this->get_setting_value('course_category');
        // $startdate = $this->get_setting_value('course_startdate');
        // TODO: Delete this lines once we are getting the vars above from settings
        $fullname = $this->task->get_info()->original_course_fullname;
        $shortname= $this->task->get_info()->original_course_shortname;
        $startdate= $this->task->get_info()->original_course_startdate;
        $category = get_course_category()->id;

        // Calculate final course names, to avoid dupes
        list($fullname, $shortname) = restore_dbops::calculate_course_names($this->get_courseid(), $fullname, $shortname);

        // Need to change some fields before updating the course record
        $data->id = $this->get_courseid();
        $data->fullname = $fullname;
        $data->shortname= $shortname;
        $data->idnumber = '';
        $data->category = $category;
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
        // If newroleid and newuserid and component is empty assign via API (handles dupes and friends)
        if ($newroleid && $newuserid && empty($data->component)) {
            // TODO: role_assign() needs one userid param to be able to specify our restore userid
            role_assign($newroleid, $newuserid, $this->task->get_contextid());
        }
    }

    public function process_override($data) {
        $data = (object)$data;

        // Check roleid is one of the mapped ones
        $newroleid = $this->get_mappingid('role', $data->roleid);
        // If newroleid is valid assign it via API (it handles dupes and so on)
        if ($newroleid) {
            // TODO: assign_capability() needs one userid param to be able to specify our restore userid
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
            if ($data->userid = $this->get_mappingid('user', $data->userid)) {
                $data->contextid = $this->task->get_contextid();
                $DB->insert_record('comments', $data);
            }
        }
    }
}
