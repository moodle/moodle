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
        // Create the old-course-id to new-course-id mapping, we need that available since the beginning
        $itemid = $this->task->get_old_courseid();
        $newitemid = $this->get_courseid();
        restore_dbops::set_backup_ids_record($this->get_restoreid(), 'course', $itemid, $newitemid);

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
 * Restore calculated grade items, grade categories etc
 */
class restore_gradebook_structure_step extends restore_structure_step {

    /**
     * To conditionally decide if this step must be executed
     * Note the "settings" conditions are evaluated in the
     * corresponding task. Here we check for other conditions
     * not being restore settings (files, site settings...)
     */
     protected function execute_condition() {
        global $CFG, $DB;

        // No gradebook info found, don't execute
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            return false;
        }

        // Some module present in backup file isn't available to restore
        // in this site, don't execute
        if ($this->task->is_missing_modules()) {
            return false;
        }

        // Some activity has been excluded to be restored, don't execute
        if ($this->task->is_excluding_activities()) {
            return false;
        }

        // There should only be one grade category (the 1 associated with the course itself)
        // If other categories already exist we're restoring into an existing course.
        // Restoring categories into a course with an existing category structure is unlikely to go well
        $category = new stdclass();
        $category->courseid  = $this->get_courseid();
        $catcount = $DB->count_records('grade_categories', (array)$category);
        if ($catcount>1) {
            return false;
        }

        // Arrived here, execute the step
        return true;
     }

    protected function define_structure() {
        $paths = array();
        $userinfo = $this->task->get_setting_value('users');

        $paths[] = new restore_path_element('gradebook', '/gradebook');
        $paths[] = new restore_path_element('grade_category', '/gradebook/grade_categories/grade_category');
        $paths[] = new restore_path_element('grade_item', '/gradebook/grade_items/grade_item');
        if ($userinfo) {
            $paths[] = new restore_path_element('grade_grade', '/gradebook/grade_items/grade_item/grade_grades/grade_grade');
        }
        $paths[] = new restore_path_element('grade_letter', '/gradebook/grade_letters/grade_letter');
        $paths[] = new restore_path_element('grade_setting', '/gradebook/grade_settings/grade_setting');

        return $paths;
    }

    protected function process_gradebook($data) {
    }

    protected function process_grade_item($data) {
        global $DB;

        $data = (object)$data;

        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->courseid = $this->get_courseid();

        if ($data->itemtype=='manual') {
            // manual grade items store category id in categoryid
            $data->categoryid = $this->get_mappingid('grade_category', $data->categoryid, NULL);
        } else if ($data->itemtype=='course') {
            // course grade item stores their category id in iteminstance
            $coursecat = grade_category::fetch_course_category($this->get_courseid());
            $data->iteminstance = $coursecat->id;
        } else if ($data->itemtype=='category') {
            // category grade items store their category id in iteminstance
            $data->iteminstance = $this->get_mappingid('grade_category', $data->iteminstance, NULL);
        } else {
            throw new restore_step_exception('unexpected_grade_item_type', $data->itemtype);
        }

        $data->scaleid   = $this->get_mappingid('scale', $data->scaleid, NULL);
        $data->outcomeid = $this->get_mappingid('outcome', $data->outcomeid, NULL);

        $data->locktime     = $this->apply_date_offset($data->locktime);
        $data->timecreated  = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $coursecategory = $newitemid = null;
        //course grade item should already exist so updating instead of inserting
        if($data->itemtype=='course') {
            //get the ID of the already created grade item
            $gi = new stdclass();
            $gi->courseid  = $this->get_courseid();
            $gi->itemtype  = $data->itemtype;

            //need to get the id of the grade_category that was automatically created for the course
            $category = new stdclass();
            $category->courseid  = $this->get_courseid();
            $category->parent  = null;
            //course category fullname starts out as ? but may be edited
            //$category->fullname  = '?';
            $coursecategory = $DB->get_record('grade_categories', (array)$category);
            $gi->iteminstance = $coursecategory->id;

            $existinggradeitem = $DB->get_record('grade_items', (array)$gi);
            if (!empty($existinggradeitem)) {
                $data->id = $newitemid = $existinggradeitem->id;
                $DB->update_record('grade_items', $data);
            }
        }

        if (empty($newitemid)) {
            //in case we found the course category but still need to insert the course grade item
            if ($data->itemtype=='course' && !empty($coursecategory)) {
                $data->iteminstance = $coursecategory->id;
            }

            $newitemid = $DB->insert_record('grade_items', $data);
        }
        $this->set_mapping('grade_item', $oldid, $newitemid);
    }

    protected function process_grade_grade($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->itemid = $this->get_new_parentid('grade_item');

        $data->userid = $this->get_mappingid('user', $data->userid, NULL);
        $data->usermodified = $this->get_mappingid('user', $data->usermodified, NULL);
        $data->locktime     = $this->apply_date_offset($data->locktime);
        // TODO: Ask, all the rest of locktime/exported... work with time... to be rolled?
        $data->overridden = $this->apply_date_offset($data->overridden);
        $data->timecreated  = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('grade_grades', $data);
        //$this->set_mapping('grade_grade', $oldid, $newitemid);
    }
    protected function process_grade_category($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->course = $this->get_courseid();
        $data->courseid = $data->course;

        $data->timecreated  = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = null;
        //no parent means a course level grade category. That may have been created when the course was created
        if(empty($data->parent)) {
            //parent was being saved as 0 when it should be null
            $data->parent = null;

            //get the already created course level grade category
            $category = new stdclass();
            $category->courseid = $this->get_courseid();
            $category->parent = null;

            $coursecategory = $DB->get_record('grade_categories', (array)$category);
            if (!empty($coursecategory)) {
                $data->id = $newitemid = $coursecategory->id;
                $DB->update_record('grade_categories', $data);
            }
        }

        //need to insert a course category
        if (empty($newitemid)) {
            $newitemid = $DB->insert_record('grade_categories', $data);
        }
        $this->set_mapping('grade_category', $oldid, $newitemid);
    }
    protected function process_grade_letter($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->contextid = get_context_instance(CONTEXT_COURSE, $this->get_courseid())->id;

        $newitemid = $DB->insert_record('grade_letters', $data);
        $this->set_mapping('grade_letter', $oldid, $newitemid);
    }
    protected function process_grade_setting($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->courseid = $this->get_courseid();

        $newitemid = $DB->insert_record('grade_settings', $data);
        //$this->set_mapping('grade_setting', $oldid, $newitemid);
    }

    /**
     * put all activity grade items in the correct grade category and mark all for recalculation
     */
    protected function after_execute() {
        global $DB;

        $conditions = array(
            'backupid' => $this->get_restoreid(),
            'itemname' => 'grade_item'//,
            //'itemid'   => $itemid
        );
        $rs = $DB->get_recordset('backup_ids_temp', $conditions);

        // We need this for calculation magic later on.
        $mappings = array();

        if (!empty($rs)) {
            foreach($rs as $grade_item_backup) {

                // Store the oldid with the new id.
                $mappings[$grade_item_backup->itemid] = $grade_item_backup->newitemid;

                $updateobj = new stdclass();
                $updateobj->id = $grade_item_backup->newitemid;

                //if this is an activity grade item that needs to be put back in its correct category
                if (!empty($grade_item_backup->parentitemid)) {
                    $oldcategoryid = $this->get_mappingid('grade_category', $grade_item_backup->parentitemid, null);
                    if (!is_null($oldcategoryid)) {
                        $updateobj->categoryid = $oldcategoryid;
                        $DB->update_record('grade_items', $updateobj);
                    }
                } else {
                    //mark course and category items as needing to be recalculated
                    $updateobj->needsupdate=1;
                    $DB->update_record('grade_items', $updateobj);
                }
            }
        }
        $rs->close();

        // We need to update the calculations for calculated grade items that may reference old
        // grade item ids using ##gi\d+##.
        // $mappings can be empty, use 0 if so (won't match ever)
        list($sql, $params) = $DB->get_in_or_equal(array_values($mappings), SQL_PARAMS_NAMED, 'param', true, 0);
        $sql = "SELECT gi.id, gi.calculation
                  FROM {grade_items} gi
                 WHERE gi.id {$sql} AND
                       calculation IS NOT NULL";
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $gradeitem) {
            // Collect all of the used grade item id references
            if (preg_match_all('/##gi(\d+)##/', $gradeitem->calculation, $matches) < 1) {
                // This calculation doesn't reference any other grade items... EASY!
                continue;
            }
            // For this next bit we are going to do the replacement of id's in two steps:
            // 1. We will replace all old id references with a special mapping reference.
            // 2. We will replace all mapping references with id's
            // Why do we do this?
            // Because there potentially there will be an overlap of ids within the query and we
            // we substitute the wrong id.. safest way around this is the two step system
            $calculationmap = array();
            $mapcount = 0;
            foreach ($matches[1] as $match) {
                // Check that the old id is known to us, if not it was broken to begin with and will
                // continue to be broken.
                if (!array_key_exists($match, $mappings)) {
                    continue;
                }
                // Our special mapping key
                $mapping = '##MAPPING'.$mapcount.'##';
                // The old id that exists within the calculation now
                $oldid = '##gi'.$match.'##';
                // The new id that we want to replace the old one with.
                $newid = '##gi'.$mappings[$match].'##';
                // Replace in the special mapping key
                $gradeitem->calculation = str_replace($oldid, $mapping, $gradeitem->calculation);
                // And record the mapping
                $calculationmap[$mapping] = $newid;
                $mapcount++;
            }
            // Iterate all special mappings for this calculation and replace in the new id's
            foreach ($calculationmap as $mapping => $newid) {
                $gradeitem->calculation = str_replace($mapping, $newid, $gradeitem->calculation);
            }
            // Update the calculation now that its being remapped
            $DB->update_record('grade_items', $gradeitem);
        }
        $rs->close();

        // Need to correct the grade category path and parent
        $conditions = array(
            'courseid' => $this->get_courseid()
        );

        $rs = $DB->get_recordset('grade_categories', $conditions);
        // Get all the parents correct first as grade_category::build_path() loads category parents from the DB
        foreach ($rs as $gc) {
            if (!empty($gc->parent)) {
                $grade_category = new stdClass();
                $grade_category->id = $gc->id;
                $grade_category->parent = $this->get_mappingid('grade_category', $gc->parent);
                $DB->update_record('grade_categories', $grade_category);
            }
        }
        $rs->close();

        // Now we can rebuild all the paths
        $rs = $DB->get_recordset('grade_categories', $conditions);
        foreach ($rs as $gc) {
            $grade_category = new stdClass();
            $grade_category->id = $gc->id;
            $grade_category->path = grade_category::build_path($gc);
            $grade_category->depth = substr_count($grade_category->path, '/') - 1;
            $DB->update_record('grade_categories', $grade_category);
        }
        $rs->close();

        // Restore marks items as needing update. Update everything now.
        grade_regrade_final_grades($this->get_courseid());
    }
}

/**
 * decode all the interlinks present in restored content
 * relying 100% in the restore_decode_processor that handles
 * both the contents to modify and the rules to be applied
 */
class restore_decode_interlinks extends restore_execution_step {

    protected function define_execution() {
        // Get the decoder (from the plan)
        $decoder = $this->task->get_decoder();
        restore_decode_processor::register_link_decoders($decoder); // Add decoder contents and rules
        // And launch it, everything will be processed
        $decoder->execute();
    }
}

/**
 * first, ensure that we have no gaps in section numbers
 * and then, rebuid the course cache
 */
class restore_rebuild_course_cache extends restore_execution_step {

    protected function define_execution() {
        global $DB;

        // Although there is some sort of auto-recovery of missing sections
        // present in course/formats... here we check that all the sections
        // from 0 to MAX(section->section) exist, creating them if necessary
        $maxsection = $DB->get_field('course_sections', 'MAX(section)', array('course' => $this->get_courseid()));
        // Iterate over all sections
        for ($i = 0; $i <= $maxsection; $i++) {
            // If the section $i doesn't exist, create it
            if (!$DB->record_exists('course_sections', array('course' => $this->get_courseid(), 'section' => $i))) {
                $sectionrec = array(
                    'course' => $this->get_courseid(),
                    'section' => $i);
                $DB->insert_record('course_sections', $sectionrec); // missing section created
            }
        }

        // Rebuild cache now that all sections are in place
        rebuild_course_cache($this->get_courseid());
    }
}

/**
 * Review all the tasks having one after_restore method
 * executing it to perform some final adjustments of information
 * not available when the task was executed.
 */
class restore_execute_after_restore extends restore_execution_step {

    protected function define_execution() {

        // Simply call to the execute_after_restore() method of the task
        // that always is the restore_final_task
        $this->task->launch_execute_after_restore();
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

/**
 * Process all the saved module availability records in backup_ids, matching
 * course modules and grade item id once all them have been already restored.
 * only if all matchings are satisfied the availability condition will be created.
 * At the same time, it is required for the site to have that functionality enabled.
 */
class restore_process_course_modules_availability extends restore_execution_step {

    protected function define_execution() {
        global $CFG, $DB;

        // Site hasn't availability enabled
        if (empty($CFG->enableavailability)) {
            return;
        }

        // Get all the module_availability objects to process
        $params = array('backupid' => $this->get_restoreid(), 'itemname' => 'module_availability');
        $rs = $DB->get_recordset('backup_ids_temp', $params, '', 'itemid');
        // Process availabilities, creating them if everything matches ok
        foreach($rs as $availrec) {
            $allmatchesok = true;
            // Get the complete availabilityobject
            $availability = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'module_availability', $availrec->itemid)->info;
            // Map the sourcecmid if needed and possible
            if (!empty($availability->sourcecmid)) {
                $newcm = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'course_module', $availability->sourcecmid);
                if ($newcm) {
                    $availability->sourcecmid = $newcm->newitemid;
                } else {
                    $allmatchesok = false; // Failed matching, we won't create this availability rule
                }
            }
            // Map the gradeitemid if needed and possible
            if (!empty($availability->gradeitemid)) {
                $newgi = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'grade_item', $availability->gradeitemid);
                if ($newgi) {
                    $availability->gradeitemid = $newgi->newitemid;
                } else {
                    $allmatchesok = false; // Failed matching, we won't create this availability rule
                }
            }
            if ($allmatchesok) { // Everything ok, create the availability rule
                $DB->insert_record('course_modules_availability', $availability);
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

        // Get all the included tasks
        $tasks = restore_dbops::get_included_tasks($this->get_restoreid());
        foreach ($tasks as $task) {
            // Load the inforef.xml file if exists
            $inforefpath = $task->get_taskbasepath() . '/inforef.xml';
            if (file_exists($inforefpath)) {
                restore_dbops::load_inforef_to_tempids($this->get_restoreid(), $inforefpath); // Load each inforef file to temp_ids
            }
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
        //   - it is one "user", "group", "grouping", "grade", "question" or "qtype_xxxx" component file (that aren't sent to inforef ever)
        // TODO: qtype_xxx should be replaced by proper backup_qtype_plugin::get_components_and_fileareas() use,
        //       but then we'll need to change it to load plugins itself (because this is executed too early in restore)
        $isfileref   = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'fileref', $data->id);
        $iscomponent = ($data->component == 'user' || $data->component == 'group' ||
                        $data->component == 'grouping' || $data->component == 'grade' ||
                        $data->component == 'question' || substr($data->component, 0, 5) == 'qtype');
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

        restore_dbops::create_included_users($this->get_basepath(), $this->get_restoreid(), $this->get_setting_value('user_files'), $this->task->get_userid());
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
                                  $DB->sql_compare_text('description') . ' = ' . $DB->sql_compare_text(':description');
           $params['description'] = $data->description;
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
                                  $DB->sql_compare_text('description') . ' = ' . $DB->sql_compare_text(':description');
           $params['description'] = $data->description;
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

        $params = array();
        $params['groupingid'] = $data->groupingid;
        $params['groupid']    = $data->groupid;

        if (!$DB->record_exists('groupings_groups', $params)) {
            $DB->insert_record('groupings_groups', $data);  // No need to set this mapping (no child info nor files)
        }
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
 * Execution step that, *conditionally* (if there isn't preloaded information
 * will load all the question categories and questions (header info only)
 * to backup_temp_ids. They will be stored with "question_category" and
 * "question" itemnames and with their original contextid and question category
 * id as paremitemids
 */
class restore_load_categories_and_questions extends restore_execution_step {

    protected function define_execution() {

        if ($this->task->get_preloaded_information()) { // if info is already preloaded, nothing to do
            return;
        }
        $file = $this->get_basepath() . '/questions.xml';
        restore_dbops::load_categories_and_questions_to_tempids($this->get_restoreid(), $file);
    }
}

/**
 * Execution step that, *conditionally* (if there isn't preloaded information)
 * will process all the needed categories and questions
 * in order to decide and perform any action with them (create / map / error)
 * Note: Any error will cause exception, as far as this is the same processing
 * than the one into restore prechecks (that should have stopped process earlier)
 */
class restore_process_categories_and_questions extends restore_execution_step {

    protected function define_execution() {

        if ($this->task->get_preloaded_information()) { // if info is already preloaded, nothing to do
            return;
        }
        restore_dbops::process_categories_and_questions($this->get_restoreid(), $this->task->get_courseid(), $this->task->get_userid(), $this->task->is_samesite());
    }
}

/**
 * Structure step that will read the section.xml creating/updating sections
 * as needed, rebuilding course cache and other friends
 */
class restore_section_structure_step extends restore_structure_step {

    protected function define_structure() {
        $section = new restore_path_element('section', '/section');

        // Apply for 'format' plugins optional paths at section level
        $this->add_plugin_structure('format', $section);

        return array($section);
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

        // set the new course_section id in the task
        $this->task->set_sectionid($newitemid);


        // Commented out. We never modify course->numsections as far as that is used
        // by a lot of people to "hide" sections on purpose (so this remains as used to be in Moodle 1.x)
        // Note: We keep the code here, to know about and because of the possibility of making this
        // optional based on some setting/attribute in the future
        // If needed, adjust course->numsections
        //if ($numsections = $DB->get_field('course', 'numsections', array('id' => $this->get_courseid()))) {
        //    if ($numsections < $section->section) {
        //        $DB->set_field('course', 'numsections', $section->section, array('id' => $this->get_courseid()));
        //    }
        //}
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

        $course = new restore_path_element('course', '/course');
        $category = new restore_path_element('category', '/course/category');
        $tag = new restore_path_element('tag', '/course/tags/tag');
        $allowed_module = new restore_path_element('allowed_module', '/course/allowed_modules/module');

        // Apply for 'format' plugins optional paths at course level
        $this->add_plugin_structure('format', $course);

        // Apply for 'theme' plugins optional paths at course level
        $this->add_plugin_structure('theme', $course);

        // Apply for 'course report' plugins optional paths at course level
        $this->add_plugin_structure('coursereport', $course);

        // Apply for plagiarism plugins optional paths at course level
        $this->add_plugin_structure('plagiarism', $course);

        return array($course, $category, $tag, $allowed_module);
    }

    /**
     * Processing functions go here
     *
     * @global moodledatabase $DB
     * @param stdClass $data
     */
    public function process_course($data) {
        global $CFG, $DB;

        $data = (object)$data;

        $fullname  = $this->get_setting_value('course_fullname');
        $shortname = $this->get_setting_value('course_shortname');
        $startdate = $this->get_setting_value('course_startdate');

        // Calculate final course names, to avoid dupes
        list($fullname, $shortname) = restore_dbops::calculate_course_names($this->get_courseid(), $fullname, $shortname);

        // Need to change some fields before updating the course record
        $data->id = $this->get_courseid();
        $data->fullname = $fullname;
        $data->shortname= $shortname;

        $context = get_context_instance_by_id($this->task->get_contextid());
        if (has_capability('moodle/course:changeidnumber', $context, $this->task->get_userid())) {
            $data->idnumber = '';
        } else {
            unset($data->idnumber);
        }

        // Any empty value for course->hiddensections will lead to 0 (default, show collapsed).
        // It has been reported that some old 1.9 courses may have it null leading to DB error. MDL-31532
        if (empty($data->hiddensections)) {
            $data->hiddensections = 0;
        }

        // Only restrict modules if original course was and target site too for new courses
        $data->restrictmodules = $data->restrictmodules && !empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor == 'all';

        $data->startdate= $this->apply_date_offset($data->startdate);
        if ($data->defaultgroupingid) {
            $data->defaultgroupingid = $this->get_mappingid('grouping', $data->defaultgroupingid);
        }
        if (empty($CFG->enablecompletion)) {
            $data->enablecompletion = 0;
            $data->completionstartonenrol = 0;
            $data->completionnotify = 0;
        }
        $languages = get_string_manager()->get_list_of_translations(); // Get languages for quick search
        if (!array_key_exists($data->lang, $languages)) {
            $data->lang = '';
        }

        $themes = get_list_of_themes(); // Get themes for quick search later
        if (!array_key_exists($data->theme, $themes) || empty($CFG->allowcoursethemes)) {
            $data->theme = '';
        }

        // Course record ready, update it
        $DB->update_record('course', $data);

        // Role name aliases
        restore_dbops::set_course_role_names($this->get_restoreid(), $this->get_courseid());
    }

    public function process_category($data) {
        // Nothing to do with the category. UI sets it before restore starts
    }

    public function process_tag($data) {
        global $CFG, $DB;

        $data = (object)$data;

        if (!empty($CFG->usetags)) { // if enabled in server
            // TODO: This is highly inneficient. Each time we add one tag
            // we fetch all the existing because tag_set() deletes them
            // so everything must be reinserted on each call
            $tags = array();
            $existingtags = tag_get_tags('course', $this->get_courseid());
            // Re-add all the existitng tags
            foreach ($existingtags as $existingtag) {
                $tags[] = $existingtag->rawname;
            }
            // Add the one being restored
            $tags[] = $data->rawname;
            // Send all the tags back to the course
            tag_set('course', $this->get_courseid(), $tags);
        }
    }

    public function process_allowed_module($data) {
        global $CFG, $DB;

        $data = (object)$data;

        // only if enabled by admin setting
        if (!empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor == 'all') {
            $available = get_plugin_list('mod');
            $mname = $data->modulename;
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

    /**
     * Assign roles
     *
     * This has to be called after enrolments processing.
     *
     * @param mixed $data
     * @return void
     */
    public function process_assignment($data) {
        global $DB;

        $data = (object)$data;

        // Check roleid, userid are one of the mapped ones
        if (!$newroleid = $this->get_mappingid('role', $data->roleid)) {
            return;
        }
        if (!$newuserid = $this->get_mappingid('user', $data->userid)) {
            return;
        }
        if (!$DB->record_exists('user', array('id' => $newuserid, 'deleted' => 0))) {
            // Only assign roles to not deleted users
            return;
        }
        if (!$contextid = $this->task->get_contextid()) {
            return;
        }

        if (empty($data->component)) {
            // assign standard manual roles
            // TODO: role_assign() needs one userid param to be able to specify our restore userid
            role_assign($newroleid, $newuserid, $contextid);

        } else if ((strpos($data->component, 'enrol_') === 0)) {
            // Deal with enrolment roles
            if ($enrolid = $this->get_mappingid('enrol', $data->itemid)) {
                if ($component = $DB->get_field('enrol', 'component', array('id'=>$enrolid))) {
                    //note: we have to verify component because it might have changed
                    if ($component === 'enrol_manual') {
                        // manual is a special case, we do not use components - this owudl happen when converting from other plugin
                        role_assign($newroleid, $newuserid, $contextid); //TODO: do we need modifierid?
                    } else {
                        role_assign($newroleid, $newuserid, $contextid, $component, $enrolid); //TODO: do we need modifierid?
                    }
                }
            }
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

    /**
     * Conditionally decide if this step should be executed.
     *
     * This function checks the following parameter:
     *
     *   1. the course/enrolments.xml file exists
     *
     * @return bool true is safe to execute, false otherwise
     */
    protected function execute_condition() {

        // Check it is included in the backup
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            // Not found, can't restore enrolments info
            return false;
        }

        return true;
    }

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('enrol', '/enrolments/enrols/enrol');
        $paths[] = new restore_path_element('enrolment', '/enrolments/enrols/enrol/user_enrolments/enrolment');

        return $paths;
    }

    /**
     * Create enrolment instances.
     *
     * This has to be called after creation of roles
     * and before adding of role assignments.
     *
     * @param mixed $data
     * @return void
     */
    public function process_enrol($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id; // We'll need this later

        $restoretype = plugin_supports('enrol', $data->enrol, ENROL_RESTORE_TYPE, null);

        if ($restoretype !== ENROL_RESTORE_EXACT and $restoretype !== ENROL_RESTORE_NOUSERS) {
            // TODO: add complex restore support via custom class
            debugging("Skipping '{$data->enrol}' enrolment plugin. Will be implemented before 2.0 release", DEBUG_DEVELOPER);
            $this->set_mapping('enrol', $oldid, 0);
            return;
        }

        // Perform various checks to decide what to do with the enrol plugin
        if (!array_key_exists($data->enrol, enrol_get_plugins(false))) {
            // TODO: decide if we want to switch to manual enrol - we need UI for this
            debugging("Enrol plugin data can not be restored because it is not installed");
            $this->set_mapping('enrol', $oldid, 0);
            return;

        }
        if (!enrol_is_enabled($data->enrol)) {
            // TODO: decide if we want to switch to manual enrol - we need UI for this
            debugging("Enrol plugin data can not be restored because it is not enabled");
            $this->set_mapping('enrol', $oldid, 0);
            return;
        }

        // map standard fields - plugin has to process custom fields from own restore class
        $data->roleid = $this->get_mappingid('role', $data->roleid);
        //TODO: should we move the enrol start and end date here?

        // always add instance, if the course does not support multiple instances it just returns NULL
        $enrol = enrol_get_plugin($data->enrol);
        $courserec = $DB->get_record('course', array('id' => $this->get_courseid())); // Requires object, uses only id!!
        if ($newitemid = $enrol->add_instance($courserec, (array)$data)) {
            // ok
        } else {
            if ($instances = $DB->get_records('enrol', array('courseid'=>$courserec->id, 'enrol'=>$data->enrol))) {
                // most probably plugin that supports only one instance
                $newitemid = key($instances);
            } else {
                debugging('Can not create new enrol instance or reuse existing');
                $newitemid = 0;
            }
        }

        if ($restoretype === ENROL_RESTORE_NOUSERS) {
            // plugin requests to prevent restore of any users
            $newitemid = 0;
        }

        $this->set_mapping('enrol', $oldid, $newitemid);
    }

    /**
     * Create user enrolments
     *
     * This has to be called after creation of enrolment instances
     * and before adding of role assignments.
     *
     * @param mixed $data
     * @return void
     */
    public function process_enrolment($data) {
        global $DB;

        $data = (object)$data;

        // Process only if parent instance have been mapped
        if ($enrolid = $this->get_new_parentid('enrol')) {
            if ($instance = $DB->get_record('enrol', array('id'=>$enrolid))) {
                // And only if user is a mapped one
                if ($userid = $this->get_mappingid('user', $data->userid)) {
                    $enrol = enrol_get_plugin($instance->enrol);
                    //TODO: do we need specify modifierid?
                    $enrol->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, $data->status);
                    //note: roles are assigned in restore_ras_and_caps_structure_step::process_assignment() processing above
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
        if ($data->itemid) {
            $mapping = $this->task->get_comment_mapping_itemname($data->commentarea);
            $data->itemid = $this->get_mappingid($mapping, $data->itemid);
        }
        // Only restore the comment if has no mapping OR we have found the matching mapping
        if (!$mapping || $data->itemid) {
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

class restore_course_completion_structure_step extends restore_structure_step {

    /**
     * Conditionally decide if this step should be executed.
     *
     * This function checks parameters that are not immediate settings to ensure
     * that the enviroment is suitable for the restore of course completion info.
     *
     * This function checks the following four parameters:
     *
     *   1. Course completion is enabled on the site
     *   2. The backup includes course completion information
     *   3. All modules are restorable
     *   4. All modules are marked for restore.
     *
     * @return bool True is safe to execute, false otherwise
     */
    protected function execute_condition() {
        global $CFG;

        // First check course completion is enabled on this site
        if (empty($CFG->enablecompletion)) {
            // Disabled, don't restore course completion
            return false;
        }

        // Check it is included in the backup
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            // Not found, can't restore course completion
            return false;
        }

        // Check we are able to restore all backed up modules
        if ($this->task->is_missing_modules()) {
            return false;
        }

        // Finally check all modules within the backup are being restored.
        if ($this->task->is_excluding_activities()) {
            return false;
        }

        return true;
    }

    /**
     * Define the course completion structure
     *
     * @return array Array of restore_path_element
     */
    protected function define_structure() {

        // To know if we are including user completion info
        $userinfo = $this->get_setting_value('userscompletion');

        $paths = array();
        $paths[] = new restore_path_element('course_completion_criteria', '/course_completion/course_completion_criteria');
        $paths[] = new restore_path_element('course_completion_notify', '/course_completion/course_completion_notify');
        $paths[] = new restore_path_element('course_completion_aggr_methd', '/course_completion/course_completion_aggr_methd');

        if ($userinfo) {
            $paths[] = new restore_path_element('course_completion_crit_compl', '/course_completion/course_completion_criteria/course_completion_crit_completions/course_completion_crit_compl');
            $paths[] = new restore_path_element('course_completions', '/course_completion/course_completions');
        }

        return $paths;

    }

    /**
     * Process course completion criteria
     *
     * @global moodle_database $DB
     * @param stdClass $data
     */
    public function process_course_completion_criteria($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();

        // Apply the date offset to the time end field
        $data->timeend = $this->apply_date_offset($data->timeend);

        // Map the role from the criteria
        if (!empty($data->role)) {
            $data->role = $this->get_mappingid('role', $data->role);
        }

        $skipcriteria = false;

        // If the completion criteria is for a module we need to map the module instance
        // to the new module id.
        if (!empty($data->moduleinstance) && !empty($data->module)) {
            $data->moduleinstance = $this->get_mappingid('course_module', $data->moduleinstance);
            if (empty($data->moduleinstance)) {
                $skipcriteria = true;
            }
        } else {
            $data->module = null;
            $data->moduleinstance = null;
        }

        // We backup the course shortname rather than the ID so that we can match back to the course
        if (!empty($data->courseinstanceshortname)) {
            $courseinstanceid = $DB->get_field('course', 'id', array('shortname'=>$data->courseinstanceshortname));
            if (!$courseinstanceid) {
                $skipcriteria = true;
            }
        } else {
            $courseinstanceid = null;
        }
        $data->courseinstance = $courseinstanceid;

        if (!$skipcriteria) {
            $params = array(
                'course'         => $data->course,
                'criteriatype'   => $data->criteriatype,
                'enrolperiod'    => $data->enrolperiod,
                'courseinstance' => $data->courseinstance,
                'module'         => $data->module,
                'moduleinstance' => $data->moduleinstance,
                'timeend'        => $data->timeend,
                'gradepass'      => $data->gradepass,
                'role'           => $data->role
            );
            $newid = $DB->insert_record('course_completion_criteria', $params);
            $this->set_mapping('course_completion_criteria', $data->id, $newid);
        }
    }

    /**
     * Processes course compltion criteria complete records
     *
     * @global moodle_database $DB
     * @param stdClass $data
     */
    public function process_course_completion_crit_compl($data) {
        global $DB;

        $data = (object)$data;

        // This may be empty if criteria could not be restored
        $data->criteriaid = $this->get_mappingid('course_completion_criteria', $data->criteriaid);

        $data->course = $this->get_courseid();
        $data->userid = $this->get_mappingid('user', $data->userid);

        if (!empty($data->criteriaid) && !empty($data->userid)) {
            $params = array(
                'userid' => $data->userid,
                'course' => $data->course,
                'criteriaid' => $data->criteriaid,
                'timecompleted' => $this->apply_date_offset($data->timecompleted)
            );
            if (isset($data->gradefinal)) {
                $params['gradefinal'] = $data->gradefinal;
            }
            if (isset($data->unenroled)) {
                $params['unenroled'] = $data->unenroled;
            }
            if (isset($data->deleted)) {
                $params['deleted'] = $data->deleted;
            }
            $DB->insert_record('course_completion_crit_compl', $params);
        }
    }

    /**
     * Process course completions
     *
     * @global moodle_database $DB
     * @param stdClass $data
     */
    public function process_course_completions($data) {
        global $DB;

        $data = (object)$data;

        $data->course = $this->get_courseid();
        $data->userid = $this->get_mappingid('user', $data->userid);

        if (!empty($data->userid)) {
            $params = array(
                'userid' => $data->userid,
                'course' => $data->course,
                'deleted' => $data->deleted,
                'timenotified' => $this->apply_date_offset($data->timenotified),
                'timeenrolled' => $this->apply_date_offset($data->timeenrolled),
                'timestarted' => $this->apply_date_offset($data->timestarted),
                'timecompleted' => $this->apply_date_offset($data->timecompleted),
                'reaggregate' => $data->reaggregate
            );
            $DB->insert_record('course_completions', $params);
        }
    }

    /**
     * Process course completion notification records.
     *
     * Note: As of Moodle 2.0 this table is not being used however it has been
     * left in in the hopes that one day the functionality there will be completed
     *
     * @global moodle_database $DB
     * @param stdClass $data
     */
    public function process_course_completion_notify($data) {
        global $DB;

        $data = (object)$data;

        $data->course = $this->get_courseid();
        if (!empty($data->role)) {
            $data->role = $this->get_mappingid('role', $data->role);
        }

        $params = array(
            'course' => $data->course,
            'role' => $data->role,
            'message' => $data->message,
            'timesent' => $this->apply_date_offset($data->timesent),
        );
        $DB->insert_record('course_completion_notify', $params);
    }

    /**
     * Process course completion aggregate methods
     *
     * @global moodle_database $DB
     * @param stdClass $data
     */
    public function process_course_completion_aggr_methd($data) {
        global $DB;

        $data = (object)$data;

        $data->course = $this->get_courseid();

        // Only create the course_completion_aggr_methd records if
        // the target course has not them defined. MDL-28180
        if (!$DB->record_exists('course_completion_aggr_methd', array(
                    'course' => $data->course,
                    'criteriatype' => $data->criteriatype))) {
            $params = array(
                'course' => $data->course,
                'criteriatype' => $data->criteriatype,
                'method' => $data->method,
                'value' => $data->value,
            );
            $DB->insert_record('course_completion_aggr_methd', $params);
        }
    }
}


/**
 * This structure step restores course logs (cmid = 0), delegating
 * the hard work to the corresponding {@link restore_logs_processor} passing the
 * collection of {@link restore_log_rule} rules to be observed as they are defined
 * by the task. Note this is only executed based in the 'logs' setting.
 *
 * NOTE: This is executed by final task, to have all the activities already restored
 *
 * NOTE: Not all course logs are being restored. For now only 'course' and 'user'
 * records are. There are others like 'calendar' and 'upload' that will be handled
 * later.
 *
 * NOTE: All the missing actions (not able to be restored) are sent to logs for
 * debugging purposes
 */
class restore_course_logs_structure_step extends restore_structure_step {

    /**
     * Conditionally decide if this step should be executed.
     *
     * This function checks the following parameter:
     *
     *   1. the course/logs.xml file exists
     *
     * @return bool true is safe to execute, false otherwise
     */
    protected function execute_condition() {

        // Check it is included in the backup
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            // Not found, can't restore course logs
            return false;
        }

        return true;
    }

    protected function define_structure() {

        $paths = array();

        // Simple, one plain level of information contains them
        $paths[] = new restore_path_element('log', '/logs/log');

        return $paths;
    }

    protected function process_log($data) {
        global $DB;

        $data = (object)($data);

        $data->time = $this->apply_date_offset($data->time);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->course = $this->get_courseid();
        $data->cmid = 0;

        // For any reason user wasn't remapped ok, stop processing this
        if (empty($data->userid)) {
            return;
        }

        // Everything ready, let's delegate to the restore_logs_processor

        // Set some fixed values that will save tons of DB requests
        $values = array(
            'course' => $this->get_courseid());
        // Get instance and process log record
        $data = restore_logs_processor::get_instance($this->task, $values)->process_log_record($data);

        // If we have data, insert it, else something went wrong in the restore_logs_processor
        if ($data) {
            $DB->insert_record('log', $data);
        }
    }
}

/**
 * This structure step restores activity logs, extending {@link restore_course_logs_structure_step}
 * sharing its same structure but modifying the way records are handled
 */
class restore_activity_logs_structure_step extends restore_course_logs_structure_step {

    protected function process_log($data) {
        global $DB;

        $data = (object)($data);

        $data->time = $this->apply_date_offset($data->time);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->course = $this->get_courseid();
        $data->cmid = $this->task->get_moduleid();

        // For any reason user wasn't remapped ok, stop processing this
        if (empty($data->userid)) {
            return;
        }

        // Everything ready, let's delegate to the restore_logs_processor

        // Set some fixed values that will save tons of DB requests
        $values = array(
            'course' => $this->get_courseid(),
            'course_module' => $this->task->get_moduleid(),
            $this->task->get_modulename() => $this->task->get_activityid());
        // Get instance and process log record
        $data = restore_logs_processor::get_instance($this->task, $values)->process_log_record($data);

        // If we have data, insert it, else something went wrong in the restore_logs_processor
        if ($data) {
            $DB->insert_record('log', $data);
        }
    }
}

/**
 * This structure step restores the grade items associated with one activity
 * All the grade items are made child of the "course" grade item but the original
 * categoryid is saved as parentitemid in the backup_ids table, so, when restoring
 * the complete gradebook (categories and calculations), that information is
 * available there
 */
class restore_activity_grades_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('grade_item', '/activity_gradebook/grade_items/grade_item');
        $paths[] = new restore_path_element('grade_letter', '/activity_gradebook/grade_letters/grade_letter');
        if ($userinfo) {
            $paths[] = new restore_path_element('grade_grade',
                           '/activity_gradebook/grade_items/grade_item/grade_grades/grade_grade');
        }
        return $paths;
    }

    protected function process_grade_item($data) {
        global $DB;

        $data = (object)($data);
        $oldid       = $data->id;        // We'll need these later
        $oldparentid = $data->categoryid;
        $courseid = $this->get_courseid();

        // make sure top course category exists, all grade items will be associated
        // to it. Later, if restoring the whole gradebook, categories will be introduced
        $coursecat = grade_category::fetch_course_category($courseid);
        $coursecatid = $coursecat->id; // Get the categoryid to be used

        $idnumber = null;
        if (!empty($data->idnumber)) {
            // Don't get any idnumber from course module. Keep them as they are in grade_item->idnumber
            // Reason: it's not clear what happens with outcomes->idnumber or activities with multiple items (workshop)
            // so the best is to keep the ones already in the gradebook
            // Potential problem: duplicates if same items are restored more than once. :-(
            // This needs to be fixed in some way (outcomes & activities with multiple items)
            // $data->idnumber     = get_coursemodule_from_instance($data->itemmodule, $data->iteminstance)->idnumber;
            // In any case, verify always for uniqueness
            $sql = "SELECT cm.id
                      FROM {course_modules} cm
                     WHERE cm.course = :courseid AND
                           cm.idnumber = :idnumber AND
                           cm.id <> :cmid";
            $params = array(
                'courseid' => $courseid,
                'idnumber' => $data->idnumber,
                'cmid' => $this->task->get_moduleid()
            );
            if (!$DB->record_exists_sql($sql, $params) && !$DB->record_exists('grade_items', array('courseid' => $courseid, 'idnumber' => $data->idnumber))) {
                $idnumber = $data->idnumber;
            }
        }

        unset($data->id);
        $data->categoryid   = $coursecatid;
        $data->courseid     = $this->get_courseid();
        $data->iteminstance = $this->task->get_activityid();
        $data->idnumber     = $idnumber;
        $data->scaleid      = $this->get_mappingid('scale', $data->scaleid);
        $data->outcomeid    = $this->get_mappingid('outcome', $data->outcomeid);
        $data->timecreated  = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $gradeitem = new grade_item($data, false);
        $gradeitem->insert('restore');

        //sortorder is automatically assigned when inserting. Re-instate the previous sortorder
        $gradeitem->sortorder = $data->sortorder;
        $gradeitem->update('restore');

        // Set mapping, saving the original category id into parentitemid
        // gradebook restore (final task) will need it to reorganise items
        $this->set_mapping('grade_item', $oldid, $gradeitem->id, false, null, $oldparentid);
    }

    protected function process_grade_grade($data) {
        $data = (object)($data);

        unset($data->id);
        $data->itemid = $this->get_new_parentid('grade_item');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->usermodified = $this->get_mappingid('user', $data->usermodified);
        $data->rawscaleid = $this->get_mappingid('scale', $data->rawscaleid);
        // TODO: Ask, all the rest of locktime/exported... work with time... to be rolled?
        $data->overridden = $this->apply_date_offset($data->overridden);

        $grade = new grade_grade($data, false);
        $grade->insert('restore');
        // no need to save any grade_grade mapping
    }

    /**
     * process activity grade_letters. Note that, while these are possible,
     * because grade_letters are contextid based, in proctice, only course
     * context letters can be defined. So we keep here this method knowing
     * it won't be executed ever. gradebook restore will restore course letters.
     */
    protected function process_grade_letter($data) {
        global $DB;

        $data = (object)$data;

        $data->contextid = $this->task->get_contextid();
        $newitemid = $DB->insert_record('grade_letters', $data);
        // no need to save any grade_letter mapping
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
        global $DB, $CFG;

        $data = (object)$data; // Handy
        $oldcontextid = $data->contextid;
        $oldid        = $data->id;
        $positions = isset($data->block_positions['block_position']) ? $data->block_positions['block_position'] : array();

        // Look for the parent contextid
        if (!$data->parentcontextid = $this->get_mappingid('context', $data->parentcontextid)) {
            throw new restore_step_exception('restore_block_missing_parent_ctx', $data->parentcontextid);
        }

        // TODO: it would be nice to use standard plugin supports instead of this instance_allow_multiple()
        // If there is already one block of that type in the parent context
        // and the block is not multiple, stop processing
        // Use blockslib loader / method executor
        if (!block_method_result($data->blockname, 'instance_allow_multiple')) {
            if ($DB->record_exists_sql("SELECT bi.id
                                          FROM {block_instances} bi
                                          JOIN {block} b ON b.name = bi.blockname
                                         WHERE bi.parentcontextid = ?
                                           AND bi.blockname = ?", array($data->parentcontextid, $data->blockname))) {
                return false;
            }
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

        $module = new restore_path_element('module', '/module');
        $paths[] = $module;
        if ($CFG->enableavailability) {
            $paths[] = new restore_path_element('availability', '/module/availability_info/availability');
        }

        // Apply for 'format' plugins optional paths at module level
        $this->add_plugin_structure('format', $module);

        // Apply for 'plagiarism' plugins optional paths at module level
        $this->add_plugin_structure('plagiarism', $module);

        return $paths;
    }

    protected function process_module($data) {
        global $CFG, $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $this->task->set_old_moduleversion($data->version);

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
        if (empty($CFG->enablecompletion)) { // completion
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
        $data = (object)$data;
        // Simply going to store the whole availability record now, we'll process
        // all them later in the final task (once all actvivities have been restored)
        // Let's call the low level one to be able to store the whole object
        $data->coursemoduleid = $this->task->get_moduleid(); // Let add the availability cmid
        restore_dbops::set_backup_ids_record($this->get_restoreid(), 'module_availability', $data->id, 0, null, $data);
    }
}

/**
 * Structure step that will process the user activity completion
 * information if all these conditions are met:
 *  - Target site has completion enabled ($CFG->enablecompletion)
 *  - Activity includes completion info (file_exists)
 */
class restore_userscompletion_structure_step extends restore_structure_step {
    /**
     * To conditionally decide if this step must be executed
     * Note the "settings" conditions are evaluated in the
     * corresponding task. Here we check for other conditions
     * not being restore settings (files, site settings...)
     */
     protected function execute_condition() {
         global $CFG;

         // Completion disabled in this site, don't execute
         if (empty($CFG->enablecompletion)) {
             return false;
         }

         // No user completion info found, don't execute
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
         if (!file_exists($fullpath)) {
             return false;
         }

         // Arrived here, execute the step
         return true;
     }

     protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('completion', '/completions/completion');

        return $paths;
    }

    protected function process_completion($data) {
        global $DB;

        $data = (object)$data;

        $data->coursemoduleid = $this->task->get_moduleid();
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Find the existing record
        $existing = $DB->get_record('course_modules_completion', array(
                'coursemoduleid' => $data->coursemoduleid,
                'userid' => $data->userid), 'id, timemodified');
        // Check we didn't already insert one for this cmid and userid
        // (there aren't supposed to be duplicates in that field, but
        // it was possible until MDL-28021 was fixed).
        if ($existing) {
            // Update it to these new values, but only if the time is newer
            if ($existing->timemodified < $data->timemodified) {
                $data->id = $existing->id;
                $DB->update_record('course_modules_completion', $data);
            }
        } else {
            // Normal entry where it doesn't exist already
            $DB->insert_record('course_modules_completion', $data);
        }
    }
}

/**
 * Abstract structure step, parent of all the activity structure steps. Used to suuport
 * the main <activity ...> tag and process it. Also provides subplugin support for
 * activities.
 */
abstract class restore_activity_structure_step extends restore_structure_step {

    protected function add_subplugin_structure($subplugintype, $element) {

        global $CFG;

        // Check the requested subplugintype is a valid one
        $subpluginsfile = $CFG->dirroot . '/mod/' . $this->task->get_modulename() . '/db/subplugins.php';
        if (!file_exists($subpluginsfile)) {
             throw new restore_step_exception('activity_missing_subplugins_php_file', $this->task->get_modulename());
        }
        include($subpluginsfile);
        if (!array_key_exists($subplugintype, $subplugins)) {
             throw new restore_step_exception('incorrect_subplugin_type', $subplugintype);
        }
        // Get all the restore path elements, looking across all the subplugin dirs
        $subpluginsdirs = get_plugin_list($subplugintype);
        foreach ($subpluginsdirs as $name => $subpluginsdir) {
            $classname = 'restore_' . $subplugintype . '_' . $name . '_subplugin';
            $restorefile = $subpluginsdir . '/backup/moodle2/' . $classname . '.class.php';
            if (file_exists($restorefile)) {
                require_once($restorefile);
                $restoresubplugin = new $classname($subplugintype, $name, $this);
                // Add subplugin paths to the step
                $this->prepare_pathelements($restoresubplugin->define_subplugin_structure($element));
            }
        }
    }

    /**
     * As far as activity restore steps are implementing restore_subplugin stuff, they need to
     * have the parent task available for wrapping purposes (get course/context....)
     * @return restore_task
     */
    public function get_task() {
        return $this->task;
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
     * This must be invoked immediately after creating the "module" activity record (forum, choice...)
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

/**
 * Structure step in charge of creating/mapping all the qcats and qs
 * by parsing the questions.xml file and checking it against the
 * results calculated by {@link restore_process_categories_and_questions}
 * and stored in backup_ids_temp
 */
class restore_create_categories_and_questions extends restore_structure_step {

    protected function define_structure() {

        $category = new restore_path_element('question_category', '/question_categories/question_category');
        $question = new restore_path_element('question', '/question_categories/question_category/questions/question');
        $hint = new restore_path_element('question_hint',
                '/question_categories/question_category/questions/question/question_hints/question_hint');

        // Apply for 'qtype' plugins optional paths at question level
        $this->add_plugin_structure('qtype', $question);

        return array($category, $question, $hint);
    }

    protected function process_question_category($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Check we have one mapping for this category
        if (!$mapping = $this->get_mapping('question_category', $oldid)) {
            return self::SKIP_ALL_CHILDREN; // No mapping = this category doesn't need to be created/mapped
        }

        // Check we have to create the category (newitemid = 0)
        if ($mapping->newitemid) {
            return; // newitemid != 0, this category is going to be mapped. Nothing to do
        }

        // Arrived here, newitemid = 0, we need to create the category
        // we'll do it at parentitemid context, but for CONTEXT_MODULE
        // categories, that will be created at CONTEXT_COURSE and moved
        // to module context later when the activity is created
        if ($mapping->info->contextlevel == CONTEXT_MODULE) {
            $mapping->parentitemid = $this->get_mappingid('context', $this->task->get_old_contextid());
        }
        $data->contextid = $mapping->parentitemid;

        // Let's create the question_category and save mapping
        $newitemid = $DB->insert_record('question_categories', $data);
        $this->set_mapping('question_category', $oldid, $newitemid);
        // Also annotate them as question_category_created, we need
        // that later when remapping parents
        $this->set_mapping('question_category_created', $oldid, $newitemid, false, null, $data->contextid);
    }

    protected function process_question($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Check we have one mapping for this question
        if (!$questionmapping = $this->get_mapping('question', $oldid)) {
            return; // No mapping = this question doesn't need to be created/mapped
        }

        // Get the mapped category (cannot use get_new_parentid() because not
        // all the categories have been created, so it is not always available
        // Instead we get the mapping for the question->parentitemid because
        // we have loaded qcatids there for all parsed questions
        $data->category = $this->get_mappingid('question_category', $questionmapping->parentitemid);

        // In the past, there were some very sloppy values of penalty. Fix them.
        if ($data->penalty >= 0.33 && $data->penalty <= 0.34) {
            $data->penalty = 0.3333333;
        }
        if ($data->penalty >= 0.66 && $data->penalty <= 0.67) {
            $data->penalty = 0.6666667;
        }
        if ($data->penalty >= 1) {
            $data->penalty = 1;
        }

        $data->timecreated  = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $userid = $this->get_mappingid('user', $data->createdby);
        $data->createdby = $userid ? $userid : $this->task->get_userid();

        $userid = $this->get_mappingid('user', $data->modifiedby);
        $data->modifiedby = $userid ? $userid : $this->task->get_userid();

        // With newitemid = 0, let's create the question
        if (!$questionmapping->newitemid) {
            $newitemid = $DB->insert_record('question', $data);
            $this->set_mapping('question', $oldid, $newitemid);
            // Also annotate them as question_created, we need
            // that later when remapping parents (keeping the old categoryid as parentid)
            $this->set_mapping('question_created', $oldid, $newitemid, false, null, $questionmapping->parentitemid);
        } else {
            // By performing this set_mapping() we make get_old/new_parentid() to work for all the
            // children elements of the 'question' one (so qtype plugins will know the question they belong to)
            $this->set_mapping('question', $oldid, $questionmapping->newitemid);
        }

        // Note, we don't restore any question files yet
        // as far as the CONTEXT_MODULE categories still
        // haven't their contexts to be restored to
        // The {@link restore_create_question_files}, executed in the final step
        // step will be in charge of restoring all the question files
    }

        protected function process_question_hint($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its question_answers too
        if ($questioncreated) {
            // Adjust some columns
            $data->questionid = $newquestionid;
            // Insert record
            $newitemid = $DB->insert_record('question_hints', $data);

        // The question existed, we need to map the existing question_hints
        } else {
            // Look in question_hints by hint text matching
            $sql = 'SELECT id
                      FROM {question_hints}
                     WHERE questionid = ?
                       AND ' . $DB->sql_compare_text('hint', 255) . ' = ' . $DB->sql_compare_text('?', 255);
            $params = array($newquestionid, $data->hint);
            $newitemid = $DB->get_field_sql($sql, $params);
            // If we haven't found the newitemid, something has gone really wrong, question in DB
            // is missing hints, exception
            if (!$newitemid) {
                $info = new stdClass();
                $info->filequestionid = $oldquestionid;
                $info->dbquestionid   = $newquestionid;
                $info->hint           = $data->hint;
                throw new restore_step_exception('error_question_hint_missing_in_db', $info);
            }
        }
        // Create mapping (I'm not sure if this is really needed?)
        $this->set_mapping('question_hint', $oldid, $newitemid);
    }

    protected function after_execute() {
        global $DB;

        // First of all, recode all the created question_categories->parent fields
        $qcats = $DB->get_records('backup_ids_temp', array(
                     'backupid' => $this->get_restoreid(),
                     'itemname' => 'question_category_created'));
        foreach ($qcats as $qcat) {
            $newparent = 0;
            $dbcat = $DB->get_record('question_categories', array('id' => $qcat->newitemid));
            // Get new parent (mapped or created, so we look in quesiton_category mappings)
            if ($newparent = $DB->get_field('backup_ids_temp', 'newitemid', array(
                                 'backupid' => $this->get_restoreid(),
                                 'itemname' => 'question_category',
                                 'itemid'   => $dbcat->parent))) {
                // contextids must match always, as far as we always include complete qbanks, just check it
                $newparentctxid = $DB->get_field('question_categories', 'contextid', array('id' => $newparent));
                if ($dbcat->contextid == $newparentctxid) {
                    $DB->set_field('question_categories', 'parent', $newparent, array('id' => $dbcat->id));
                } else {
                    $newparent = 0; // No ctx match for both cats, no parent relationship
                }
            }
            // Here with $newparent empty, problem with contexts or remapping, set it to top cat
            if (!$newparent) {
                $DB->set_field('question_categories', 'parent', 0, array('id' => $dbcat->id));
            }
        }

        // Now, recode all the created question->parent fields
        $qs = $DB->get_records('backup_ids_temp', array(
                  'backupid' => $this->get_restoreid(),
                  'itemname' => 'question_created'));
        foreach ($qs as $q) {
            $newparent = 0;
            $dbq = $DB->get_record('question', array('id' => $q->newitemid));
            // Get new parent (mapped or created, so we look in question mappings)
            if ($newparent = $DB->get_field('backup_ids_temp', 'newitemid', array(
                                 'backupid' => $this->get_restoreid(),
                                 'itemname' => 'question',
                                 'itemid'   => $dbq->parent))) {
                $DB->set_field('question', 'parent', $newparent, array('id' => $dbq->id));
            }
        }

        // Note, we don't restore any question files yet
        // as far as the CONTEXT_MODULE categories still
        // haven't their contexts to be restored to
        // The {@link restore_create_question_files}, executed in the final step
        // step will be in charge of restoring all the question files
    }
}

/**
 * Execution step that will move all the CONTEXT_MODULE question categories
 * created at early stages of restore in course context (because modules weren't
 * created yet) to their target module (matching by old-new-contextid mapping)
 */
class restore_move_module_questions_categories extends restore_execution_step {

    protected function define_execution() {
        global $DB;

        $contexts = restore_dbops::restore_get_question_banks($this->get_restoreid(), CONTEXT_MODULE);
        foreach ($contexts as $contextid => $contextlevel) {
            // Only if context mapping exists (i.e. the module has been restored)
            if ($newcontext = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'context', $contextid)) {
                // Update all the qcats having their parentitemid set to the original contextid
                $modulecats = $DB->get_records_sql("SELECT itemid, newitemid
                                                      FROM {backup_ids_temp}
                                                     WHERE backupid = ?
                                                       AND itemname = 'question_category'
                                                       AND parentitemid = ?", array($this->get_restoreid(), $contextid));
                foreach ($modulecats as $modulecat) {
                    $DB->set_field('question_categories', 'contextid', $newcontext->newitemid, array('id' => $modulecat->newitemid));
                    // And set new contextid also in question_category mapping (will be
                    // used by {@link restore_create_question_files} later
                    restore_dbops::set_backup_ids_record($this->get_restoreid(), 'question_category', $modulecat->itemid, $modulecat->newitemid, $newcontext->newitemid);
                }
            }
        }
    }
}

/**
 * Execution step that will create all the question/answers/qtype-specific files for the restored
 * questions. It must be executed after {@link restore_move_module_questions_categories}
 * because only then each question is in its final category and only then the
 * context can be determined
 *
 * TODO: Improve this. Instead of looping over each question, it can be reduced to
 *       be done by contexts (this will save a huge ammount of queries)
 */
class restore_create_question_files extends restore_execution_step {

    protected function define_execution() {
        global $DB;

        // Let's process only created questions
        $questionsrs = $DB->get_recordset_sql("SELECT bi.itemid, bi.newitemid, bi.parentitemid, q.qtype
                                               FROM {backup_ids_temp} bi
                                               JOIN {question} q ON q.id = bi.newitemid
                                              WHERE bi.backupid = ?
                                                AND bi.itemname = 'question_created'", array($this->get_restoreid()));
        foreach ($questionsrs as $question) {
            // Get question_category mapping, it contains the target context for the question
            if (!$qcatmapping = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'question_category', $question->parentitemid)) {
                // Something went really wrong, cannot find the question_category for the question
                debugging('Error fetching target context for question', DEBUG_DEVELOPER);
                continue;
            }
            // Calculate source and target contexts
            $oldctxid = $qcatmapping->info->contextid;
            $newctxid = $qcatmapping->parentitemid;

            // Add common question files (question and question_answer ones)
            restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'questiontext',
                                              $oldctxid, $this->task->get_userid(), 'question_created', $question->itemid, $newctxid, true);
            restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'generalfeedback',
                                              $oldctxid, $this->task->get_userid(), 'question_created', $question->itemid, $newctxid, true);
            restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'answer',
                                              $oldctxid, $this->task->get_userid(), 'question_answer', null, $newctxid, true);
            restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'answerfeedback',
                                              $oldctxid, $this->task->get_userid(), 'question_answer', null, $newctxid, true);
            restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'hint',
                                              $oldctxid, $this->task->get_userid(), 'question_hint', null, $newctxid, true);
            restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'correctfeedback',
                                              $oldctxid, $this->task->get_userid(), 'question_created', $question->itemid, $newctxid, true);
            restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'partiallycorrectfeedback',
                                              $oldctxid, $this->task->get_userid(), 'question_created', $question->itemid, $newctxid, true);
            restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'incorrectfeedback',
                                              $oldctxid, $this->task->get_userid(), 'question_created', $question->itemid, $newctxid, true);
            // Add qtype dependent files
            $components = backup_qtype_plugin::get_components_and_fileareas($question->qtype);
            foreach ($components as $component => $fileareas) {
                foreach ($fileareas as $filearea => $mapping) {
                    // Use itemid only if mapping is question_created
                    $itemid = ($mapping == 'question_created') ? $question->itemid : null;
                    restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), $component, $filearea,
                                                      $oldctxid, $this->task->get_userid(), $mapping, $itemid, $newctxid, true);
                }
            }
        }
        $questionsrs->close();
    }
}

/**
 * Abstract structure step, to be used by all the activities using core questions stuff
 * (like the quiz module), to support qtype plugins, states and sessions
 */
abstract class restore_questions_activity_structure_step extends restore_activity_structure_step {
    /** @var array question_attempt->id to qtype. */
    protected $qtypes = array();
    /** @var array question_attempt->id to questionid. */
    protected $newquestionids = array();

    /**
     * Attach below $element (usually attempts) the needed restore_path_elements
     * to restore question_usages and all they contain.
     */
    protected function add_question_usages($element, &$paths) {
        // Check $element is restore_path_element
        if (! $element instanceof restore_path_element) {
            throw new restore_step_exception('element_must_be_restore_path_element', $element);
        }
        // Check $paths is one array
        if (!is_array($paths)) {
            throw new restore_step_exception('paths_must_be_array', $paths);
        }
        $paths[] = new restore_path_element('question_usage',
                $element->get_path() . '/question_usage');
        $paths[] = new restore_path_element('question_attempt',
                $element->get_path() . '/question_usage/question_attempts/question_attempt');
        $paths[] = new restore_path_element('question_attempt_step',
                $element->get_path() . '/question_usage/question_attempts/question_attempt/steps/step',
                true);
        $paths[] = new restore_path_element('question_attempt_step_data',
                $element->get_path() . '/question_usage/question_attempts/question_attempt/steps/step/response/variable');
    }

    /**
     * Process question_usages
     */
    protected function process_question_usage($data) {
        global $DB;

        // Clear our caches.
        $this->qtypes = array();
        $this->newquestionids = array();

        $data = (object)$data;
        $oldid = $data->id;

        $oldcontextid = $this->get_task()->get_old_contextid();
        $data->contextid  = $this->get_mappingid('context', $this->task->get_old_contextid());

        // Everything ready, insert (no mapping needed)
        $newitemid = $DB->insert_record('question_usages', $data);

        $this->inform_new_usage_id($newitemid);

        $this->set_mapping('question_usage', $oldid, $newitemid, false);
    }

    /**
     * When process_question_usage creates the new usage, it calls this method
     * to let the activity link to the new usage. For example, the quiz uses
     * this method to set quiz_attempts.uniqueid to the new usage id.
     * @param integer $newusageid
     */
    abstract protected function inform_new_usage_id($newusageid);

    /**
     * Process question_attempts
     */
    protected function process_question_attempt($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $question = $this->get_mapping('question', $data->questionid);

        $data->questionusageid = $this->get_new_parentid('question_usage');
        $data->questionid      = $question->newitemid;
        $data->timemodified    = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('question_attempts', $data);

        $this->set_mapping('question_attempt', $oldid, $newitemid);
        $this->qtypes[$newitemid] = $question->info->qtype;
        $this->newquestionids[$newitemid] = $data->questionid;
    }

    /**
     * Process question_attempt_steps
     */
    protected function process_question_attempt_step($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Pull out the response data.
        $response = array();
        if (!empty($data->response['variable'])) {
            foreach ($data->response['variable'] as $variable) {
                $response[$variable['name']] = $variable['value'];
            }
        }
        unset($data->response);

        $data->questionattemptid = $this->get_new_parentid('question_attempt');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->userid      = $this->get_mappingid('user', $data->userid);

        // Everything ready, insert and create mapping (needed by question_sessions)
        $newitemid = $DB->insert_record('question_attempt_steps', $data);
        $this->set_mapping('question_attempt_step', $oldid, $newitemid, true);

        // Now process the response data.
        $response = $this->questions_recode_response_data(
                $this->qtypes[$data->questionattemptid],
                $this->newquestionids[$data->questionattemptid],
                $data->sequencenumber, $response);
        foreach ($response as $name => $value) {
            $row = new stdClass();
            $row->attemptstepid = $newitemid;
            $row->name = $name;
            $row->value = $value;
            $DB->insert_record('question_attempt_step_data', $row, false);
        }
    }

    /**
     * Recode the respones data for a particular step of an attempt at at particular question.
     * @param string $qtype the question type.
     * @param int $newquestionid the question id.
     * @param int $sequencenumber the sequence number.
     * @param array $response the response data to recode.
     */
    public function questions_recode_response_data(
            $qtype, $newquestionid, $sequencenumber, array $response) {
        $qtyperestorer = $this->get_qtype_restorer($qtype);
        if ($qtyperestorer) {
            $response = $qtyperestorer->recode_response($newquestionid, $sequencenumber, $response);
        }
        return $response;
    }

    /**
     * Given a list of question->ids, separated by commas, returns the
     * recoded list, with all the restore question mappings applied.
     * Note: Used by quiz->questions and quiz_attempts->layout
     * Note: 0 = page break (unconverted)
     */
    protected function questions_recode_layout($layout) {
        // Extracts question id from sequence
        if ($questionids = explode(',', $layout)) {
            foreach ($questionids as $id => $questionid) {
                if ($questionid) { // If it is zero then this is a pagebreak, don't translate
                    $newquestionid = $this->get_mappingid('question', $questionid);
                    $questionids[$id] = $newquestionid;
                }
            }
        }
        return implode(',', $questionids);
    }

    /**
     * Get the restore_qtype_plugin subclass for a specific question type.
     * @param string $qtype e.g. multichoice.
     * @return restore_qtype_plugin instance.
     */
    protected function get_qtype_restorer($qtype) {
        // Build one static cache to store {@link restore_qtype_plugin}
        // while we are needing them, just to save zillions of instantiations
        // or using static stuff that will break our nice API
        static $qtypeplugins = array();

        if (!isset($qtypeplugins[$qtype])) {
            $classname = 'restore_qtype_' . $qtype . '_plugin';
            if (class_exists($classname)) {
                $qtypeplugins[$qtype] = new $classname('qtype', $qtype, $this);
            } else {
                $qtypeplugins[$qtype] = null;
            }
        }
        return $qtypeplugins[$qtype];
    }

    protected function after_execute() {
        parent::after_execute();

        // Restore any files belonging to responses.
        foreach (question_engine::get_all_response_file_areas() as $filearea) {
            $this->add_related_files('question', $filearea, 'question_attempt_step');
        }
    }

    /**
     * Attach below $element (usually attempts) the needed restore_path_elements
     * to restore question attempt data from Moodle 2.0.
     *
     * When using this method, the parent element ($element) must be defined with
     * $grouped = true. Then, in that elements process method, you must call
     * {@link process_legacy_attempt_data()} with the groupded data. See, for
     * example, the usage of this method in {@link restore_quiz_activity_structure_step}.
     * @param restore_path_element $element the parent element. (E.g. a quiz attempt.)
     * @param array $paths the paths array that is being built to describe the
     *      structure.
     */
    protected function add_legacy_question_attempt_data($element, &$paths) {
        global $CFG;
        require_once($CFG->dirroot . '/question/engine/upgrade/upgradelib.php');

        // Check $element is restore_path_element
        if (!($element instanceof restore_path_element)) {
            throw new restore_step_exception('element_must_be_restore_path_element', $element);
        }
        // Check $paths is one array
        if (!is_array($paths)) {
            throw new restore_step_exception('paths_must_be_array', $paths);
        }

        $paths[] = new restore_path_element('question_state',
                $element->get_path() . '/states/state');
        $paths[] = new restore_path_element('question_session',
                $element->get_path() . '/sessions/session');
    }

    protected function get_attempt_upgrader() {
        if (empty($this->attemptupgrader)) {
            $this->attemptupgrader = new question_engine_attempt_upgrader();
            $this->attemptupgrader->prepare_to_restore();
        }
        return $this->attemptupgrader;
    }

    /**
     * Process the attempt data defined by {@link add_legacy_question_attempt_data()}.
     * @param object $data contains all the grouped attempt data ot process.
     * @param pbject $quiz data about the activity the attempts belong to. Required
     * fields are (basically this only works for the quiz module):
     *      oldquestions => list of question ids in this activity - using old ids.
     *      preferredbehaviour => the behaviour to use for questionattempts.
     */
    protected function process_legacy_quiz_attempt_data($data, $quiz) {
        global $DB;
        $upgrader = $this->get_attempt_upgrader();

        $data = (object)$data;

        $layout = explode(',', $data->layout);
        $newlayout = $layout;

        // Convert each old question_session into a question_attempt.
        $qas = array();
        foreach (explode(',', $quiz->oldquestions) as $questionid) {
            if ($questionid == 0) {
                continue;
            }

            $newquestionid = $this->get_mappingid('question', $questionid);
            if (!$newquestionid) {
                throw new restore_step_exception('questionattemptreferstomissingquestion',
                        $questionid, $questionid);
            }

            $question = $upgrader->load_question($newquestionid, $quiz->id);

            foreach ($layout as $key => $qid) {
                if ($qid == $questionid) {
                    $newlayout[$key] = $newquestionid;
                }
            }

            list($qsession, $qstates) = $this->find_question_session_and_states(
                    $data, $questionid);

            if (empty($qsession) || empty($qstates)) {
                throw new restore_step_exception('questionattemptdatamissing',
                        $questionid, $questionid);
            }

            list($qsession, $qstates) = $this->recode_legacy_response_data(
                    $question, $qsession, $qstates);

            $data->layout = implode(',', $newlayout);
            $qas[$newquestionid] = $upgrader->convert_question_attempt(
                    $quiz, $data, $question, $qsession, $qstates);
        }

        // Now create a new question_usage.
        $usage = new stdClass();
        $usage->component = 'mod_quiz';
        $usage->contextid = $this->get_mappingid('context', $this->task->get_old_contextid());
        $usage->preferredbehaviour = $quiz->preferredbehaviour;
        $usage->id = $DB->insert_record('question_usages', $usage);

        $this->inform_new_usage_id($usage->id);

        $data->uniqueid = $usage->id;
        $upgrader->save_usage($quiz->preferredbehaviour, $data, $qas, $quiz->questions);
    }

    protected function find_question_session_and_states($data, $questionid) {
        $qsession = null;
        foreach ($data->sessions['session'] as $session) {
            if ($session['questionid'] == $questionid) {
                $qsession = (object) $session;
                break;
            }
        }

        $qstates = array();
        foreach ($data->states['state'] as $state) {
            if ($state['question'] == $questionid) {
                // It would be natural to use $state['seq_number'] as the array-key
                // here, but it seems that buggy behaviour in 2.0 and early can
                // mean that that is not unique, so we use id, which is guaranteed
                // to be unique.
                $qstates[$state['id']] = (object) $state;
            }
        }
        ksort($qstates);
        $qstates = array_values($qstates);

        return array($qsession, $qstates);
    }

    /**
     * Recode any ids in the response data
     * @param object $question the question data
     * @param object $qsession the question sessions.
     * @param array $qstates the question states.
     */
    protected function recode_legacy_response_data($question, $qsession, $qstates) {
        $qsession->questionid = $question->id;

        foreach ($qstates as &$state) {
            $state->question = $question->id;
            $state->answer = $this->restore_recode_legacy_answer($state, $question->qtype);
        }

        return array($qsession, $qstates);
    }

    /**
     * Recode the legacy answer field.
     * @param object $state the state to recode the answer of.
     * @param string $qtype the question type.
     */
    public function restore_recode_legacy_answer($state, $qtype) {
        $restorer = $this->get_qtype_restorer($qtype);
        if ($restorer) {
            return $restorer->recode_legacy_state_answer($state);
        } else {
            return $state->answer;
        }
    }
}
