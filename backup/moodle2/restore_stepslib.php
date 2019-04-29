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
 * Defines various restore steps that will be used by common tasks in restore
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
        $newitemid = context_course::instance($this->get_courseid())->id;
        restore_dbops::set_backup_ids_record($this->get_restoreid(), 'context', $itemid, $newitemid);
        // Create the old-system-ctxid to new-system-ctxid mapping, we need that available since the beginning
        $itemid = $this->task->get_old_system_contextid();
        $newitemid = context_system::instance()->id;
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
        $progress = $this->task->get_progress();
        $progress->start_progress('Deleting backup dir');
        backup_helper::delete_old_backup_dirs(strtotime('-1 week'), $progress);      // Delete > 1 week old temp dirs.
        if (empty($CFG->keeptempdirectoriesonbackup)) { // Conditionally
            backup_helper::delete_backup_dir($this->task->get_tempdir(), $progress); // Empty restore dir
        }
        $progress->end_progress();
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

        if ($this->get_courseid() == SITEID) {
            return false;
        }

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

        // Identify the backup we're dealing with.
        $backuprelease = floatval($this->get_task()->get_info()->backup_release); // The major version: 2.9, 3.0, ...
        $backupbuild = 0;
        preg_match('/(\d{8})/', $this->get_task()->get_info()->moodle_release, $matches);
        if (!empty($matches[1])) {
            $backupbuild = (int) $matches[1]; // The date of Moodle build at the time of the backup.
        }

        // On older versions the freeze value has to be converted.
        // We do this from here as it is happening right before the file is read.
        // This only targets the backup files that can contain the legacy freeze.
        if ($backupbuild > 20150618 && ($backuprelease < 3.0 || $backupbuild < 20160527)) {
            $this->rewrite_step_backup_file_for_legacy_freeze($fullpath);
        }

        // Arrived here, execute the step
        return true;
     }

    protected function define_structure() {
        $paths = array();
        $userinfo = $this->task->get_setting_value('users');

        $paths[] = new restore_path_element('attributes', '/gradebook/attributes');
        $paths[] = new restore_path_element('grade_category', '/gradebook/grade_categories/grade_category');
        $paths[] = new restore_path_element('grade_item', '/gradebook/grade_items/grade_item');
        if ($userinfo) {
            $paths[] = new restore_path_element('grade_grade', '/gradebook/grade_items/grade_item/grade_grades/grade_grade');
        }
        $paths[] = new restore_path_element('grade_letter', '/gradebook/grade_letters/grade_letter');
        $paths[] = new restore_path_element('grade_setting', '/gradebook/grade_settings/grade_setting');

        return $paths;
    }

    protected function process_attributes($data) {
        // For non-merge restore types:
        // Unset 'gradebook_calculations_freeze_' in the course and replace with the one from the backup.
        $target = $this->get_task()->get_target();
        if ($target == backup::TARGET_CURRENT_DELETING || $target == backup::TARGET_EXISTING_DELETING) {
            set_config('gradebook_calculations_freeze_' . $this->get_courseid(), null);
        }
        if (!empty($data['calculations_freeze'])) {
            if ($target == backup::TARGET_NEW_COURSE || $target == backup::TARGET_CURRENT_DELETING ||
                    $target == backup::TARGET_EXISTING_DELETING) {
                set_config('gradebook_calculations_freeze_' . $this->get_courseid(), $data['calculations_freeze']);
            }
        }
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
            // if mapping failed put in course's grade category
            if (NULL == $data->categoryid) {
                $coursecat = grade_category::fetch_course_category($this->get_courseid());
                $data->categoryid = $coursecat->id;
            }
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

        $data->locktime = $this->apply_date_offset($data->locktime);

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
        } else if ($data->itemtype == 'manual') {
            // Manual items aren't assigned to a cm, so don't go duplicating them in the target if one exists.
            $gi = array(
                'itemtype' => $data->itemtype,
                'courseid' => $data->courseid,
                'itemname' => $data->itemname,
                'categoryid' => $data->categoryid,
            );
            $newitemid = $DB->get_field('grade_items', 'id', $gi);
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
        $olduserid = $data->userid;

        $data->itemid = $this->get_new_parentid('grade_item');

        $data->userid = $this->get_mappingid('user', $data->userid, null);
        if (!empty($data->userid)) {
            $data->usermodified = $this->get_mappingid('user', $data->usermodified, null);
            $data->locktime     = $this->apply_date_offset($data->locktime);

            $gradeexists = $DB->record_exists('grade_grades', array('userid' => $data->userid, 'itemid' => $data->itemid));
            if ($gradeexists) {
                $message = "User id '{$data->userid}' already has a grade entry for grade item id '{$data->itemid}'";
                $this->log($message, backup::LOG_DEBUG);
            } else {
                $newitemid = $DB->insert_record('grade_grades', $data);
                $this->set_mapping('grade_grades', $oldid, $newitemid);
            }
        } else {
            $message = "Mapped user id not found for user id '{$olduserid}', grade item id '{$data->itemid}'";
            $this->log($message, backup::LOG_DEBUG);
        }
    }

    protected function process_grade_category($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->course = $this->get_courseid();
        $data->courseid = $data->course;

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

        // Add a warning about a removed setting.
        if (!empty($data->aggregatesubcats)) {
            set_config('show_aggregatesubcats_upgrade_' . $data->courseid, 1);
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

        $data->contextid = context_course::instance($this->get_courseid())->id;

        $gradeletter = (array)$data;
        unset($gradeletter['id']);
        if (!$DB->record_exists('grade_letters', $gradeletter)) {
            $newitemid = $DB->insert_record('grade_letters', $data);
        } else {
            $newitemid = $data->id;
        }

        $this->set_mapping('grade_letter', $oldid, $newitemid);
    }
    protected function process_grade_setting($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->courseid = $this->get_courseid();

        $target = $this->get_task()->get_target();
        if ($data->name == 'minmaxtouse' &&
                ($target == backup::TARGET_CURRENT_ADDING || $target == backup::TARGET_EXISTING_ADDING)) {
            // We never restore minmaxtouse during merge.
            return;
        }

        if (!$DB->record_exists('grade_settings', array('courseid' => $data->courseid, 'name' => $data->name))) {
            $newitemid = $DB->insert_record('grade_settings', $data);
        } else {
            $newitemid = $data->id;
        }

        if (!empty($oldid)) {
            // In rare cases (minmaxtouse), it is possible that there wasn't any ID associated with the setting.
            $this->set_mapping('grade_setting', $oldid, $newitemid);
        }
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

        // Check what to do with the minmaxtouse setting.
        $this->check_minmaxtouse();

        // Freeze gradebook calculations if needed.
        $this->gradebook_calculation_freeze();

        // Ensure the module cache is current when recalculating grades.
        rebuild_course_cache($this->get_courseid(), true);

        // Restore marks items as needing update. Update everything now.
        grade_regrade_final_grades($this->get_courseid());
    }

    /**
     * Freeze gradebook calculation if needed.
     *
     * This is similar to various upgrade scripts that check if the freeze is needed.
     */
    protected function gradebook_calculation_freeze() {
        global $CFG;
        $gradebookcalculationsfreeze = get_config('core', 'gradebook_calculations_freeze_' . $this->get_courseid());
        preg_match('/(\d{8})/', $this->get_task()->get_info()->moodle_release, $matches);
        $backupbuild = (int)$matches[1];
        // The function floatval will return a float even if there is text mixed with the release number.
        $backuprelease = floatval($this->get_task()->get_info()->backup_release);

        // Extra credits need adjustments only for backups made between 2.8 release (20141110) and the fix release (20150619).
        if (!$gradebookcalculationsfreeze && $backupbuild >= 20141110 && $backupbuild < 20150619) {
            require_once($CFG->libdir . '/db/upgradelib.php');
            upgrade_extra_credit_weightoverride($this->get_courseid());
        }
        // Calculated grade items need recalculating for backups made between 2.8 release (20141110) and the fix release (20150627).
        if (!$gradebookcalculationsfreeze && $backupbuild >= 20141110 && $backupbuild < 20150627) {
            require_once($CFG->libdir . '/db/upgradelib.php');
            upgrade_calculated_grade_items($this->get_courseid());
        }
        // Courses from before 3.1 (20160518) may have a letter boundary problem and should be checked for this issue.
        // Backups from before and including 2.9 could have a build number that is greater than 20160518 and should
        // be checked for this problem.
        if (!$gradebookcalculationsfreeze && ($backupbuild < 20160518 || $backuprelease <= 2.9)) {
            require_once($CFG->libdir . '/db/upgradelib.php');
            upgrade_course_letter_boundary($this->get_courseid());
        }

    }

    /**
     * Checks what should happen with the course grade setting minmaxtouse.
     *
     * This is related to the upgrade step at the time the setting was added.
     *
     * @see MDL-48618
     * @return void
     */
    protected function check_minmaxtouse() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/gradelib.php');

        $userinfo = $this->task->get_setting_value('users');
        $settingname = 'minmaxtouse';
        $courseid = $this->get_courseid();
        $minmaxtouse = $DB->get_field('grade_settings', 'value', array('courseid' => $courseid, 'name' => $settingname));
        $version28start = 2014111000.00;
        $version28last = 2014111006.05;
        $version29start = 2015051100.00;
        $version29last = 2015060400.02;

        $target = $this->get_task()->get_target();
        if ($minmaxtouse === false &&
                ($target != backup::TARGET_CURRENT_ADDING && $target != backup::TARGET_EXISTING_ADDING)) {
            // The setting was not found because this setting did not exist at the time the backup was made.
            // And we are not restoring as merge, in which case we leave the course as it was.
            $version = $this->get_task()->get_info()->moodle_version;

            if ($version < $version28start) {
                // We need to set it to use grade_item, but only if the site-wide setting is different. No need to notice them.
                if ($CFG->grade_minmaxtouse != GRADE_MIN_MAX_FROM_GRADE_ITEM) {
                    grade_set_setting($courseid, $settingname, GRADE_MIN_MAX_FROM_GRADE_ITEM);
                }

            } else if (($version >= $version28start && $version < $version28last) ||
                    ($version >= $version29start && $version < $version29last)) {
                // They should be using grade_grade when the course has inconsistencies.

                $sql = "SELECT gi.id
                          FROM {grade_items} gi
                          JOIN {grade_grades} gg
                            ON gg.itemid = gi.id
                         WHERE gi.courseid = ?
                           AND (gi.itemtype != ? AND gi.itemtype != ?)
                           AND (gg.rawgrademax != gi.grademax OR gg.rawgrademin != gi.grademin)";

                // The course can only have inconsistencies when we restore the user info,
                // we do not need to act on existing grades that were not restored as part of this backup.
                if ($userinfo && $DB->record_exists_sql($sql, array($courseid, 'course', 'category'))) {

                    // Display the notice as we do during upgrade.
                    set_config('show_min_max_grades_changed_' . $courseid, 1);

                    if ($CFG->grade_minmaxtouse != GRADE_MIN_MAX_FROM_GRADE_GRADE) {
                        // We need set the setting as their site-wise setting is not GRADE_MIN_MAX_FROM_GRADE_GRADE.
                        // If they are using the site-wide grade_grade setting, we only want to notice them.
                        grade_set_setting($courseid, $settingname, GRADE_MIN_MAX_FROM_GRADE_GRADE);
                    }
                }

            } else {
                // This should never happen because from now on minmaxtouse is always saved in backups.
            }
        }
    }

    /**
     * Rewrite step definition to handle the legacy freeze attribute.
     *
     * In previous backups the calculations_freeze property was stored as an attribute of the
     * top level node <gradebook>. The backup API, however, do not process grandparent nodes.
     * It only processes definitive children, and their parent attributes.
     *
     * We had:
     *
     * <gradebook calculations_freeze="20160511">
     *   <grade_categories>
     *     <grade_category id="10">
     *       <depth>1</depth>
     *       ...
     *     </grade_category>
     *   </grade_categories>
     *   ...
     * </gradebook>
     *
     * And this method will convert it to:
     *
     * <gradebook >
     *   <attributes>
     *     <calculations_freeze>20160511</calculations_freeze>
     *   </attributes>
     *   <grade_categories>
     *     <grade_category id="10">
     *       <depth>1</depth>
     *       ...
     *     </grade_category>
     *   </grade_categories>
     *   ...
     * </gradebook>
     *
     * Note that we cannot just load the XML file in memory as it could potentially be huge.
     * We can also completely ignore if the node <attributes> is already in the backup
     * file as it never existed before.
     *
     * @param string $filepath The absolute path to the XML file.
     * @return void
     */
    protected function rewrite_step_backup_file_for_legacy_freeze($filepath) {
        $foundnode = false;
        $newfile = make_request_directory(true) . DIRECTORY_SEPARATOR . 'file.xml';
        $fr = fopen($filepath, 'r');
        $fw = fopen($newfile, 'w');
        if ($fr && $fw) {
            while (($line = fgets($fr, 4096)) !== false) {
                if (!$foundnode && strpos($line, '<gradebook ') === 0) {
                    $foundnode = true;
                    $matches = array();
                    $pattern = '@calculations_freeze=.([0-9]+).@';
                    if (preg_match($pattern, $line, $matches)) {
                        $freeze = $matches[1];
                        $line = preg_replace($pattern, '', $line);
                        $line .= "  <attributes>\n    <calculations_freeze>$freeze</calculations_freeze>\n  </attributes>\n";
                    }
                }
                fputs($fw, $line);
            }
            if (!feof($fr)) {
                throw new restore_step_exception('Error while attempting to rewrite the gradebook step file.');
            }
            fclose($fr);
            fclose($fw);
            if (!rename($newfile, $filepath)) {
                throw new restore_step_exception('Error while attempting to rename the gradebook step file.');
            }
        } else {
            if ($fr) {
                fclose($fr);
            }
            if ($fw) {
                fclose($fw);
            }
        }
    }

}

/**
 * Step in charge of restoring the grade history of a course.
 *
 * The execution conditions are itendical to {@link restore_gradebook_structure_step} because
 * we do not want to restore the history if the gradebook and its content has not been
 * restored. At least for now.
 */
class restore_grade_history_structure_step extends restore_structure_step {

     protected function execute_condition() {
        global $CFG, $DB;

        if ($this->get_courseid() == SITEID) {
            return false;
        }

        // No gradebook info found, don't execute.
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            return false;
        }

        // Some module present in backup file isn't available to restore in this site, don't execute.
        if ($this->task->is_missing_modules()) {
            return false;
        }

        // Some activity has been excluded to be restored, don't execute.
        if ($this->task->is_excluding_activities()) {
            return false;
        }

        // There should only be one grade category (the 1 associated with the course itself).
        $category = new stdclass();
        $category->courseid  = $this->get_courseid();
        $catcount = $DB->count_records('grade_categories', (array)$category);
        if ($catcount > 1) {
            return false;
        }

        // Arrived here, execute the step.
        return true;
     }

    protected function define_structure() {
        $paths = array();

        // Settings to use.
        $userinfo = $this->get_setting_value('users');
        $history = $this->get_setting_value('grade_histories');

        if ($userinfo && $history) {
            $paths[] = new restore_path_element('grade_grade',
               '/grade_history/grade_grades/grade_grade');
        }

        return $paths;
    }

    protected function process_grade_grade($data) {
        global $DB;

        $data = (object)($data);
        $olduserid = $data->userid;
        unset($data->id);

        $data->userid = $this->get_mappingid('user', $data->userid, null);
        if (!empty($data->userid)) {
            // Do not apply the date offsets as this is history.
            $data->itemid = $this->get_mappingid('grade_item', $data->itemid);
            $data->oldid = $this->get_mappingid('grade_grades', $data->oldid);
            $data->usermodified = $this->get_mappingid('user', $data->usermodified, null);
            $data->rawscaleid = $this->get_mappingid('scale', $data->rawscaleid);
            $DB->insert_record('grade_grades_history', $data);
        } else {
            $message = "Mapped user id not found for user id '{$olduserid}', grade item id '{$data->itemid}'";
            $this->log($message, backup::LOG_DEBUG);
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
                    'section' => $i,
                    'timemodified' => time());
                $DB->insert_record('course_sections', $sectionrec); // missing section created
            }
        }

        // Rebuild cache now that all sections are in place
        rebuild_course_cache($this->get_courseid());
        cache_helper::purge_by_event('changesincourse');
        cache_helper::purge_by_event('changesincoursecat');
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
        $rs = $DB->get_recordset('backup_ids_temp', $params, '', 'itemid, info');
        // Process block positions, creating them or accumulating for final step
        foreach($rs as $posrec) {
            // Get the complete position object out of the info field.
            $position = backup_controller_dbops::decode_backup_temp_info($posrec->info);
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
 * Updates the availability data for course modules and sections.
 *
 * Runs after the restore of all course modules, sections, and grade items has
 * completed. This is necessary in order to update IDs that have changed during
 * restore.
 *
 * @package core_backup
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_update_availability extends restore_execution_step {

    protected function define_execution() {
        global $CFG, $DB;

        // Note: This code runs even if availability is disabled when restoring.
        // That will ensure that if you later turn availability on for the site,
        // there will be no incorrect IDs. (It doesn't take long if the restored
        // data does not contain any availability information.)

        // Get modinfo with all data after resetting cache.
        rebuild_course_cache($this->get_courseid(), true);
        $modinfo = get_fast_modinfo($this->get_courseid());

        // Get the date offset for this restore.
        $dateoffset = $this->apply_date_offset(1) - 1;

        // Update all sections that were restored.
        $params = array('backupid' => $this->get_restoreid(), 'itemname' => 'course_section');
        $rs = $DB->get_recordset('backup_ids_temp', $params, '', 'newitemid');
        $sectionsbyid = null;
        foreach ($rs as $rec) {
            if (is_null($sectionsbyid)) {
                $sectionsbyid = array();
                foreach ($modinfo->get_section_info_all() as $section) {
                    $sectionsbyid[$section->id] = $section;
                }
            }
            if (!array_key_exists($rec->newitemid, $sectionsbyid)) {
                // If the section was not fully restored for some reason
                // (e.g. due to an earlier error), skip it.
                $this->get_logger()->process('Section not fully restored: id ' .
                        $rec->newitemid, backup::LOG_WARNING);
                continue;
            }
            $section = $sectionsbyid[$rec->newitemid];
            if (!is_null($section->availability)) {
                $info = new \core_availability\info_section($section);
                $info->update_after_restore($this->get_restoreid(),
                        $this->get_courseid(), $this->get_logger(), $dateoffset, $this->task);
            }
        }
        $rs->close();

        // Update all modules that were restored.
        $params = array('backupid' => $this->get_restoreid(), 'itemname' => 'course_module');
        $rs = $DB->get_recordset('backup_ids_temp', $params, '', 'newitemid');
        foreach ($rs as $rec) {
            if (!array_key_exists($rec->newitemid, $modinfo->cms)) {
                // If the module was not fully restored for some reason
                // (e.g. due to an earlier error), skip it.
                $this->get_logger()->process('Module not fully restored: id ' .
                        $rec->newitemid, backup::LOG_WARNING);
                continue;
            }
            $cm = $modinfo->get_cm($rec->newitemid);
            if (!is_null($cm->availability)) {
                $info = new \core_availability\info_module($cm);
                $info->update_after_restore($this->get_restoreid(),
                        $this->get_courseid(), $this->get_logger(), $dateoffset, $this->task);
            }
        }
        $rs->close();
    }
}


/**
 * Process legacy module availability records in backup_ids.
 *
 * Matches course modules and grade item id once all them have been already restored.
 * Only if all matchings are satisfied the availability condition will be created.
 * At the same time, it is required for the site to have that functionality enabled.
 *
 * This step is included only to handle legacy backups (2.6 and before). It does not
 * do anything for newer backups.
 *
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class restore_process_course_modules_availability extends restore_execution_step {

    protected function define_execution() {
        global $CFG, $DB;

        // Site hasn't availability enabled
        if (empty($CFG->enableavailability)) {
            return;
        }

        // Do both modules and sections.
        foreach (array('module', 'section') as $table) {
            // Get all the availability objects to process.
            $params = array('backupid' => $this->get_restoreid(), 'itemname' => $table . '_availability');
            $rs = $DB->get_recordset('backup_ids_temp', $params, '', 'itemid, info');
            // Process availabilities, creating them if everything matches ok.
            foreach ($rs as $availrec) {
                $allmatchesok = true;
                // Get the complete legacy availability object.
                $availability = backup_controller_dbops::decode_backup_temp_info($availrec->info);

                // Note: This code used to update IDs, but that is now handled by the
                // current code (after restore) instead of this legacy code.

                // Get showavailability option.
                $thingid = ($table === 'module') ? $availability->coursemoduleid :
                        $availability->coursesectionid;
                $showrec = restore_dbops::get_backup_ids_record($this->get_restoreid(),
                        $table . '_showavailability', $thingid);
                if (!$showrec) {
                    // Should not happen.
                    throw new coding_exception('No matching showavailability record');
                }
                $show = $showrec->info->showavailability;

                // The $availability object is now in the format used in the old
                // system. Interpret this and convert to new system.
                $currentvalue = $DB->get_field('course_' . $table . 's', 'availability',
                        array('id' => $thingid), MUST_EXIST);
                $newvalue = \core_availability\info::add_legacy_availability_condition(
                        $currentvalue, $availability, $show);
                $DB->set_field('course_' . $table . 's', 'availability', $newvalue,
                        array('id' => $thingid));
            }
            $rs->close();
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

        // Get all the included tasks
        $tasks = restore_dbops::get_included_tasks($this->get_restoreid());
        $progress = $this->task->get_progress();
        $progress->start_progress($this->get_name(), count($tasks));
        foreach ($tasks as $task) {
            // Load the inforef.xml file if exists
            $inforefpath = $task->get_taskbasepath() . '/inforef.xml';
            if (file_exists($inforefpath)) {
                // Load each inforef file to temp_ids.
                restore_dbops::load_inforef_to_tempids($this->get_restoreid(), $inforefpath, $progress);
            }
        }
        $progress->end_progress();
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

    /**
     * Process one <file> element from files.xml
     *
     * @param array $data the element data
     */
    public function process_file($data) {

        $data = (object)$data; // handy

        // load it if needed:
        //   - it it is one of the annotated inforef files (course/section/activity/block)
        //   - it is one "user", "group", "grouping", "grade", "question" or "qtype_xxxx" component file (that aren't sent to inforef ever)
        // TODO: qtype_xxx should be replaced by proper backup_qtype_plugin::get_components_and_fileareas() use,
        //       but then we'll need to change it to load plugins itself (because this is executed too early in restore)
        $isfileref   = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'fileref', $data->id);
        $iscomponent = ($data->component == 'user' || $data->component == 'group' || $data->component == 'badges' ||
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
        // Load needed users to temp_ids.
        restore_dbops::load_users_to_tempids($this->get_restoreid(), $file, $this->task->get_progress());
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
        restore_dbops::process_included_users($this->get_restoreid(), $this->task->get_courseid(),
                $this->task->get_userid(), $this->task->is_samesite(), $this->task->get_progress());
    }
}

/**
 * Execution step that will create all the needed users as calculated
 * by @restore_process_included_users (those having newiteind = 0)
 */
class restore_create_included_users extends restore_execution_step {

    protected function define_execution() {

        restore_dbops::create_included_users($this->get_basepath(), $this->get_restoreid(),
                $this->task->get_userid(), $this->task->get_progress());
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

        // Do not include group/groupings information if not requested.
        $groupinfo = $this->get_setting_value('groups');
        if ($groupinfo) {
            $paths[] = new restore_path_element('group', '/groups/group');
            $paths[] = new restore_path_element('grouping', '/groups/groupings/grouping');
            $paths[] = new restore_path_element('grouping_group', '/groups/groupings/grouping/grouping_groups/grouping_group');
        }
        return $paths;
    }

    // Processing functions go here
    public function process_group($data) {
        global $DB;

        $data = (object)$data; // handy
        $data->courseid = $this->get_courseid();

        // Only allow the idnumber to be set if the user has permission and the idnumber is not already in use by
        // another a group in the same course
        $context = context_course::instance($data->courseid);
        if (isset($data->idnumber) and has_capability('moodle/course:changeidnumber', $context, $this->task->get_userid())) {
            if (groups_get_group_by_idnumber($data->courseid, $data->idnumber)) {
                unset($data->idnumber);
            }
        } else {
            unset($data->idnumber);
        }

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
        // Invalidate the course group data cache just in case.
        cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($data->courseid));
    }

    public function process_grouping($data) {
        global $DB;

        $data = (object)$data; // handy
        $data->courseid = $this->get_courseid();

        // Only allow the idnumber to be set if the user has permission and the idnumber is not already in use by
        // another a grouping in the same course
        $context = context_course::instance($data->courseid);
        if (isset($data->idnumber) and has_capability('moodle/course:changeidnumber', $context, $this->task->get_userid())) {
            if (groups_get_grouping_by_idnumber($data->courseid, $data->idnumber)) {
                unset($data->idnumber);
            }
        } else {
            unset($data->idnumber);
        }

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
        // Invalidate the course group data cache just in case.
        cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($data->courseid));
    }

    public function process_grouping_group($data) {
        global $CFG;

        require_once($CFG->dirroot.'/group/lib.php');

        $data = (object)$data;
        groups_assign_grouping($this->get_new_parentid('grouping'), $this->get_mappingid('group', $data->groupid), $data->timeadded);
    }

    protected function after_execute() {
        // Add group related files, matching with "group" mappings
        $this->add_related_files('group', 'icon', 'group');
        $this->add_related_files('group', 'description', 'group');
        // Add grouping related files, matching with "grouping" mappings
        $this->add_related_files('grouping', 'description', 'grouping');
        // Invalidate the course group data.
        cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($this->get_courseid()));
    }

}

/**
 * Structure step that will create all the needed group memberships
 * by loading them from the groups.xml file performing the required matches.
 */
class restore_groups_members_structure_step extends restore_structure_step {

    protected $plugins = null;

    protected function define_structure() {

        $paths = array(); // Add paths here

        if ($this->get_setting_value('groups') && $this->get_setting_value('users')) {
            $paths[] = new restore_path_element('group', '/groups/group');
            $paths[] = new restore_path_element('member', '/groups/group/group_members/group_member');
        }

        return $paths;
    }

    public function process_group($data) {
        $data = (object)$data; // handy

        // HACK ALERT!
        // Not much to do here, this groups mapping should be already done from restore_groups_structure_step.
        // Let's fake internal state to make $this->get_new_parentid('group') work.

        $this->set_mapping('group', $data->id, $this->get_mappingid('group', $data->id));
    }

    public function process_member($data) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/group/lib.php");

        // NOTE: Always use groups_add_member() because it triggers events and verifies if user is enrolled.

        $data = (object)$data; // handy

        // get parent group->id
        $data->groupid = $this->get_new_parentid('group');

        // map user newitemid and insert if not member already
        if ($data->userid = $this->get_mappingid('user', $data->userid)) {
            if (!$DB->record_exists('groups_members', array('groupid' => $data->groupid, 'userid' => $data->userid))) {
                // Check the component, if any, exists.
                if (empty($data->component)) {
                    groups_add_member($data->groupid, $data->userid);

                } else if ((strpos($data->component, 'enrol_') === 0)) {
                    // Deal with enrolment groups - ignore the component and just find out the instance via new id,
                    // it is possible that enrolment was restored using different plugin type.
                    if (!isset($this->plugins)) {
                        $this->plugins = enrol_get_plugins(true);
                    }
                    if ($enrolid = $this->get_mappingid('enrol', $data->itemid)) {
                        if ($instance = $DB->get_record('enrol', array('id'=>$enrolid))) {
                            if (isset($this->plugins[$instance->enrol])) {
                                $this->plugins[$instance->enrol]->restore_group_member($instance, $data->groupid, $data->userid);
                            }
                        }
                    }

                } else {
                    $dir = core_component::get_component_directory($data->component);
                    if ($dir and is_dir($dir)) {
                        if (component_callback($data->component, 'restore_group_member', array($this, $data), true)) {
                            return;
                        }
                    }
                    // Bad luck, plugin could not restore the data, let's add normal membership.
                    groups_add_member($data->groupid, $data->userid);
                    $message = "Restore of '$data->component/$data->itemid' group membership is not supported, using standard group membership instead.";
                    $this->log($message, backup::LOG_WARNING);
                }
            }
        }
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
            $systemctx = context_system::instance();
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
            $systemctx = context_system::instance();
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
    /** @var array Cache: Array of id => course format */
    private static $courseformats = array();

    /**
     * Resets a static cache of course formats. Required for unit testing.
     */
    public static function reset_caches() {
        self::$courseformats = array();
    }

    protected function define_structure() {
        global $CFG;

        $paths = array();

        $section = new restore_path_element('section', '/section');
        $paths[] = $section;
        if ($CFG->enableavailability) {
            $paths[] = new restore_path_element('availability', '/section/availability');
            $paths[] = new restore_path_element('availability_field', '/section/availability_field');
        }
        $paths[] = new restore_path_element('course_format_options', '/section/course_format_options');

        // Apply for 'format' plugins optional paths at section level
        $this->add_plugin_structure('format', $section);

        // Apply for 'local' plugins optional paths at section level
        $this->add_plugin_structure('local', $section);

        return $paths;
    }

    public function process_section($data) {
        global $CFG, $DB;
        $data = (object)$data;
        $oldid = $data->id; // We'll need this later

        $restorefiles = false;

        // Look for the section
        $section = new stdclass();
        $section->course  = $this->get_courseid();
        $section->section = $data->number;
        $section->timemodified = $data->timemodified ?? 0;
        // Section doesn't exist, create it with all the info from backup
        if (!$secrec = $DB->get_record('course_sections', ['course' => $this->get_courseid(), 'section' => $data->number])) {
            $section->name = $data->name;
            $section->summary = $data->summary;
            $section->summaryformat = $data->summaryformat;
            $section->sequence = '';
            $section->visible = $data->visible;
            if (empty($CFG->enableavailability)) { // Process availability information only if enabled.
                $section->availability = null;
            } else {
                $section->availability = isset($data->availabilityjson) ? $data->availabilityjson : null;
                // Include legacy [<2.7] availability data if provided.
                if (is_null($section->availability)) {
                    $section->availability = \core_availability\info::convert_legacy_fields(
                            $data, true);
                }
            }
            $newitemid = $DB->insert_record('course_sections', $section);
            $section->id = $newitemid;

            core\event\course_section_created::create_from_section($section)->trigger();

            $restorefiles = true;

        // Section exists, update non-empty information
        } else {
            $section->id = $secrec->id;
            if ((string)$secrec->name === '') {
                $section->name = $data->name;
            }
            if (empty($secrec->summary)) {
                $section->summary = $data->summary;
                $section->summaryformat = $data->summaryformat;
                $restorefiles = true;
            }

            // Don't update availability (I didn't see a useful way to define
            // whether existing or new one should take precedence).

            $DB->update_record('course_sections', $section);
            $newitemid = $secrec->id;

            // Trigger an event for course section update.
            $event = \core\event\course_section_updated::create(
                array(
                    'objectid' => $section->id,
                    'courseid' => $section->course,
                    'context' => context_course::instance($section->course),
                    'other' => array('sectionnum' => $section->section)
                )
            );
            $event->trigger();
        }

        // Annotate the section mapping, with restorefiles option if needed
        $this->set_mapping('course_section', $oldid, $newitemid, $restorefiles);

        // set the new course_section id in the task
        $this->task->set_sectionid($newitemid);

        // If there is the legacy showavailability data, store this for later use.
        // (This data is not present when restoring 'new' backups.)
        if (isset($data->showavailability)) {
            // Cache the showavailability flag using the backup_ids data field.
            restore_dbops::set_backup_ids_record($this->get_restoreid(),
                    'section_showavailability', $newitemid, 0, null,
                    (object)array('showavailability' => $data->showavailability));
        }

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

    /**
     * Process the legacy availability table record. This table does not exist
     * in Moodle 2.7+ but we still support restore.
     *
     * @param stdClass $data Record data
     */
    public function process_availability($data) {
        $data = (object)$data;
        // Simply going to store the whole availability record now, we'll process
        // all them later in the final task (once all activities have been restored)
        // Let's call the low level one to be able to store the whole object.
        $data->coursesectionid = $this->task->get_sectionid();
        restore_dbops::set_backup_ids_record($this->get_restoreid(),
                'section_availability', $data->id, 0, null, $data);
    }

    /**
     * Process the legacy availability fields table record. This table does not
     * exist in Moodle 2.7+ but we still support restore.
     *
     * @param stdClass $data Record data
     */
    public function process_availability_field($data) {
        global $DB;
        $data = (object)$data;
        // Mark it is as passed by default
        $passed = true;
        $customfieldid = null;

        // If a customfield has been used in order to pass we must be able to match an existing
        // customfield by name (data->customfield) and type (data->customfieldtype)
        if (is_null($data->customfield) xor is_null($data->customfieldtype)) {
            // xor is sort of uncommon. If either customfield is null or customfieldtype is null BUT not both.
            // If one is null but the other isn't something clearly went wrong and we'll skip this condition.
            $passed = false;
        } else if (!is_null($data->customfield)) {
            $params = array('shortname' => $data->customfield, 'datatype' => $data->customfieldtype);
            $customfieldid = $DB->get_field('user_info_field', 'id', $params);
            $passed = ($customfieldid !== false);
        }

        if ($passed) {
            // Create the object to insert into the database
            $availfield = new stdClass();
            $availfield->coursesectionid = $this->task->get_sectionid();
            $availfield->userfield = $data->userfield;
            $availfield->customfieldid = $customfieldid;
            $availfield->operator = $data->operator;
            $availfield->value = $data->value;

            // Get showavailability option.
            $showrec = restore_dbops::get_backup_ids_record($this->get_restoreid(),
                    'section_showavailability', $availfield->coursesectionid);
            if (!$showrec) {
                // Should not happen.
                throw new coding_exception('No matching showavailability record');
            }
            $show = $showrec->info->showavailability;

            // The $availfield object is now in the format used in the old
            // system. Interpret this and convert to new system.
            $currentvalue = $DB->get_field('course_sections', 'availability',
                    array('id' => $availfield->coursesectionid), MUST_EXIST);
            $newvalue = \core_availability\info::add_legacy_availability_field_condition(
                    $currentvalue, $availfield, $show);

            $section = new stdClass();
            $section->id = $availfield->coursesectionid;
            $section->availability = $newvalue;
            $section->timemodified = time();
            $DB->update_record('course_sections', $section);
        }
    }

    public function process_course_format_options($data) {
        global $DB;
        $courseid = $this->get_courseid();
        if (!array_key_exists($courseid, self::$courseformats)) {
            // It is safe to have a static cache of course formats because format can not be changed after this point.
            self::$courseformats[$courseid] = $DB->get_field('course', 'format', array('id' => $courseid));
        }
        $data = (array)$data;
        if (self::$courseformats[$courseid] === $data['format']) {
            // Import section format options only if both courses (the one that was backed up
            // and the one we are restoring into) have same formats.
            $params = array(
                'courseid' => $this->get_courseid(),
                'sectionid' => $this->task->get_sectionid(),
                'format' => $data['format'],
                'name' => $data['name']
            );
            if ($record = $DB->get_record('course_format_options', $params, 'id, value')) {
                // Do not overwrite existing information.
                $newid = $record->id;
            } else {
                $params['value'] = $data['value'];
                $newid = $DB->insert_record('course_format_options', $params);
            }
            $this->set_mapping('course_format_options', $data['id'], $newid);
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
    /**
     * @var bool this gets set to true by {@link process_course()} if we are
     * restoring an old coures that used the legacy 'module security' feature.
     * If so, we have to do more work in {@link after_execute()}.
     */
    protected $legacyrestrictmodules = false;

    /**
     * @var array Used when {@link $legacyrestrictmodules} is true. This is an
     * array with array keys the module names ('forum', 'quiz', etc.). These are
     * the modules that are allowed according to the data in the backup file.
     * In {@link after_execute()} we then have to prevent adding of all the other
     * types of activity.
     */
    protected $legacyallowedmodules = array();

    protected function define_structure() {

        $course = new restore_path_element('course', '/course');
        $category = new restore_path_element('category', '/course/category');
        $tag = new restore_path_element('tag', '/course/tags/tag');
        $customfield = new restore_path_element('customfield', '/course/customfields/customfield');
        $allowed_module = new restore_path_element('allowed_module', '/course/allowed_modules/module');

        // Apply for 'format' plugins optional paths at course level
        $this->add_plugin_structure('format', $course);

        // Apply for 'theme' plugins optional paths at course level
        $this->add_plugin_structure('theme', $course);

        // Apply for 'report' plugins optional paths at course level
        $this->add_plugin_structure('report', $course);

        // Apply for 'course report' plugins optional paths at course level
        $this->add_plugin_structure('coursereport', $course);

        // Apply for plagiarism plugins optional paths at course level
        $this->add_plugin_structure('plagiarism', $course);

        // Apply for local plugins optional paths at course level
        $this->add_plugin_structure('local', $course);

        // Apply for admin tool plugins optional paths at course level.
        $this->add_plugin_structure('tool', $course);

        return array($course, $category, $tag, $customfield, $allowed_module);
    }

    /**
     * Processing functions go here
     *
     * @global moodledatabase $DB
     * @param stdClass $data
     */
    public function process_course($data) {
        global $CFG, $DB;
        $context = context::instance_by_id($this->task->get_contextid());
        $userid = $this->task->get_userid();
        $target = $this->get_task()->get_target();
        $isnewcourse = $target == backup::TARGET_NEW_COURSE;

        // When restoring to a new course we can set all the things except for the ID number.
        $canchangeidnumber = $isnewcourse || has_capability('moodle/course:changeidnumber', $context, $userid);
        $canchangesummary = $isnewcourse || has_capability('moodle/course:changesummary', $context, $userid);
        $canforcelanguage = has_capability('moodle/course:setforcedlanguage', $context, $userid);

        $data = (object)$data;
        $data->id = $this->get_courseid();

        // Calculate final course names, to avoid dupes.
        $fullname  = $this->get_setting_value('course_fullname');
        $shortname = $this->get_setting_value('course_shortname');
        list($data->fullname, $data->shortname) = restore_dbops::calculate_course_names($this->get_courseid(),
            $fullname === false ? $data->fullname : $fullname,
            $shortname === false ? $data->shortname : $shortname);
        // Do not modify the course names at all when merging and user selected to keep the names (or prohibited by cap).
        if (!$isnewcourse && $fullname === false) {
            unset($data->fullname);
        }
        if (!$isnewcourse && $shortname === false) {
            unset($data->shortname);
        }

        // Unset summary if user can't change it.
        if (!$canchangesummary) {
            unset($data->summary);
            unset($data->summaryformat);
        }

        // Unset lang if user can't change it.
        if (!$canforcelanguage) {
            unset($data->lang);
        }

        // Only allow the idnumber to be set if the user has permission and the idnumber is not already in use by
        // another course on this site.
        if (!empty($data->idnumber) && $canchangeidnumber && $this->task->is_samesite()
                && !$DB->record_exists('course', array('idnumber' => $data->idnumber))) {
            // Do not reset idnumber.

        } else if (!$isnewcourse) {
            // Prevent override when restoring as merge.
            unset($data->idnumber);

        } else {
            $data->idnumber = '';
        }

        // Any empty value for course->hiddensections will lead to 0 (default, show collapsed).
        // It has been reported that some old 1.9 courses may have it null leading to DB error. MDL-31532
        if (empty($data->hiddensections)) {
            $data->hiddensections = 0;
        }

        // Set legacyrestrictmodules to true if the course was resticting modules. If so
        // then we will need to process restricted modules after execution.
        $this->legacyrestrictmodules = !empty($data->restrictmodules);

        $data->startdate= $this->apply_date_offset($data->startdate);
        if (isset($data->enddate)) {
            $data->enddate = $this->apply_date_offset($data->enddate);
        }

        if ($data->defaultgroupingid) {
            $data->defaultgroupingid = $this->get_mappingid('grouping', $data->defaultgroupingid);
        }
        if (empty($CFG->enablecompletion)) {
            $data->enablecompletion = 0;
            $data->completionstartonenrol = 0;
            $data->completionnotify = 0;
        }
        $languages = get_string_manager()->get_list_of_translations(); // Get languages for quick search
        if (isset($data->lang) && !array_key_exists($data->lang, $languages)) {
            $data->lang = '';
        }

        $themes = get_list_of_themes(); // Get themes for quick search later
        if (!array_key_exists($data->theme, $themes) || empty($CFG->allowcoursethemes)) {
            $data->theme = '';
        }

        // Check if this is an old SCORM course format.
        if ($data->format == 'scorm') {
            $data->format = 'singleactivity';
            $data->activitytype = 'scorm';
        }

        // Course record ready, update it
        $DB->update_record('course', $data);

        course_get_format($data)->update_course_format_options($data);

        // Role name aliases
        restore_dbops::set_course_role_names($this->get_restoreid(), $this->get_courseid());
    }

    public function process_category($data) {
        // Nothing to do with the category. UI sets it before restore starts
    }

    public function process_tag($data) {
        global $CFG, $DB;

        $data = (object)$data;

        core_tag_tag::add_item_tag('core', 'course', $this->get_courseid(),
                context_course::instance($this->get_courseid()), $data->rawname);
    }

    /**
     * Process custom fields
     *
     * @param array $data
     */
    public function process_customfield($data) {
        $handler = core_course\customfield\course_handler::create();
        $handler->restore_instance_data_from_backup($this->task, $data);
    }

    public function process_allowed_module($data) {
        $data = (object)$data;

        // Backwards compatiblity support for the data that used to be in the
        // course_allowed_modules table.
        if ($this->legacyrestrictmodules) {
            $this->legacyallowedmodules[$data->modulename] = 1;
        }
    }

    protected function after_execute() {
        global $DB;

        // Add course related files, without itemid to match
        $this->add_related_files('course', 'summary', null);
        $this->add_related_files('course', 'overviewfiles', null);

        // Deal with legacy allowed modules.
        if ($this->legacyrestrictmodules) {
            $context = context_course::instance($this->get_courseid());

            list($roleids) = get_roles_with_cap_in_context($context, 'moodle/course:manageactivities');
            list($managerroleids) = get_roles_with_cap_in_context($context, 'moodle/site:config');
            foreach ($managerroleids as $roleid) {
                unset($roleids[$roleid]);
            }

            foreach (core_component::get_plugin_list('mod') as $modname => $notused) {
                if (isset($this->legacyallowedmodules[$modname])) {
                    // Module is allowed, no worries.
                    continue;
                }

                $capability = 'mod/' . $modname . ':addinstance';
                foreach ($roleids as $roleid) {
                    assign_capability($capability, CAP_PREVENT, $roleid, $context);
                }
            }
        }
    }
}

/**
 * Execution step that will migrate legacy files if present.
 */
class restore_course_legacy_files_step extends restore_execution_step {
    public function define_execution() {
        global $DB;

        // Do a check for legacy files and skip if there are none.
        $sql = 'SELECT count(*)
                  FROM {backup_files_temp}
                 WHERE backupid = ?
                   AND contextid = ?
                   AND component = ?
                   AND filearea  = ?';
        $params = array($this->get_restoreid(), $this->task->get_old_contextid(), 'course', 'legacy');

        if ($DB->count_records_sql($sql, $params)) {
            $DB->set_field('course', 'legacyfiles', 2, array('id' => $this->get_courseid()));
            restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'course',
                'legacy', $this->task->get_old_contextid(), $this->task->get_userid());
        }
    }
}

/*
 * Structure step that will read the roles.xml file (at course/activity/block levels)
 * containing all the role_assignments and overrides for that context. If corresponding to
 * one mapped role, they will be applied to target context. Will observe the role_assignments
 * setting to decide if ras are restored.
 *
 * Note: this needs to be executed after all users are enrolled.
 */
class restore_ras_and_caps_structure_step extends restore_structure_step {
    protected $plugins = null;

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
            // Deal with enrolment roles - ignore the component and just find out the instance via new id,
            // it is possible that enrolment was restored using different plugin type.
            if (!isset($this->plugins)) {
                $this->plugins = enrol_get_plugins(true);
            }
            if ($enrolid = $this->get_mappingid('enrol', $data->itemid)) {
                if ($instance = $DB->get_record('enrol', array('id'=>$enrolid))) {
                    if (isset($this->plugins[$instance->enrol])) {
                        $this->plugins[$instance->enrol]->restore_role_assignment($instance, $newroleid, $newuserid, $contextid);
                    }
                }
            }

        } else {
            $data->roleid    = $newroleid;
            $data->userid    = $newuserid;
            $data->contextid = $contextid;
            $dir = core_component::get_component_directory($data->component);
            if ($dir and is_dir($dir)) {
                if (component_callback($data->component, 'restore_role_assignment', array($this, $data), true)) {
                    return;
                }
            }
            // Bad luck, plugin could not restore the data, let's add normal membership.
            role_assign($data->roleid, $data->userid, $data->contextid);
            $message = "Restore of '$data->component/$data->itemid' role assignments is not supported, using manual role assignments instead.";
            $this->log($message, backup::LOG_WARNING);
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
 * If no instances yet add default enrol methods the same way as when creating new course in UI.
 */
class restore_default_enrolments_step extends restore_execution_step {

    public function define_execution() {
        global $DB;

        // No enrolments in front page.
        if ($this->get_courseid() == SITEID) {
            return;
        }

        $course = $DB->get_record('course', array('id'=>$this->get_courseid()), '*', MUST_EXIST);

        if ($DB->record_exists('enrol', array('courseid'=>$this->get_courseid(), 'enrol'=>'manual'))) {
            // Something already added instances, do not add default instances.
            $plugins = enrol_get_plugins(true);
            foreach ($plugins as $plugin) {
                $plugin->restore_sync_course($course);
            }

        } else {
            // Looks like a newly created course.
            enrol_course_updated(true, $course, null);
        }
    }
}

/**
 * This structure steps restores the enrol plugins and their underlying
 * enrolments, performing all the mappings and/or movements required
 */
class restore_enrolments_structure_step extends restore_structure_step {
    protected $enrolsynced = false;
    protected $plugins = null;
    protected $originalstatus = array();

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

        if ($this->get_courseid() == SITEID) {
            return false;
        }

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

        $userinfo = $this->get_setting_value('users');

        $paths = [];
        $paths[] = $enrol = new restore_path_element('enrol', '/enrolments/enrols/enrol');
        if ($userinfo) {
            $paths[] = new restore_path_element('enrolment', '/enrolments/enrols/enrol/user_enrolments/enrolment');
        }
        // Attach local plugin stucture to enrol element.
        $this->add_plugin_structure('enrol', $enrol);

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
        $oldid = $data->id; // We'll need this later.
        unset($data->id);

        $this->originalstatus[$oldid] = $data->status;

        if (!$courserec = $DB->get_record('course', array('id' => $this->get_courseid()))) {
            $this->set_mapping('enrol', $oldid, 0);
            return;
        }

        if (!isset($this->plugins)) {
            $this->plugins = enrol_get_plugins(true);
        }

        if (!$this->enrolsynced) {
            // Make sure that all plugin may create instances and enrolments automatically
            // before the first instance restore - this is suitable especially for plugins
            // that synchronise data automatically using course->idnumber or by course categories.
            foreach ($this->plugins as $plugin) {
                $plugin->restore_sync_course($courserec);
            }
            $this->enrolsynced = true;
        }

        // Map standard fields - plugin has to process custom fields manually.
        $data->roleid   = $this->get_mappingid('role', $data->roleid);
        $data->courseid = $courserec->id;

        if (!$this->get_setting_value('users') && $this->get_setting_value('enrolments') == backup::ENROL_WITHUSERS) {
            $converttomanual = true;
        } else {
            $converttomanual = ($this->get_setting_value('enrolments') == backup::ENROL_NEVER);
        }

        if ($converttomanual) {
            // Restore enrolments as manual enrolments.
            unset($data->sortorder); // Remove useless sortorder from <2.4 backups.
            if (!enrol_is_enabled('manual')) {
                $this->set_mapping('enrol', $oldid, 0);
                return;
            }
            if ($instances = $DB->get_records('enrol', array('courseid'=>$data->courseid, 'enrol'=>'manual'), 'id')) {
                $instance = reset($instances);
                $this->set_mapping('enrol', $oldid, $instance->id);
            } else {
                if ($data->enrol === 'manual') {
                    $instanceid = $this->plugins['manual']->add_instance($courserec, (array)$data);
                } else {
                    $instanceid = $this->plugins['manual']->add_default_instance($courserec);
                }
                $this->set_mapping('enrol', $oldid, $instanceid);
            }

        } else {
            if (!enrol_is_enabled($data->enrol) or !isset($this->plugins[$data->enrol])) {
                $this->set_mapping('enrol', $oldid, 0);
                $message = "Enrol plugin '$data->enrol' data can not be restored because it is not enabled, consider restoring without enrolment methods";
                $this->log($message, backup::LOG_WARNING);
                return;
            }
            if ($task = $this->get_task() and $task->get_target() == backup::TARGET_NEW_COURSE) {
                // Let's keep the sortorder in old backups.
            } else {
                // Prevent problems with colliding sortorders in old backups,
                // new 2.4 backups do not need sortorder because xml elements are ordered properly.
                unset($data->sortorder);
            }
            // Note: plugin is responsible for setting up the mapping, it may also decide to migrate to different type.
            $this->plugins[$data->enrol]->restore_instance($this, $data, $courserec, $oldid);
        }
    }

    /**
     * Create user enrolments.
     *
     * This has to be called after creation of enrolment instances
     * and before adding of role assignments.
     *
     * Roles are assigned in restore_ras_and_caps_structure_step::process_assignment() processing afterwards.
     *
     * @param mixed $data
     * @return void
     */
    public function process_enrolment($data) {
        global $DB;

        if (!isset($this->plugins)) {
            $this->plugins = enrol_get_plugins(true);
        }

        $data = (object)$data;

        // Process only if parent instance have been mapped.
        if ($enrolid = $this->get_new_parentid('enrol')) {
            $oldinstancestatus = ENROL_INSTANCE_ENABLED;
            $oldenrolid = $this->get_old_parentid('enrol');
            if (isset($this->originalstatus[$oldenrolid])) {
                $oldinstancestatus = $this->originalstatus[$oldenrolid];
            }
            if ($instance = $DB->get_record('enrol', array('id'=>$enrolid))) {
                // And only if user is a mapped one.
                if ($userid = $this->get_mappingid('user', $data->userid)) {
                    if (isset($this->plugins[$instance->enrol])) {
                        $this->plugins[$instance->enrol]->restore_user_enrolment($this, $data, $instance, $userid, $oldinstancestatus);
                    }
                }
            }
        }
    }
}


/**
 * Make sure the user restoring the course can actually access it.
 */
class restore_fix_restorer_access_step extends restore_execution_step {
    protected function define_execution() {
        global $CFG, $DB;

        if (!$userid = $this->task->get_userid()) {
            return;
        }

        if (empty($CFG->restorernewroleid)) {
            // Bad luck, no fallback role for restorers specified
            return;
        }

        $courseid = $this->get_courseid();
        $context = context_course::instance($courseid);

        if (is_enrolled($context, $userid, 'moodle/course:update', true) or is_viewing($context, $userid, 'moodle/course:update')) {
            // Current user may access the course (admin, category manager or restored teacher enrolment usually)
            return;
        }

        // Try to add role only - we do not need enrolment if user has moodle/course:view or is already enrolled
        role_assign($CFG->restorernewroleid, $userid, $context);

        if (is_enrolled($context, $userid, 'moodle/course:update', true) or is_viewing($context, $userid, 'moodle/course:update')) {
            // Extra role is enough, yay!
            return;
        }

        // The last chance is to create manual enrol if it does not exist and and try to enrol the current user,
        // hopefully admin selected suitable $CFG->restorernewroleid ...
        if (!enrol_is_enabled('manual')) {
            return;
        }
        if (!$enrol = enrol_get_plugin('manual')) {
            return;
        }
        if (!$DB->record_exists('enrol', array('enrol'=>'manual', 'courseid'=>$courseid))) {
            $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
            $fields = array('status'=>ENROL_INSTANCE_ENABLED, 'enrolperiod'=>$enrol->get_config('enrolperiod', 0), 'roleid'=>$enrol->get_config('roleid', 0));
            $enrol->add_instance($course, $fields);
        }

        enrol_try_internal_enrol($courseid, $userid);
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

        if (strpos($data->filter, 'filter/') === 0) {
            $data->filter = substr($data->filter, 7);

        } else if (strpos($data->filter, '/') !== false) {
            // Unsupported old filter.
            return;
        }

        if (!filter_is_enabled($data->filter)) { // Not installed or not enabled, nothing to do
            return;
        }
        filter_set_local_state($data->filter, $this->task->get_contextid(), $data->active);
    }

    public function process_config($data) {

        $data = (object)$data;

        if (strpos($data->filter, 'filter/') === 0) {
            $data->filter = substr($data->filter, 7);

        } else if (strpos($data->filter, '/') !== false) {
            // Unsupported old filter.
            return;
        }

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

/**
 * This structure steps restores the badges and their configs
 */
class restore_badges_structure_step extends restore_structure_step {

    /**
     * Conditionally decide if this step should be executed.
     *
     * This function checks the following parameters:
     *
     *   1. Badges and course badges are enabled on the site.
     *   2. The course/badges.xml file exists.
     *   3. All modules are restorable.
     *   4. All modules are marked for restore.
     *
     * @return bool True is safe to execute, false otherwise
     */
    protected function execute_condition() {
        global $CFG;

        // First check is badges and course level badges are enabled on this site.
        if (empty($CFG->enablebadges) || empty($CFG->badges_allowcoursebadges)) {
            // Disabled, don't restore course badges.
            return false;
        }

        // Check if badges.xml is included in the backup.
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            // Not found, can't restore course badges.
            return false;
        }

        // Check we are able to restore all backed up modules.
        if ($this->task->is_missing_modules()) {
            return false;
        }

        // Finally check all modules within the backup are being restored.
        if ($this->task->is_excluding_activities()) {
            return false;
        }

        return true;
    }

    protected function define_structure() {
        $paths = array();
        $paths[] = new restore_path_element('badge', '/badges/badge');
        $paths[] = new restore_path_element('criterion', '/badges/badge/criteria/criterion');
        $paths[] = new restore_path_element('parameter', '/badges/badge/criteria/criterion/parameters/parameter');
        $paths[] = new restore_path_element('endorsement', '/badges/badge/endorsement');
        $paths[] = new restore_path_element('alignment', '/badges/badge/alignments/alignment');
        $paths[] = new restore_path_element('relatedbadge', '/badges/badge/relatedbadges/relatedbadge');
        $paths[] = new restore_path_element('manual_award', '/badges/badge/manual_awards/manual_award');

        return $paths;
    }

    public function process_badge($data) {
        global $DB, $CFG;

        require_once($CFG->libdir . '/badgeslib.php');

        $data = (object)$data;
        $data->usercreated = $this->get_mappingid('user', $data->usercreated);
        if (empty($data->usercreated)) {
            $data->usercreated = $this->task->get_userid();
        }
        $data->usermodified = $this->get_mappingid('user', $data->usermodified);
        if (empty($data->usermodified)) {
            $data->usermodified = $this->task->get_userid();
        }

        // We'll restore the badge image.
        $restorefiles = true;

        $courseid = $this->get_courseid();

        $params = array(
                'name'           => $data->name,
                'description'    => $data->description,
                'timecreated'    => $data->timecreated,
                'timemodified'   => $data->timemodified,
                'usercreated'    => $data->usercreated,
                'usermodified'   => $data->usermodified,
                'issuername'     => $data->issuername,
                'issuerurl'      => $data->issuerurl,
                'issuercontact'  => $data->issuercontact,
                'expiredate'     => $this->apply_date_offset($data->expiredate),
                'expireperiod'   => $data->expireperiod,
                'type'           => BADGE_TYPE_COURSE,
                'courseid'       => $courseid,
                'message'        => $data->message,
                'messagesubject' => $data->messagesubject,
                'attachment'     => $data->attachment,
                'notification'   => $data->notification,
                'status'         => BADGE_STATUS_INACTIVE,
                'nextcron'       => $data->nextcron,
                'version'        => $data->version,
                'language'       => $data->language,
                'imageauthorname' => $data->imageauthorname,
                'imageauthoremail' => $data->imageauthoremail,
                'imageauthorurl' => $data->imageauthorurl,
                'imagecaption'   => $data->imagecaption
        );

        $newid = $DB->insert_record('badge', $params);
        $this->set_mapping('badge', $data->id, $newid, $restorefiles);
    }

    /**
     * Create an endorsement for a badge.
     *
     * @param mixed $data
     * @return void
     */
    public function process_endorsement($data) {
        global $DB;

        $data = (object)$data;

        $params = [
            'badgeid' => $this->get_new_parentid('badge'),
            'issuername' => $data->issuername,
            'issuerurl' => $data->issuerurl,
            'issueremail' => $data->issueremail,
            'claimid' => $data->claimid,
            'claimcomment' => $data->claimcomment,
            'dateissued' => $this->apply_date_offset($data->dateissued)
        ];
        $newid = $DB->insert_record('badge_endorsement', $params);
        $this->set_mapping('endorsement', $data->id, $newid);
    }

    /**
     * Link to related badges for a badge. This relies on post processing in after_execute().
     *
     * @param mixed $data
     * @return void
     */
    public function process_relatedbadge($data) {
        global $DB;

        $data = (object)$data;
        $relatedbadgeid = $data->relatedbadgeid;

        if ($relatedbadgeid) {
            // Only backup and restore related badges if they are contained in the backup file.
            $params = array(
                    'badgeid'           => $this->get_new_parentid('badge'),
                    'relatedbadgeid'    => $relatedbadgeid
            );
            $newid = $DB->insert_record('badge_related', $params);
        }
    }

    /**
     * Link to an alignment for a badge.
     *
     * @param mixed $data
     * @return void
     */
    public function process_alignment($data) {
        global $DB;

        $data = (object)$data;
        $params = array(
                'badgeid'           => $this->get_new_parentid('badge'),
                'targetname'        => $data->targetname,
                'targeturl'         => $data->targeturl,
                'targetdescription' => $data->targetdescription,
                'targetframework'   => $data->targetframework,
                'targetcode'        => $data->targetcode
        );
        $newid = $DB->insert_record('badge_alignment', $params);
        $this->set_mapping('alignment', $data->id, $newid);
    }

    public function process_criterion($data) {
        global $DB;

        $data = (object)$data;

        $params = array(
                'badgeid'           => $this->get_new_parentid('badge'),
                'criteriatype'      => $data->criteriatype,
                'method'            => $data->method,
                'description'       => isset($data->description) ? $data->description : '',
                'descriptionformat' => isset($data->descriptionformat) ? $data->descriptionformat : 0,
        );

        $newid = $DB->insert_record('badge_criteria', $params);
        $this->set_mapping('criterion', $data->id, $newid);
    }

    public function process_parameter($data) {
        global $DB, $CFG;

        require_once($CFG->libdir . '/badgeslib.php');

        $data = (object)$data;
        $criteriaid = $this->get_new_parentid('criterion');

        // Parameter array that will go to database.
        $params = array();
        $params['critid'] = $criteriaid;

        $oldparam = explode('_', $data->name);

        if ($data->criteriatype == BADGE_CRITERIA_TYPE_ACTIVITY) {
            $module = $this->get_mappingid('course_module', $oldparam[1]);
            $params['name'] = $oldparam[0] . '_' . $module;
            $params['value'] = $oldparam[0] == 'module' ? $module : $data->value;
        } else if ($data->criteriatype == BADGE_CRITERIA_TYPE_COURSE) {
            $params['name'] = $oldparam[0] . '_' . $this->get_courseid();
            $params['value'] = $oldparam[0] == 'course' ? $this->get_courseid() : $data->value;
        } else if ($data->criteriatype == BADGE_CRITERIA_TYPE_MANUAL) {
            $role = $this->get_mappingid('role', $data->value);
            if (!empty($role)) {
                $params['name'] = 'role_' . $role;
                $params['value'] = $role;
            } else {
                return;
            }
        } else if ($data->criteriatype == BADGE_CRITERIA_TYPE_COMPETENCY) {
            $competencyid = $this->get_mappingid('competency', $data->value);
            if (!empty($competencyid)) {
                $params['name'] = 'competency_' . $competencyid;
                $params['value'] = $competencyid;
            } else {
                return;
            }
        }

        if (!$DB->record_exists('badge_criteria_param', $params)) {
            $DB->insert_record('badge_criteria_param', $params);
        }
    }

    public function process_manual_award($data) {
        global $DB;

        $data = (object)$data;
        $role = $this->get_mappingid('role', $data->issuerrole);

        if (!empty($role)) {
            $award = array(
                'badgeid'     => $this->get_new_parentid('badge'),
                'recipientid' => $this->get_mappingid('user', $data->recipientid),
                'issuerid'    => $this->get_mappingid('user', $data->issuerid),
                'issuerrole'  => $role,
                'datemet'     => $this->apply_date_offset($data->datemet)
            );

            // Skip the manual award if recipient or issuer can not be mapped to.
            if (empty($award['recipientid']) || empty($award['issuerid'])) {
                return;
            }

            $DB->insert_record('badge_manual_award', $award);
        }
    }

    protected function after_execute() {
        global $DB;
        // Add related files.
        $this->add_related_files('badges', 'badgeimage', 'badge');

        $badgeid = $this->get_new_parentid('badge');
        // Remap any related badges.
        // We do this in the DB directly because this is backup/restore it is not valid to call into
        // the component API.
        $params = array('badgeid' => $badgeid);
        $query = "SELECT DISTINCT br.id, br.badgeid, br.relatedbadgeid
                    FROM {badge_related} br
                   WHERE (br.badgeid = :badgeid)";
        $relatedbadges = $DB->get_records_sql($query, $params);
        $newrelatedids = [];
        foreach ($relatedbadges as $relatedbadge) {
            $relatedid = $this->get_mappingid('badge', $relatedbadge->relatedbadgeid);
            $params['relatedbadgeid'] = $relatedbadge->relatedbadgeid;
            $DB->delete_records_select('badge_related', '(badgeid = :badgeid AND relatedbadgeid = :relatedbadgeid)', $params);
            if ($relatedid) {
                $newrelatedids[] = $relatedid;
            }
        }
        if (!empty($newrelatedids)) {
            $relatedbadges = [];
            foreach ($newrelatedids as $relatedid) {
                $relatedbadge = new stdClass();
                $relatedbadge->badgeid = $badgeid;
                $relatedbadge->relatedbadgeid = $relatedid;
                $relatedbadges[] = $relatedbadge;
            }
            $DB->insert_records('badge_related', $relatedbadges);
        }
    }
}

/**
 * This structure steps restores the calendar events
 */
class restore_calendarevents_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('calendarevents', '/events/event');

        return $paths;
    }

    public function process_calendarevents($data) {
        global $DB, $SITE, $USER;

        $data = (object)$data;
        $oldid = $data->id;
        $restorefiles = true; // We'll restore the files

        // If this is a new action event, it will automatically be populated by the adhoc task.
        // Nothing to do here.
        if (isset($data->type) && $data->type == CALENDAR_EVENT_TYPE_ACTION) {
            return;
        }

        // User overrides for activities are identified by having a courseid of zero with
        // both a modulename and instance value set.
        $isuseroverride = !$data->courseid && $data->modulename && $data->instance;

        // If we don't want to include user data and this record is a user override event
        // for an activity then we should not create it. (Only activity events can be user override events - which must have this
        // setting).
        if ($isuseroverride && $this->task->setting_exists('userinfo') && !$this->task->get_setting_value('userinfo')) {
            return;
        }

        // Find the userid and the groupid associated with the event.
        $data->userid = $this->get_mappingid('user', $data->userid);
        if ($data->userid === false) {
            // Blank user ID means that we are dealing with module generated events such as quiz starting times.
            // Use the current user ID for these events.
            $data->userid = $USER->id;
        }
        if (!empty($data->groupid)) {
            $data->groupid = $this->get_mappingid('group', $data->groupid);
            if ($data->groupid === false) {
                return;
            }
        }
        // Handle events with empty eventtype //MDL-32827
        if(empty($data->eventtype)) {
            if ($data->courseid == $SITE->id) {                                // Site event
                $data->eventtype = "site";
            } else if ($data->courseid != 0 && $data->groupid == 0 && ($data->modulename == 'assignment' || $data->modulename == 'assign')) {
                // Course assingment event
                $data->eventtype = "due";
            } else if ($data->courseid != 0 && $data->groupid == 0) {      // Course event
                $data->eventtype = "course";
            } else if ($data->groupid) {                                      // Group event
                $data->eventtype = "group";
            } else if ($data->userid) {                                       // User event
                $data->eventtype = "user";
            } else {
                return;
            }
        }

        $params = array(
                'name'           => $data->name,
                'description'    => $data->description,
                'format'         => $data->format,
                // User overrides in activities use a course id of zero. All other event types
                // must use the mapped course id.
                'courseid'       => $data->courseid ? $this->get_courseid() : 0,
                'groupid'        => $data->groupid,
                'userid'         => $data->userid,
                'repeatid'       => $this->get_mappingid('event', $data->repeatid),
                'modulename'     => $data->modulename,
                'type'           => isset($data->type) ? $data->type : 0,
                'eventtype'      => $data->eventtype,
                'timestart'      => $this->apply_date_offset($data->timestart),
                'timeduration'   => $data->timeduration,
                'timesort'       => isset($data->timesort) ? $this->apply_date_offset($data->timesort) : null,
                'visible'        => $data->visible,
                'uuid'           => $data->uuid,
                'sequence'       => $data->sequence,
                'timemodified'   => $data->timemodified,
                'priority'       => isset($data->priority) ? $data->priority : null,
                'location'       => isset($data->location) ? $data->location : null);
        if ($this->name == 'activity_calendar') {
            $params['instance'] = $this->task->get_activityid();
        } else {
            $params['instance'] = 0;
        }
        $sql = "SELECT id
                  FROM {event}
                 WHERE " . $DB->sql_compare_text('name', 255) . " = " . $DB->sql_compare_text('?', 255) . "
                   AND courseid = ?
                   AND modulename = ?
                   AND instance = ?
                   AND timestart = ?
                   AND timeduration = ?
                   AND " . $DB->sql_compare_text('description', 255) . " = " . $DB->sql_compare_text('?', 255);
        $arg = array ($params['name'], $params['courseid'], $params['modulename'], $params['instance'], $params['timestart'], $params['timeduration'], $params['description']);
        $result = $DB->record_exists_sql($sql, $arg);
        if (empty($result)) {
            $newitemid = $DB->insert_record('event', $params);
            $this->set_mapping('event', $oldid, $newitemid);
            $this->set_mapping('event_description', $oldid, $newitemid, $restorefiles);
        }
        // With repeating events, each event has the repeatid pointed at the first occurrence.
        // Since the repeatid will be empty when the first occurrence is restored,
        // Get the repeatid from the second occurrence of the repeating event and use that to update the first occurrence.
        // Then keep a list of repeatids so we only perform this update once.
        static $repeatids = array();
        if (!empty($params['repeatid']) && !in_array($params['repeatid'], $repeatids)) {
            // This entry is repeated so the repeatid field must be set.
            $DB->set_field('event', 'repeatid', $params['repeatid'], array('id' => $params['repeatid']));
            $repeatids[] = $params['repeatid'];
        }

    }
    protected function after_execute() {
        // Add related files
        $this->add_related_files('calendar', 'event_description', 'event_description');
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
     *   5. No completion criteria already exist for the course.
     *
     * @return bool True is safe to execute, false otherwise
     */
    protected function execute_condition() {
        global $CFG, $DB;

        // First check course completion is enabled on this site
        if (empty($CFG->enablecompletion)) {
            // Disabled, don't restore course completion
            return false;
        }

        // No course completion on the front page.
        if ($this->get_courseid() == SITEID) {
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

        // Check all modules within the backup are being restored.
        if ($this->task->is_excluding_activities()) {
            return false;
        }

        // Check that no completion criteria is already set for the course.
        if ($DB->record_exists('course_completion_criteria', array('course' => $this->get_courseid()))) {
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
        if (isset($data->role) && $data->role != '') {
            // Newer backups should include roleshortname, which makes this much easier.
            if (!empty($data->roleshortname)) {
                $roleinstanceid = $DB->get_field('role', 'id', array('shortname' => $data->roleshortname));
                if (!$roleinstanceid) {
                    $this->log(
                        'Could not match the role shortname in course_completion_criteria, so skipping',
                        backup::LOG_DEBUG
                    );
                    return;
                }
                $data->role = $roleinstanceid;
            } else {
                $data->role = $this->get_mappingid('role', $data->role);
            }

            // Check we have an id, otherwise it causes all sorts of bugs.
            if (!$data->role) {
                $this->log(
                    'Could not match role in course_completion_criteria, so skipping',
                    backup::LOG_DEBUG
                );
                return;
            }
        }

        // If the completion criteria is for a module we need to map the module instance
        // to the new module id.
        if (!empty($data->moduleinstance) && !empty($data->module)) {
            $data->moduleinstance = $this->get_mappingid('course_module', $data->moduleinstance);
            if (empty($data->moduleinstance)) {
                $this->log(
                    'Could not match the module instance in course_completion_criteria, so skipping',
                    backup::LOG_DEBUG
                );
                return;
            }
        } else {
            $data->module = null;
            $data->moduleinstance = null;
        }

        // We backup the course shortname rather than the ID so that we can match back to the course
        if (!empty($data->courseinstanceshortname)) {
            $courseinstanceid = $DB->get_field('course', 'id', array('shortname'=>$data->courseinstanceshortname));
            if (!$courseinstanceid) {
                $this->log(
                    'Could not match the course instance in course_completion_criteria, so skipping',
                    backup::LOG_DEBUG
                );
                return;
            }
        } else {
            $courseinstanceid = null;
        }
        $data->courseinstance = $courseinstanceid;

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
                'timecompleted' => $data->timecompleted
            );
            if (isset($data->gradefinal)) {
                $params['gradefinal'] = $data->gradefinal;
            }
            if (isset($data->unenroled)) {
                $params['unenroled'] = $data->unenroled;
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
                'timeenrolled' => $data->timeenrolled,
                'timestarted' => $data->timestarted,
                'timecompleted' => $data->timecompleted,
                'reaggregate' => $data->reaggregate
            );

            $existing = $DB->get_record('course_completions', array(
                'userid' => $data->userid,
                'course' => $data->course
            ));

            // MDL-46651 - If cron writes out a new record before we get to it
            // then we should replace it with the Truth data from the backup.
            // This may be obsolete after MDL-48518 is resolved
            if ($existing) {
                $params['id'] = $existing->id;
                $DB->update_record('course_completions', $params);
            } else {
                $DB->insert_record('course_completions', $params);
            }
        }
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

        // There is no need to roll dates. Logs are supposed to be immutable. See MDL-44961.

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
            if (empty($data->url)) {
                $data->url = '';
            }
            if (empty($data->info)) {
                $data->info = '';
            }
            // Store the data in the legacy log table if we are still using it.
            $manager = get_log_manager();
            if (method_exists($manager, 'legacy_add_to_log')) {
                $manager->legacy_add_to_log($data->course, $data->module, $data->action, $data->url,
                    $data->info, $data->cmid, $data->userid, $data->ip, $data->time);
            }
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

        // There is no need to roll dates. Logs are supposed to be immutable. See MDL-44961.

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
            if (empty($data->url)) {
                $data->url = '';
            }
            if (empty($data->info)) {
                $data->info = '';
            }
            // Store the data in the legacy log table if we are still using it.
            $manager = get_log_manager();
            if (method_exists($manager, 'legacy_add_to_log')) {
                $manager->legacy_add_to_log($data->course, $data->module, $data->action, $data->url,
                    $data->info, $data->cmid, $data->userid, $data->ip, $data->time);
            }
        }
    }
}

/**
 * Structure step in charge of restoring the logstores.xml file for the course logs.
 *
 * This restore step will rebuild the logs for all the enabled logstore subplugins supporting
 * it, for logs belonging to the course level.
 */
class restore_course_logstores_structure_step extends restore_structure_step {

    /**
     * Conditionally decide if this step should be executed.
     *
     * This function checks the following parameter:
     *
     *   1. the logstores.xml file exists
     *
     * @return bool true is safe to execute, false otherwise
     */
    protected function execute_condition() {

        // Check it is included in the backup.
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            // Not found, can't restore logstores.xml information.
            return false;
        }

        return true;
    }

    /**
     * Return the elements to be processed on restore of logstores.
     *
     * @return restore_path_element[] array of elements to be processed on restore.
     */
    protected function define_structure() {

        $paths = array();

        $logstore = new restore_path_element('logstore', '/logstores/logstore');
        $paths[] = $logstore;

        // Add logstore subplugin support to the 'logstore' element.
        $this->add_subplugin_structure('logstore', $logstore, 'tool', 'log');

        return array($logstore);
    }

    /**
     * Process the 'logstore' element,
     *
     * Note: This is empty by definition in backup, because stores do not share any
     * data between them, so there is nothing to process here.
     *
     * @param array $data element data
     */
    protected function process_logstore($data) {
        return;
    }
}

/**
 * Structure step in charge of restoring the logstores.xml file for the activity logs.
 *
 * Note: Activity structure is completely equivalent to the course one, so just extend it.
 */
class restore_activity_logstores_structure_step extends restore_course_logstores_structure_step {
}

/**
 * Restore course competencies structure step.
 */
class restore_course_competencies_structure_step extends restore_structure_step {

    /**
     * Returns the structure.
     *
     * @return array
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value('users');
        $paths = array(
            new restore_path_element('course_competency', '/course_competencies/competencies/competency'),
            new restore_path_element('course_competency_settings', '/course_competencies/settings'),
        );
        if ($userinfo) {
            $paths[] = new restore_path_element('user_competency_course',
                '/course_competencies/user_competencies/user_competency');
        }
        return $paths;
    }

    /**
     * Process a course competency settings.
     *
     * @param array $data The data.
     */
    public function process_course_competency_settings($data) {
        global $DB;
        $data = (object) $data;

        // We do not restore the course settings during merge.
        $target = $this->get_task()->get_target();
        if ($target == backup::TARGET_CURRENT_ADDING || $target == backup::TARGET_EXISTING_ADDING) {
            return;
        }

        $courseid = $this->task->get_courseid();
        $exists = \core_competency\course_competency_settings::record_exists_select('courseid = :courseid',
            array('courseid' => $courseid));

        // Strangely the course settings already exist, let's just leave them as is then.
        if ($exists) {
            $this->log('Course competency settings not restored, existing settings have been found.', backup::LOG_WARNING);
            return;
        }

        $data = (object) array('courseid' => $courseid, 'pushratingstouserplans' => $data->pushratingstouserplans);
        $settings = new \core_competency\course_competency_settings(0, $data);
        $settings->create();
    }

    /**
     * Process a course competency.
     *
     * @param array $data The data.
     */
    public function process_course_competency($data) {
        $data = (object) $data;

        // Mapping the competency by ID numbers.
        $framework = \core_competency\competency_framework::get_record(array('idnumber' => $data->frameworkidnumber));
        if (!$framework) {
            return;
        }
        $competency = \core_competency\competency::get_record(array('idnumber' => $data->idnumber,
            'competencyframeworkid' => $framework->get('id')));
        if (!$competency) {
            return;
        }
        $this->set_mapping(\core_competency\competency::TABLE, $data->id, $competency->get('id'));

        $params = array(
            'competencyid' => $competency->get('id'),
            'courseid' => $this->task->get_courseid()
        );
        $query = 'competencyid = :competencyid AND courseid = :courseid';
        $existing = \core_competency\course_competency::record_exists_select($query, $params);

        if (!$existing) {
            // Sortorder is ignored by precaution, anyway we should walk through the records in the right order.
            $record = (object) $params;
            $record->ruleoutcome = $data->ruleoutcome;
            $coursecompetency = new \core_competency\course_competency(0, $record);
            $coursecompetency->create();
        }
    }

    /**
     * Process the user competency course.
     *
     * @param array $data The data.
     */
    public function process_user_competency_course($data) {
        global $USER, $DB;
        $data = (object) $data;

        $data->competencyid = $this->get_mappingid(\core_competency\competency::TABLE, $data->competencyid);
        if (!$data->competencyid) {
            // This is strange, the competency does not belong to the course.
            return;
        } else if ($data->grade === null) {
            // We do not need to do anything when there is no grade.
            return;
        }

        $data->userid = $this->get_mappingid('user', $data->userid);
        $shortname = $DB->get_field('course', 'shortname', array('id' => $this->task->get_courseid()), MUST_EXIST);

        // The method add_evidence also sets the course rating.
        \core_competency\api::add_evidence($data->userid,
                                           $data->competencyid,
                                           $this->task->get_contextid(),
                                           \core_competency\evidence::ACTION_OVERRIDE,
                                           'evidence_courserestored',
                                           'core_competency',
                                           $shortname,
                                           false,
                                           null,
                                           $data->grade,
                                           $USER->id);
    }

    /**
     * Execute conditions.
     *
     * @return bool
     */
    protected function execute_condition() {

        // Do not execute if competencies are not included.
        if (!$this->get_setting_value('competencies')) {
            return false;
        }

        // Do not execute if the competencies XML file is not found.
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            return false;
        }

        return true;
    }
}

/**
 * Restore activity competencies structure step.
 */
class restore_activity_competencies_structure_step extends restore_structure_step {

    /**
     * Defines the structure.
     *
     * @return array
     */
    protected function define_structure() {
        $paths = array(
            new restore_path_element('course_module_competency', '/course_module_competencies/competencies/competency')
        );
        return $paths;
    }

    /**
     * Process a course module competency.
     *
     * @param array $data The data.
     */
    public function process_course_module_competency($data) {
        $data = (object) $data;

        // Mapping the competency by ID numbers.
        $framework = \core_competency\competency_framework::get_record(array('idnumber' => $data->frameworkidnumber));
        if (!$framework) {
            return;
        }
        $competency = \core_competency\competency::get_record(array('idnumber' => $data->idnumber,
            'competencyframeworkid' => $framework->get('id')));
        if (!$competency) {
            return;
        }

        $params = array(
            'competencyid' => $competency->get('id'),
            'cmid' => $this->task->get_moduleid()
        );
        $query = 'competencyid = :competencyid AND cmid = :cmid';
        $existing = \core_competency\course_module_competency::record_exists_select($query, $params);

        if (!$existing) {
            // Sortorder is ignored by precaution, anyway we should walk through the records in the right order.
            $record = (object) $params;
            $record->ruleoutcome = $data->ruleoutcome;
            $coursemodulecompetency = new \core_competency\course_module_competency(0, $record);
            $coursemodulecompetency->create();
        }
    }

    /**
     * Execute conditions.
     *
     * @return bool
     */
    protected function execute_condition() {

        // Do not execute if competencies are not included.
        if (!$this->get_setting_value('competencies')) {
            return false;
        }

        // Do not execute if the competencies XML file is not found.
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            return false;
        }

        return true;
    }
}

/**
 * Defines the restore step for advanced grading methods attached to the activity module
 */
class restore_activity_grading_structure_step extends restore_structure_step {

    /**
     * This step is executed only if the grading file is present
     */
     protected function execute_condition() {

        if ($this->get_courseid() == SITEID) {
            return false;
        }

        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            return false;
        }

        return true;
    }


    /**
     * Declares paths in the grading.xml file we are interested in
     */
    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $area = new restore_path_element('grading_area', '/areas/area');
        $paths[] = $area;
        // attach local plugin stucture to $area element
        $this->add_plugin_structure('local', $area);

        $definition = new restore_path_element('grading_definition', '/areas/area/definitions/definition');
        $paths[] = $definition;
        $this->add_plugin_structure('gradingform', $definition);
        // attach local plugin stucture to $definition element
        $this->add_plugin_structure('local', $definition);


        if ($userinfo) {
            $instance = new restore_path_element('grading_instance',
                '/areas/area/definitions/definition/instances/instance');
            $paths[] = $instance;
            $this->add_plugin_structure('gradingform', $instance);
            // attach local plugin stucture to $intance element
            $this->add_plugin_structure('local', $instance);
        }

        return $paths;
    }

    /**
     * Processes one grading area element
     *
     * @param array $data element data
     */
    protected function process_grading_area($data) {
        global $DB;

        $task = $this->get_task();
        $data = (object)$data;
        $oldid = $data->id;
        $data->component = 'mod_'.$task->get_modulename();
        $data->contextid = $task->get_contextid();

        $newid = $DB->insert_record('grading_areas', $data);
        $this->set_mapping('grading_area', $oldid, $newid);
    }

    /**
     * Processes one grading definition element
     *
     * @param array $data element data
     */
    protected function process_grading_definition($data) {
        global $DB;

        $task = $this->get_task();
        $data = (object)$data;
        $oldid = $data->id;
        $data->areaid = $this->get_new_parentid('grading_area');
        $data->copiedfromid = null;
        $data->timecreated = time();
        $data->usercreated = $task->get_userid();
        $data->timemodified = $data->timecreated;
        $data->usermodified = $data->usercreated;

        $newid = $DB->insert_record('grading_definitions', $data);
        $this->set_mapping('grading_definition', $oldid, $newid, true);
    }

    /**
     * Processes one grading form instance element
     *
     * @param array $data element data
     */
    protected function process_grading_instance($data) {
        global $DB;

        $data = (object)$data;

        // new form definition id
        $newformid = $this->get_new_parentid('grading_definition');

        // get the name of the area we are restoring to
        $sql = "SELECT ga.areaname
                  FROM {grading_definitions} gd
                  JOIN {grading_areas} ga ON gd.areaid = ga.id
                 WHERE gd.id = ?";
        $areaname = $DB->get_field_sql($sql, array($newformid), MUST_EXIST);

        // get the mapped itemid - the activity module is expected to define the mappings
        // for each gradable area
        $newitemid = $this->get_mappingid(restore_gradingform_plugin::itemid_mapping($areaname), $data->itemid);

        $oldid = $data->id;
        $data->definitionid = $newformid;
        $data->raterid = $this->get_mappingid('user', $data->raterid);
        $data->itemid = $newitemid;

        $newid = $DB->insert_record('grading_instances', $data);
        $this->set_mapping('grading_instance', $oldid, $newid);
    }

    /**
     * Final operations when the database records are inserted
     */
    protected function after_execute() {
        // Add files embedded into the definition description
        $this->add_related_files('grading', 'description', 'grading_definition');
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

    /**
     * No grades in front page.
     * @return bool
     */
    protected function execute_condition() {
        return ($this->get_courseid() != SITEID);
    }

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

        if (!empty($data->categoryid)) {
            // If the grade category id of the grade item being restored belongs to this course
            // then it is a fair assumption that this is the correct grade category for the activity
            // and we should leave it in place, if not then unset it.
            // TODO MDL-34790 Gradebook does not import if target course has gradebook categories.
            $conditions = array('id' => $data->categoryid, 'courseid' => $courseid);
            if (!$this->task->is_samesite() || !$DB->record_exists('grade_categories', $conditions)) {
                unset($data->categoryid);
            }
        }

        unset($data->id);
        $data->courseid     = $this->get_courseid();
        $data->iteminstance = $this->task->get_activityid();
        $data->idnumber     = $idnumber;
        $data->scaleid      = $this->get_mappingid('scale', $data->scaleid);
        $data->outcomeid    = $this->get_mappingid('outcome', $data->outcomeid);

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
        global $CFG;

        require_once($CFG->libdir . '/grade/constants.php');

        $data = (object)($data);
        $olduserid = $data->userid;
        $oldid = $data->id;
        unset($data->id);

        $data->itemid = $this->get_new_parentid('grade_item');

        $data->userid = $this->get_mappingid('user', $data->userid, null);
        if (!empty($data->userid)) {
            $data->usermodified = $this->get_mappingid('user', $data->usermodified, null);
            $data->rawscaleid = $this->get_mappingid('scale', $data->rawscaleid);

            $grade = new grade_grade($data, false);
            $grade->insert('restore');

            $this->set_mapping('grade_grades', $oldid, $grade->id, true);

            $this->add_related_files(
                GRADE_FILE_COMPONENT,
                GRADE_FEEDBACK_FILEAREA,
                'grade_grades',
                null,
                $oldid
            );
        } else {
            debugging("Mapped user id not found for user id '{$olduserid}', grade item id '{$data->itemid}'");
        }
    }

    /**
     * process activity grade_letters. Note that, while these are possible,
     * because grade_letters are contextid based, in practice, only course
     * context letters can be defined. So we keep here this method knowing
     * it won't be executed ever. gradebook restore will restore course letters.
     */
    protected function process_grade_letter($data) {
        global $DB;

        $data['contextid'] = $this->task->get_contextid();
        $gradeletter = (object)$data;

        // Check if it exists before adding it
        unset($data['id']);
        if (!$DB->record_exists('grade_letters', $data)) {
            $newitemid = $DB->insert_record('grade_letters', $gradeletter);
        }
        // no need to save any grade_letter mapping
    }

    public function after_restore() {
        // Fix grade item's sortorder after restore, as it might have duplicates.
        $courseid = $this->get_task()->get_courseid();
        grade_item::fix_duplicate_sortorder($courseid);
    }
}

/**
 * Step in charge of restoring the grade history of an activity.
 *
 * This step is added to the task regardless of the setting 'grade_histories'.
 * The reason is to allow for a more flexible step in case the logic needs to be
 * split accross different settings to control the history of items and/or grades.
 */
class restore_activity_grade_history_structure_step extends restore_structure_step {

    /**
     * This step is executed only if the grade history file is present.
     */
     protected function execute_condition() {

        if ($this->get_courseid() == SITEID) {
            return false;
        }

        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            return false;
        }
        return true;
    }

    protected function define_structure() {
        $paths = array();

        // Settings to use.
        $userinfo = $this->get_setting_value('userinfo');
        $history = $this->get_setting_value('grade_histories');

        if ($userinfo && $history) {
            $paths[] = new restore_path_element('grade_grade',
               '/grade_history/grade_grades/grade_grade');
        }

        return $paths;
    }

    protected function process_grade_grade($data) {
        global $CFG, $DB;

        require_once($CFG->libdir . '/grade/constants.php');

        $data = (object) $data;
        $oldhistoryid = $data->id;
        $olduserid = $data->userid;
        unset($data->id);

        $data->userid = $this->get_mappingid('user', $data->userid, null);
        if (!empty($data->userid)) {
            // Do not apply the date offsets as this is history.
            $data->itemid = $this->get_mappingid('grade_item', $data->itemid);
            $data->oldid = $this->get_mappingid('grade_grades', $data->oldid);
            $data->usermodified = $this->get_mappingid('user', $data->usermodified, null);
            $data->rawscaleid = $this->get_mappingid('scale', $data->rawscaleid);

            $newhistoryid = $DB->insert_record('grade_grades_history', $data);

            $this->set_mapping('grade_grades_history', $oldhistoryid, $newhistoryid, true);

            $this->add_related_files(
                GRADE_FILE_COMPONENT,
                GRADE_HISTORY_FEEDBACK_FILEAREA,
                'grade_grades_history',
                null,
                $oldhistoryid
            );
        } else {
            $message = "Mapped user id not found for user id '{$olduserid}', grade item id '{$data->itemid}'";
            $this->log($message, backup::LOG_DEBUG);
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
        if (!$bi = block_instance($data->blockname)) {
            return false;
        }

        if (!$bi->instance_allow_multiple()) {
            // The block cannot be added twice, so we will check if the same block is already being
            // displayed on the same page. For this, rather than mocking a page and using the block_manager
            // we use a similar query to the one in block_manager::load_blocks(), this will give us
            // a very good idea of the blocks already displayed in the context.
            $params =  array(
                'blockname' => $data->blockname
            );

            // Context matching test.
            $context = context::instance_by_id($data->parentcontextid);
            $contextsql = 'bi.parentcontextid = :contextid';
            $params['contextid'] = $context->id;

            $parentcontextids = $context->get_parent_context_ids();
            if ($parentcontextids) {
                list($parentcontextsql, $parentcontextparams) =
                        $DB->get_in_or_equal($parentcontextids, SQL_PARAMS_NAMED);
                $contextsql = "($contextsql OR (bi.showinsubcontexts = 1 AND bi.parentcontextid $parentcontextsql))";
                $params = array_merge($params, $parentcontextparams);
            }

            // Page type pattern test.
            $pagetypepatterns = matching_page_type_patterns_from_pattern($data->pagetypepattern);
            list($pagetypepatternsql, $pagetypepatternparams) =
                $DB->get_in_or_equal($pagetypepatterns, SQL_PARAMS_NAMED);
            $params = array_merge($params, $pagetypepatternparams);

            // Sub page pattern test.
            $subpagepatternsql = 'bi.subpagepattern IS NULL';
            if ($data->subpagepattern !== null) {
                $subpagepatternsql = "($subpagepatternsql OR bi.subpagepattern = :subpagepattern)";
                $params['subpagepattern'] = $data->subpagepattern;
            }

            $exists = $DB->record_exists_sql("SELECT bi.id
                                                FROM {block_instances} bi
                                                JOIN {block} b ON b.name = bi.blockname
                                               WHERE bi.blockname = :blockname
                                                 AND $contextsql
                                                 AND bi.pagetypepattern $pagetypepatternsql
                                                 AND $subpagepatternsql", $params);
            if ($exists) {
                // There is at least one very similar block visible on the page where we
                // are trying to restore the block. In these circumstances the block API
                // would not allow the user to add another instance of the block, so we
                // apply the same rule here.
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

        // Set timecreated, timemodified if not included (older backup).
        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }
        if (empty($data->timemodified)) {
            $data->timemodified = $data->timecreated;
        }

        // Create the block instance
        $newitemid = $DB->insert_record('block_instances', $data);
        // Save the mapping (with restorefiles support)
        $this->set_mapping('block_instance', $oldid, $newitemid, true);
        // Create the block context
        $newcontextid = context_block::instance($newitemid)->id;
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
            $paths[] = new restore_path_element('availability_field', '/module/availability_info/availability_field');
        }

        $paths[] = new restore_path_element('tag', '/module/tags/tag');

        // Apply for 'format' plugins optional paths at module level
        $this->add_plugin_structure('format', $module);

        // Apply for 'plagiarism' plugins optional paths at module level
        $this->add_plugin_structure('plagiarism', $module);

        // Apply for 'local' plugins optional paths at module level
        $this->add_plugin_structure('local', $module);

        // Apply for 'admin tool' plugins optional paths at module level.
        $this->add_plugin_structure('tool', $module);

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
                'section' => 0,
                'timemodified' => time());
            $DB->insert_record('course_sections', $sectionrec); // section 0
            $sectionrec = array(
                'course' => $this->get_courseid(),
                'section' => 1,
                'timemodified' => time());
            $data->section = $DB->insert_record('course_sections', $sectionrec); // section 1
        }
        $data->groupingid= $this->get_mappingid('grouping', $data->groupingid);      // grouping
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
            $data->availability = null;
        }
        // Backups that did not include showdescription, set it to default 0
        // (this is not totally necessary as it has a db default, but just to
        // be explicit).
        if (!isset($data->showdescription)) {
            $data->showdescription = 0;
        }
        $data->instance = 0; // Set to 0 for now, going to create it soon (next step)

        if (empty($data->availability)) {
            // If there are legacy availablility data fields (and no new format data),
            // convert the old fields.
            $data->availability = \core_availability\info::convert_legacy_fields(
                    $data, false);
        } else if (!empty($data->groupmembersonly)) {
            // There is current availability data, but it still has groupmembersonly
            // as well (2.7 backups), convert just that part.
            require_once($CFG->dirroot . '/lib/db/upgradelib.php');
            $data->availability = upgrade_group_members_only($data->groupingid, $data->availability);
        }

        // course_module record ready, insert it
        $newitemid = $DB->insert_record('course_modules', $data);
        // save mapping
        $this->set_mapping('course_module', $oldid, $newitemid);
        // set the new course_module id in the task
        $this->task->set_moduleid($newitemid);
        // we can now create the context safely
        $ctxid = context_module::instance($newitemid)->id;
        // set the new context id in the task
        $this->task->set_contextid($ctxid);
        // update sequence field in course_section
        if ($sequence = $DB->get_field('course_sections', 'sequence', array('id' => $data->section))) {
            $sequence .= ',' . $newitemid;
        } else {
            $sequence = $newitemid;
        }

        $updatesection = new \stdClass();
        $updatesection->id = $data->section;
        $updatesection->sequence = $sequence;
        $updatesection->timemodified = time();
        $DB->update_record('course_sections', $updatesection);

        // If there is the legacy showavailability data, store this for later use.
        // (This data is not present when restoring 'new' backups.)
        if (isset($data->showavailability)) {
            // Cache the showavailability flag using the backup_ids data field.
            restore_dbops::set_backup_ids_record($this->get_restoreid(),
                    'module_showavailability', $newitemid, 0, null,
                    (object)array('showavailability' => $data->showavailability));
        }
    }

    /**
     * Fetch all the existing because tag_set() deletes them
     * so everything must be reinserted on each call.
     *
     * @param stdClass $data Record data
     */
    protected function process_tag($data) {
        global $CFG;

        $data = (object)$data;

        if (core_tag_tag::is_enabled('core', 'course_modules')) {
            $modcontext = context::instance_by_id($this->task->get_contextid());
            $instanceid = $this->task->get_moduleid();

            core_tag_tag::add_item_tag('core', 'course_modules', $instanceid, $modcontext, $data->rawname);
        }
    }

    /**
     * Process the legacy availability table record. This table does not exist
     * in Moodle 2.7+ but we still support restore.
     *
     * @param stdClass $data Record data
     */
    protected function process_availability($data) {
        $data = (object)$data;
        // Simply going to store the whole availability record now, we'll process
        // all them later in the final task (once all activities have been restored)
        // Let's call the low level one to be able to store the whole object
        $data->coursemoduleid = $this->task->get_moduleid(); // Let add the availability cmid
        restore_dbops::set_backup_ids_record($this->get_restoreid(), 'module_availability', $data->id, 0, null, $data);
    }

    /**
     * Process the legacy availability fields table record. This table does not
     * exist in Moodle 2.7+ but we still support restore.
     *
     * @param stdClass $data Record data
     */
    protected function process_availability_field($data) {
        global $DB;
        $data = (object)$data;
        // Mark it is as passed by default
        $passed = true;
        $customfieldid = null;

        // If a customfield has been used in order to pass we must be able to match an existing
        // customfield by name (data->customfield) and type (data->customfieldtype)
        if (!empty($data->customfield) xor !empty($data->customfieldtype)) {
            // xor is sort of uncommon. If either customfield is null or customfieldtype is null BUT not both.
            // If one is null but the other isn't something clearly went wrong and we'll skip this condition.
            $passed = false;
        } else if (!empty($data->customfield)) {
            $params = array('shortname' => $data->customfield, 'datatype' => $data->customfieldtype);
            $customfieldid = $DB->get_field('user_info_field', 'id', $params);
            $passed = ($customfieldid !== false);
        }

        if ($passed) {
            // Create the object to insert into the database
            $availfield = new stdClass();
            $availfield->coursemoduleid = $this->task->get_moduleid(); // Lets add the availability cmid
            $availfield->userfield = $data->userfield;
            $availfield->customfieldid = $customfieldid;
            $availfield->operator = $data->operator;
            $availfield->value = $data->value;

            // Get showavailability option.
            $showrec = restore_dbops::get_backup_ids_record($this->get_restoreid(),
                    'module_showavailability', $availfield->coursemoduleid);
            if (!$showrec) {
                // Should not happen.
                throw new coding_exception('No matching showavailability record');
            }
            $show = $showrec->info->showavailability;

            // The $availfieldobject is now in the format used in the old
            // system. Interpret this and convert to new system.
            $currentvalue = $DB->get_field('course_modules', 'availability',
                    array('id' => $availfield->coursemoduleid), MUST_EXIST);
            $newvalue = \core_availability\info::add_legacy_availability_field_condition(
                    $currentvalue, $availfield, $show);
            $DB->set_field('course_modules', 'availability', $newvalue,
                    array('id' => $availfield->coursemoduleid));
        }
    }
    /**
     * This method will be executed after the rest of the restore has been processed.
     *
     * Update old tag instance itemid(s).
     */
    protected function after_restore() {
        global $DB;

        $contextid = $this->task->get_contextid();
        $instanceid = $this->task->get_activityid();
        $olditemid = $this->task->get_old_activityid();

        $DB->set_field('tag_instance', 'itemid', $instanceid, array('contextid' => $contextid, 'itemid' => $olditemid));
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

        // No completion on the front page.
        if ($this->get_courseid() == SITEID) {
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
 * Abstract structure step, parent of all the activity structure steps. Used to support
 * the main <activity ...> tag and process it.
 */
abstract class restore_activity_structure_step extends restore_structure_step {

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

    /** @var array $cachecategory store a question category */
    protected $cachedcategory = null;

    protected function define_structure() {

        $category = new restore_path_element('question_category', '/question_categories/question_category');
        $question = new restore_path_element('question', '/question_categories/question_category/questions/question');
        $hint = new restore_path_element('question_hint',
                '/question_categories/question_category/questions/question/question_hints/question_hint');

        $tag = new restore_path_element('tag','/question_categories/question_category/questions/question/tags/tag');

        // Apply for 'qtype' plugins optional paths at question level
        $this->add_plugin_structure('qtype', $question);

        // Apply for 'local' plugins optional paths at question level
        $this->add_plugin_structure('local', $question);

        return array($category, $question, $hint, $tag);
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
            // By performing this set_mapping() we make get_old/new_parentid() to work for all the
            // children elements of the 'question_category' one.
            $this->set_mapping('question_category', $oldid, $mapping->newitemid);
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

        // Before 3.5, question categories could be created at top level.
        // From 3.5 onwards, all question categories should be a child of a special category called the "top" category.
        $backuprelease = floatval($this->get_task()->get_info()->backup_release);
        preg_match('/(\d{8})/', $this->get_task()->get_info()->moodle_release, $matches);
        $backupbuild = (int)$matches[1];
        $before35 = false;
        if ($backuprelease < 3.5 || $backupbuild < 20180205) {
            $before35 = true;
        }
        if (empty($mapping->info->parent) && $before35) {
            $top = question_get_top_category($data->contextid, true);
            $data->parent = $top->id;
        }

        if (empty($data->parent)) {
            if (!$top = question_get_top_category($data->contextid)) {
                $top = question_get_top_category($data->contextid, true);
                $this->set_mapping('question_category_created', $oldid, $top->id, false, null, $data->contextid);
            }
            $this->set_mapping('question_category', $oldid, $top->id);
        } else {

            // Before 3.1, the 'stamp' field could be erroneously duplicated.
            // From 3.1 onwards, there's a unique index of (contextid, stamp).
            // If we encounter a duplicate in an old restore file, just generate a new stamp.
            // This is the same as what happens during an upgrade to 3.1+ anyway.
            if ($DB->record_exists('question_categories', ['stamp' => $data->stamp, 'contextid' => $data->contextid])) {
                $data->stamp = make_unique_id_code();
            }

            // The idnumber if it exists also needs to be unique within a context or reset it to null.
            if (!empty($data->idnumber) && $DB->record_exists('question_categories',
                    ['idnumber' => $data->idnumber, 'contextid' => $data->contextid])) {
                unset($data->idnumber);
            }

            // Let's create the question_category and save mapping.
            $newitemid = $DB->insert_record('question_categories', $data);
            $this->set_mapping('question_category', $oldid, $newitemid);
            // Also annotate them as question_category_created, we need
            // that later when remapping parents.
            $this->set_mapping('question_category_created', $oldid, $newitemid, false, null, $data->contextid);
        }
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

        $userid = $this->get_mappingid('user', $data->createdby);
        $data->createdby = $userid ? $userid : $this->task->get_userid();

        $userid = $this->get_mappingid('user', $data->modifiedby);
        $data->modifiedby = $userid ? $userid : $this->task->get_userid();

        // With newitemid = 0, let's create the question
        if (!$questionmapping->newitemid) {

            // The idnumber if it exists also needs to be unique within a category or reset it to null.
            if (!empty($data->idnumber) && $DB->record_exists('question',
                    ['idnumber' => $data->idnumber, 'category' => $data->category])) {
                unset($data->idnumber);
            }

            if ($data->qtype === 'random') {
                // Ensure that this newly created question is considered by
                // \qtype_random\task\remove_unused_questions.
                $data->hidden = 0;
            }

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

            // Not able to find the hint, let's try cleaning the hint text
            // of all the question's hints in DB as slower fallback. MDL-33863.
            if (!$newitemid) {
                $potentialhints = $DB->get_records('question_hints',
                        array('questionid' => $newquestionid), '', 'id, hint');
                foreach ($potentialhints as $potentialhint) {
                    // Clean in the same way than {@link xml_writer::xml_safe_utf8()}.
                    $cleanhint = preg_replace('/[\x-\x8\xb-\xc\xe-\x1f\x7f]/is','', $potentialhint->hint); // Clean CTRL chars.
                    $cleanhint = preg_replace("/\r\n|\r/", "\n", $cleanhint); // Normalize line ending.
                    if ($cleanhint === $data->hint) {
                        $newitemid = $data->id;
                    }
                }
            }

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

    protected function process_tag($data) {
        global $DB;

        $data = (object)$data;
        $newquestion = $this->get_new_parentid('question');
        $questioncreated = (bool) $this->get_mappingid('question_created', $this->get_old_parentid('question'));
        if (!$questioncreated) {
            // This question already exists in the question bank. Nothing for us to do.
            return;
        }

        if (core_tag_tag::is_enabled('core_question', 'question')) {
            $tagname = $data->rawname;
            if (!empty($data->contextid) && $newcontextid = $this->get_mappingid('context', $data->contextid)) {
                    $tagcontextid = $newcontextid;
            } else {
                // Get the category, so we can then later get the context.
                $categoryid = $this->get_new_parentid('question_category');
                if (empty($this->cachedcategory) || $this->cachedcategory->id != $categoryid) {
                    $this->cachedcategory = $DB->get_record('question_categories', array('id' => $categoryid));
                }
                $tagcontextid = $this->cachedcategory->contextid;
            }
            // Add the tag to the question.
            core_tag_tag::add_item_tag('core_question', 'question', $newquestion,
                    context::instance_by_id($tagcontextid),
                    $tagname);
        }
    }

    protected function after_execute() {
        global $DB;

        // First of all, recode all the created question_categories->parent fields
        $qcats = $DB->get_records('backup_ids_temp', array(
                     'backupid' => $this->get_restoreid(),
                     'itemname' => 'question_category_created'));
        foreach ($qcats as $qcat) {
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
            if (!$newparent && $dbcat->parent) {
                $topcat = question_get_top_category($dbcat->contextid, true);
                if ($dbcat->parent != $topcat->id) {
                    $DB->set_field('question_categories', 'parent', $topcat->id, array('id' => $dbcat->id));
                }
            }
        }

        // Now, recode all the created question->parent fields
        $qs = $DB->get_records('backup_ids_temp', array(
                  'backupid' => $this->get_restoreid(),
                  'itemname' => 'question_created'));
        foreach ($qs as $q) {
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

        $backuprelease = floatval($this->task->get_info()->backup_release);
        preg_match('/(\d{8})/', $this->task->get_info()->moodle_release, $matches);
        $backupbuild = (int)$matches[1];
        $after35 = false;
        if ($backuprelease >= 3.5 && $backupbuild > 20180205) {
            $after35 = true;
        }

        $contexts = restore_dbops::restore_get_question_banks($this->get_restoreid(), CONTEXT_MODULE);
        foreach ($contexts as $contextid => $contextlevel) {
            // Only if context mapping exists (i.e. the module has been restored)
            if ($newcontext = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'context', $contextid)) {
                // Update all the qcats having their parentitemid set to the original contextid
                $modulecats = $DB->get_records_sql("SELECT itemid, newitemid, info
                                                      FROM {backup_ids_temp}
                                                     WHERE backupid = ?
                                                       AND itemname = 'question_category'
                                                       AND parentitemid = ?", array($this->get_restoreid(), $contextid));
                $top = question_get_top_category($newcontext->newitemid, true);
                $oldtopid = 0;
                foreach ($modulecats as $modulecat) {
                    // Before 3.5, question categories could be created at top level.
                    // From 3.5 onwards, all question categories should be a child of a special category called the "top" category.
                    $info = backup_controller_dbops::decode_backup_temp_info($modulecat->info);
                    if ($after35 && empty($info->parent)) {
                        $oldtopid = $modulecat->newitemid;
                        $modulecat->newitemid = $top->id;
                    } else {
                        $cat = new stdClass();
                        $cat->id = $modulecat->newitemid;
                        $cat->contextid = $newcontext->newitemid;
                        if (empty($info->parent)) {
                            $cat->parent = $top->id;
                        }
                        $DB->update_record('question_categories', $cat);
                    }

                    // And set new contextid (and maybe update newitemid) also in question_category mapping (will be
                    // used by {@link restore_create_question_files} later.
                    restore_dbops::set_backup_ids_record($this->get_restoreid(), 'question_category', $modulecat->itemid,
                            $modulecat->newitemid, $newcontext->newitemid);
                }

                // Now set the parent id for the question categories that were in the top category in the course context
                // and have been moved now.
                if ($oldtopid) {
                    $DB->set_field('question_categories', 'parent', $top->id,
                            array('contextid' => $newcontext->newitemid, 'parent' => $oldtopid));
                }
            }
        }
    }
}

/**
 * Execution step that will create all the question/answers/qtype-specific files for the restored
 * questions. It must be executed after {@link restore_move_module_questions_categories}
 * because only then each question is in its final category and only then the
 * contexts can be determined.
 */
class restore_create_question_files extends restore_execution_step {

    /** @var array Question-type specific component items cache. */
    private $qtypecomponentscache = array();

    /**
     * Preform the restore_create_question_files step.
     */
    protected function define_execution() {
        global $DB;

        // Track progress, as this task can take a long time.
        $progress = $this->task->get_progress();
        $progress->start_progress($this->get_name(), \core\progress\base::INDETERMINATE);

        // Parentitemids of question_createds in backup_ids_temp are the category it is in.
        // MUST use a recordset, as there is no unique key in the first (or any) column.
        $catqtypes = $DB->get_recordset_sql("SELECT DISTINCT bi.parentitemid AS categoryid, q.qtype as qtype
                                               FROM {backup_ids_temp} bi
                                               JOIN {question} q ON q.id = bi.newitemid
                                              WHERE bi.backupid = ?
                                                AND bi.itemname = 'question_created'
                                           ORDER BY categoryid ASC", array($this->get_restoreid()));

        $currentcatid = -1;
        foreach ($catqtypes as $categoryid => $row) {
            $qtype = $row->qtype;

            // Check if we are in a new category.
            if ($currentcatid !== $categoryid) {
                // Report progress for each category.
                $progress->progress();

                if (!$qcatmapping = restore_dbops::get_backup_ids_record($this->get_restoreid(),
                        'question_category', $categoryid)) {
                    // Something went really wrong, cannot find the question_category for the question_created records.
                    debugging('Error fetching target context for question', DEBUG_DEVELOPER);
                    continue;
                }

                // Calculate source and target contexts.
                $oldctxid = $qcatmapping->info->contextid;
                $newctxid = $qcatmapping->parentitemid;

                $this->send_common_files($oldctxid, $newctxid, $progress);
                $currentcatid = $categoryid;
            }

            $this->send_qtype_files($qtype, $oldctxid, $newctxid, $progress);
        }
        $catqtypes->close();
        $progress->end_progress();
    }

    /**
     * Send the common question files to a new context.
     *
     * @param int             $oldctxid Old context id.
     * @param int             $newctxid New context id.
     * @param \core\progress  $progress Progress object to use.
     */
    private function send_common_files($oldctxid, $newctxid, $progress) {
        // Add common question files (question and question_answer ones).
        restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'questiontext',
                $oldctxid, $this->task->get_userid(), 'question_created', null, $newctxid, true, $progress);
        restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'generalfeedback',
                $oldctxid, $this->task->get_userid(), 'question_created', null, $newctxid, true, $progress);
        restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'answer',
                $oldctxid, $this->task->get_userid(), 'question_answer', null, $newctxid, true, $progress);
        restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'answerfeedback',
                $oldctxid, $this->task->get_userid(), 'question_answer', null, $newctxid, true, $progress);
        restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'hint',
                $oldctxid, $this->task->get_userid(), 'question_hint', null, $newctxid, true, $progress);
        restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'correctfeedback',
                $oldctxid, $this->task->get_userid(), 'question_created', null, $newctxid, true, $progress);
        restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'partiallycorrectfeedback',
                $oldctxid, $this->task->get_userid(), 'question_created', null, $newctxid, true, $progress);
        restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), 'question', 'incorrectfeedback',
                $oldctxid, $this->task->get_userid(), 'question_created', null, $newctxid, true, $progress);
    }

    /**
     * Send the question type specific files to a new context.
     *
     * @param text            $qtype The qtype name to send.
     * @param int             $oldctxid Old context id.
     * @param int             $newctxid New context id.
     * @param \core\progress  $progress Progress object to use.
     */
    private function send_qtype_files($qtype, $oldctxid, $newctxid, $progress) {
        if (!isset($this->qtypecomponentscache[$qtype])) {
            $this->qtypecomponentscache[$qtype] = backup_qtype_plugin::get_components_and_fileareas($qtype);
        }
        $components = $this->qtypecomponentscache[$qtype];
        foreach ($components as $component => $fileareas) {
            foreach ($fileareas as $filearea => $mapping) {
                restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), $component, $filearea,
                        $oldctxid, $this->task->get_userid(), $mapping, null, $newctxid, true, $progress);
            }
        }
    }
}

/**
 * Try to restore aliases and references to external files.
 *
 * The queue of these files was prepared for us in {@link restore_dbops::send_files_to_pool()}.
 * We expect that all regular (non-alias) files have already been restored. Make sure
 * there is no restore step executed after this one that would call send_files_to_pool() again.
 *
 * You may notice we have hardcoded support for Server files, Legacy course files
 * and user Private files here at the moment. This could be eventually replaced with a set of
 * callbacks in the future if needed.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_process_file_aliases_queue extends restore_execution_step {

    /** @var array internal cache for {@link choose_repository()} */
    private $cachereposbyid = array();

    /** @var array internal cache for {@link choose_repository()} */
    private $cachereposbytype = array();

    /**
     * What to do when this step is executed.
     */
    protected function define_execution() {
        global $DB;

        $this->log('processing file aliases queue', backup::LOG_DEBUG);

        $fs = get_file_storage();

        // Load the queue.
        $rs = $DB->get_recordset('backup_ids_temp',
            array('backupid' => $this->get_restoreid(), 'itemname' => 'file_aliases_queue'),
            '', 'info');

        // Iterate over aliases in the queue.
        foreach ($rs as $record) {
            $info = backup_controller_dbops::decode_backup_temp_info($record->info);

            // Try to pick a repository instance that should serve the alias.
            $repository = $this->choose_repository($info);

            if (is_null($repository)) {
                $this->notify_failure($info, 'unable to find a matching repository instance');
                continue;
            }

            if ($info->oldfile->repositorytype === 'local' or $info->oldfile->repositorytype === 'coursefiles') {
                // Aliases to Server files and Legacy course files may refer to a file
                // contained in the backup file or to some existing file (if we are on the
                // same site).
                try {
                    $reference = file_storage::unpack_reference($info->oldfile->reference);
                } catch (Exception $e) {
                    $this->notify_failure($info, 'invalid reference field format');
                    continue;
                }

                // Let's see if the referred source file was also included in the backup.
                $candidates = $DB->get_recordset('backup_files_temp', array(
                        'backupid' => $this->get_restoreid(),
                        'contextid' => $reference['contextid'],
                        'component' => $reference['component'],
                        'filearea' => $reference['filearea'],
                        'itemid' => $reference['itemid'],
                    ), '', 'info, newcontextid, newitemid');

                $source = null;

                foreach ($candidates as $candidate) {
                    $candidateinfo = backup_controller_dbops::decode_backup_temp_info($candidate->info);
                    if ($candidateinfo->filename === $reference['filename']
                            and $candidateinfo->filepath === $reference['filepath']
                            and !is_null($candidate->newcontextid)
                            and !is_null($candidate->newitemid) ) {
                        $source = $candidateinfo;
                        $source->contextid = $candidate->newcontextid;
                        $source->itemid = $candidate->newitemid;
                        break;
                    }
                }
                $candidates->close();

                if ($source) {
                    // We have an alias that refers to another file also included in
                    // the backup. Let us change the reference field so that it refers
                    // to the restored copy of the original file.
                    $reference = file_storage::pack_reference($source);

                    // Send the new alias to the filepool.
                    $fs->create_file_from_reference($info->newfile, $repository->id, $reference);
                    $this->notify_success($info);
                    continue;

                } else {
                    // This is a reference to some moodle file that was not contained in the backup
                    // file. If we are restoring to the same site, keep the reference untouched
                    // and restore the alias as is if the referenced file exists.
                    if ($this->task->is_samesite()) {
                        if ($fs->file_exists($reference['contextid'], $reference['component'], $reference['filearea'],
                                $reference['itemid'], $reference['filepath'], $reference['filename'])) {
                            $reference = file_storage::pack_reference($reference);
                            $fs->create_file_from_reference($info->newfile, $repository->id, $reference);
                            $this->notify_success($info);
                            continue;
                        } else {
                            $this->notify_failure($info, 'referenced file not found');
                            continue;
                        }

                    // If we are at other site, we can't restore this alias.
                    } else {
                        $this->notify_failure($info, 'referenced file not included');
                        continue;
                    }
                }

            } else if ($info->oldfile->repositorytype === 'user') {
                if ($this->task->is_samesite()) {
                    // For aliases to user Private files at the same site, we have a chance to check
                    // if the referenced file still exists.
                    try {
                        $reference = file_storage::unpack_reference($info->oldfile->reference);
                    } catch (Exception $e) {
                        $this->notify_failure($info, 'invalid reference field format');
                        continue;
                    }
                    if ($fs->file_exists($reference['contextid'], $reference['component'], $reference['filearea'],
                            $reference['itemid'], $reference['filepath'], $reference['filename'])) {
                        $reference = file_storage::pack_reference($reference);
                        $fs->create_file_from_reference($info->newfile, $repository->id, $reference);
                        $this->notify_success($info);
                        continue;
                    } else {
                        $this->notify_failure($info, 'referenced file not found');
                        continue;
                    }

                // If we are at other site, we can't restore this alias.
                } else {
                    $this->notify_failure($info, 'restoring at another site');
                    continue;
                }

            } else {
                // This is a reference to some external file such as in boxnet or dropbox.
                // If we are restoring to the same site, keep the reference untouched and
                // restore the alias as is.
                if ($this->task->is_samesite()) {
                    $fs->create_file_from_reference($info->newfile, $repository->id, $info->oldfile->reference);
                    $this->notify_success($info);
                    continue;

                // If we are at other site, we can't restore this alias.
                } else {
                    $this->notify_failure($info, 'restoring at another site');
                    continue;
                }
            }
        }
        $rs->close();
    }

    /**
     * Choose the repository instance that should handle the alias.
     *
     * At the same site, we can rely on repository instance id and we just
     * check it still exists. On other site, try to find matching Server files or
     * Legacy course files repository instance. Return null if no matching
     * repository instance can be found.
     *
     * @param stdClass $info
     * @return repository|null
     */
    private function choose_repository(stdClass $info) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/repository/lib.php');

        if ($this->task->is_samesite()) {
            // We can rely on repository instance id.

            if (array_key_exists($info->oldfile->repositoryid, $this->cachereposbyid)) {
                return $this->cachereposbyid[$info->oldfile->repositoryid];
            }

            $this->log('looking for repository instance by id', backup::LOG_DEBUG, $info->oldfile->repositoryid, 1);

            try {
                $this->cachereposbyid[$info->oldfile->repositoryid] = repository::get_repository_by_id($info->oldfile->repositoryid, SYSCONTEXTID);
                return $this->cachereposbyid[$info->oldfile->repositoryid];
            } catch (Exception $e) {
                $this->cachereposbyid[$info->oldfile->repositoryid] = null;
                return null;
            }

        } else {
            // We can rely on repository type only.

            if (empty($info->oldfile->repositorytype)) {
                return null;
            }

            if (array_key_exists($info->oldfile->repositorytype, $this->cachereposbytype)) {
                return $this->cachereposbytype[$info->oldfile->repositorytype];
            }

            $this->log('looking for repository instance by type', backup::LOG_DEBUG, $info->oldfile->repositorytype, 1);

            // Both Server files and Legacy course files repositories have a single
            // instance at the system context to use. Let us try to find it.
            if ($info->oldfile->repositorytype === 'local' or $info->oldfile->repositorytype === 'coursefiles') {
                $sql = "SELECT ri.id
                          FROM {repository} r
                          JOIN {repository_instances} ri ON ri.typeid = r.id
                         WHERE r.type = ? AND ri.contextid = ?";
                $ris = $DB->get_records_sql($sql, array($info->oldfile->repositorytype, SYSCONTEXTID));
                if (empty($ris)) {
                    return null;
                }
                $repoids = array_keys($ris);
                $repoid = reset($repoids);
                try {
                    $this->cachereposbytype[$info->oldfile->repositorytype] = repository::get_repository_by_id($repoid, SYSCONTEXTID);
                    return $this->cachereposbytype[$info->oldfile->repositorytype];
                } catch (Exception $e) {
                    $this->cachereposbytype[$info->oldfile->repositorytype] = null;
                    return null;
                }
            }

            $this->cachereposbytype[$info->oldfile->repositorytype] = null;
            return null;
        }
    }

    /**
     * Let the user know that the given alias was successfully restored
     *
     * @param stdClass $info
     */
    private function notify_success(stdClass $info) {
        $filedesc = $this->describe_alias($info);
        $this->log('successfully restored alias', backup::LOG_DEBUG, $filedesc, 1);
    }

    /**
     * Let the user know that the given alias can't be restored
     *
     * @param stdClass $info
     * @param string $reason detailed reason to be logged
     */
    private function notify_failure(stdClass $info, $reason = '') {
        $filedesc = $this->describe_alias($info);
        if ($reason) {
            $reason = ' ('.$reason.')';
        }
        $this->log('unable to restore alias'.$reason, backup::LOG_WARNING, $filedesc, 1);
        $this->add_result_item('file_aliases_restore_failures', $filedesc);
    }

    /**
     * Return a human readable description of the alias file
     *
     * @param stdClass $info
     * @return string
     */
    private function describe_alias(stdClass $info) {

        $filedesc = $this->expected_alias_location($info->newfile);

        if (!is_null($info->oldfile->source)) {
            $filedesc .= ' ('.$info->oldfile->source.')';
        }

        return $filedesc;
    }

    /**
     * Return the expected location of a file
     *
     * Please note this may and may not work as a part of URL to pluginfile.php
     * (depends on how the given component/filearea deals with the itemid).
     *
     * @param stdClass $filerecord
     * @return string
     */
    private function expected_alias_location($filerecord) {

        $filedesc = '/'.$filerecord->contextid.'/'.$filerecord->component.'/'.$filerecord->filearea;
        if (!is_null($filerecord->itemid)) {
            $filedesc .= '/'.$filerecord->itemid;
        }
        $filedesc .= $filerecord->filepath.$filerecord->filename;

        return $filedesc;
    }

    /**
     * Append a value to the given resultset
     *
     * @param string $name name of the result containing a list of values
     * @param mixed $value value to add as another item in that result
     */
    private function add_result_item($name, $value) {

        $results = $this->task->get_results();

        if (isset($results[$name])) {
            if (!is_array($results[$name])) {
                throw new coding_exception('Unable to append a result item into a non-array structure.');
            }
            $current = $results[$name];
            $current[] = $value;
            $this->task->add_result(array($name => $current));

        } else {
            $this->task->add_result(array($name => array($value)));
        }
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
     *
     * If you use the $nameprefix parameter, then you will need to implement some
     * extra methods in your class, like
     *
     * protected function process_{nameprefix}question_attempt($data) {
     *     $this->restore_question_usage_worker($data, '{nameprefix}');
     * }
     * protected function process_{nameprefix}question_attempt($data) {
     *     $this->restore_question_attempt_worker($data, '{nameprefix}');
     * }
     * protected function process_{nameprefix}question_attempt_step($data) {
     *     $this->restore_question_attempt_step_worker($data, '{nameprefix}');
     * }
     *
     * @param restore_path_element $element the parent element that the usages are stored inside.
     * @param array $paths the paths array that is being built.
     * @param string $nameprefix should match the prefix passed to the corresponding
     *      backup_questions_activity_structure_step::add_question_usages call.
     */
    protected function add_question_usages($element, &$paths, $nameprefix = '') {
        // Check $element is restore_path_element
        if (! $element instanceof restore_path_element) {
            throw new restore_step_exception('element_must_be_restore_path_element', $element);
        }

        // Check $paths is one array
        if (!is_array($paths)) {
            throw new restore_step_exception('paths_must_be_array', $paths);
        }
        $paths[] = new restore_path_element($nameprefix . 'question_usage',
                $element->get_path() . "/{$nameprefix}question_usage");
        $paths[] = new restore_path_element($nameprefix . 'question_attempt',
                $element->get_path() . "/{$nameprefix}question_usage/{$nameprefix}question_attempts/{$nameprefix}question_attempt");
        $paths[] = new restore_path_element($nameprefix . 'question_attempt_step',
                $element->get_path() . "/{$nameprefix}question_usage/{$nameprefix}question_attempts/{$nameprefix}question_attempt/{$nameprefix}steps/{$nameprefix}step",
                true);
        $paths[] = new restore_path_element($nameprefix . 'question_attempt_step_data',
                $element->get_path() . "/{$nameprefix}question_usage/{$nameprefix}question_attempts/{$nameprefix}question_attempt/{$nameprefix}steps/{$nameprefix}step/{$nameprefix}response/{$nameprefix}variable");
    }

    /**
     * Process question_usages
     */
    protected function process_question_usage($data) {
        $this->restore_question_usage_worker($data, '');
    }

    /**
     * Process question_attempts
     */
    protected function process_question_attempt($data) {
        $this->restore_question_attempt_worker($data, '');
    }

    /**
     * Process question_attempt_steps
     */
    protected function process_question_attempt_step($data) {
        $this->restore_question_attempt_step_worker($data, '');
    }

    /**
     * This method does the actual work for process_question_usage or
     * process_{nameprefix}_question_usage.
     * @param array $data the data from the XML file.
     * @param string $nameprefix the element name prefix.
     */
    protected function restore_question_usage_worker($data, $nameprefix) {
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

        $this->set_mapping($nameprefix . 'question_usage', $oldid, $newitemid, false);
    }

    /**
     * When process_question_usage creates the new usage, it calls this method
     * to let the activity link to the new usage. For example, the quiz uses
     * this method to set quiz_attempts.uniqueid to the new usage id.
     * @param integer $newusageid
     */
    abstract protected function inform_new_usage_id($newusageid);

    /**
     * This method does the actual work for process_question_attempt or
     * process_{nameprefix}_question_attempt.
     * @param array $data the data from the XML file.
     * @param string $nameprefix the element name prefix.
     */
    protected function restore_question_attempt_worker($data, $nameprefix) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $question = $this->get_mapping('question', $data->questionid);

        $data->questionusageid = $this->get_new_parentid($nameprefix . 'question_usage');
        $data->questionid      = $question->newitemid;
        if (!property_exists($data, 'variant')) {
            $data->variant = 1;
        }

        if (!property_exists($data, 'maxfraction')) {
            $data->maxfraction = 1;
        }

        $newitemid = $DB->insert_record('question_attempts', $data);

        $this->set_mapping($nameprefix . 'question_attempt', $oldid, $newitemid);
        $this->qtypes[$newitemid] = $question->info->qtype;
        $this->newquestionids[$newitemid] = $data->questionid;
    }

    /**
     * This method does the actual work for process_question_attempt_step or
     * process_{nameprefix}_question_attempt_step.
     * @param array $data the data from the XML file.
     * @param string $nameprefix the element name prefix.
     */
    protected function restore_question_attempt_step_worker($data, $nameprefix) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Pull out the response data.
        $response = array();
        if (!empty($data->{$nameprefix . 'response'}[$nameprefix . 'variable'])) {
            foreach ($data->{$nameprefix . 'response'}[$nameprefix . 'variable'] as $variable) {
                $response[$variable['name']] = $variable['value'];
            }
        }
        unset($data->response);

        $data->questionattemptid = $this->get_new_parentid($nameprefix . 'question_attempt');
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
     * @param object $data contains all the grouped attempt data to process.
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
        $upgrader->save_usage($quiz->preferredbehaviour, $data, $qas,
                 $this->questions_recode_layout($quiz->oldquestions));
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

/**
 * Restore completion defaults for each module type
 *
 * @package     core_backup
 * @copyright   2017 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_completion_defaults_structure_step extends restore_structure_step {
    /**
     * To conditionally decide if this step must be executed.
     */
    protected function execute_condition() {
        // No completion on the front page.
        if ($this->get_courseid() == SITEID) {
            return false;
        }

        // No default completion info found, don't execute.
        $fullpath = $this->task->get_taskbasepath();
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;
        if (!file_exists($fullpath)) {
            return false;
        }

        // Arrived here, execute the step.
        return true;
    }

    /**
     * Function that will return the structure to be processed by this restore_step.
     *
     * @return restore_path_element[]
     */
    protected function define_structure() {
        return [new restore_path_element('completion_defaults', '/course_completion_defaults/course_completion_default')];
    }

    /**
     * Processor for path element 'completion_defaults'
     *
     * @param stdClass|array $data
     */
    protected function process_completion_defaults($data) {
        global $DB;

        $data = (array)$data;
        $oldid = $data['id'];
        unset($data['id']);

        // Find the module by name since id may be different in another site.
        if (!$mod = $DB->get_record('modules', ['name' => $data['modulename']])) {
            return;
        }
        unset($data['modulename']);

        // Find the existing record.
        $newid = $DB->get_field('course_completion_defaults', 'id',
            ['course' => $this->task->get_courseid(), 'module' => $mod->id]);
        if (!$newid) {
            $newid = $DB->insert_record('course_completion_defaults',
                ['course' => $this->task->get_courseid(), 'module' => $mod->id] + $data);
        } else {
            $DB->update_record('course_completion_defaults', ['id' => $newid] + $data);
        }

        // Save id mapping for restoring associated events.
        $this->set_mapping('course_completion_defaults', $oldid, $newid);
    }
}

/**
 * Index course after restore.
 *
 * @package core_backup
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_course_search_index extends restore_execution_step {
    /**
     * When this step is executed, we add the course context to the queue for reindexing.
     */
    protected function define_execution() {
        $context = \context_course::instance($this->task->get_courseid());
        \core_search\manager::request_index($context);
    }
}

/**
 * Index activity after restore (when not restoring whole course).
 *
 * @package core_backup
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_activity_search_index extends restore_execution_step {
    /**
     * When this step is executed, we add the activity context to the queue for reindexing.
     */
    protected function define_execution() {
        $context = \context::instance_by_id($this->task->get_contextid());
        \core_search\manager::request_index($context);
    }
}

/**
 * Index block after restore (when not restoring whole course).
 *
 * @package core_backup
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_block_search_index extends restore_execution_step {
    /**
     * When this step is executed, we add the block context to the queue for reindexing.
     */
    protected function define_execution() {
        // A block in the restore list may be skipped because a duplicate is detected.
        // In this case, there is no new blockid (or context) to get.
        if (!empty($this->task->get_blockid())) {
            $context = \context_block::instance($this->task->get_blockid());
            \core_search\manager::request_index($context);
        }
    }
}

/**
 * Restore action events.
 *
 * @package     core_backup
 * @copyright   2017 onwards Ankit Agarwal
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_calendar_action_events extends restore_execution_step {
    /**
     * What to do when this step is executed.
     */
    protected function define_execution() {
        // We just queue the task here rather trying to recreate everything manually.
        // The task will automatically populate all data.
        $task = new \core\task\refresh_mod_calendar_events_task();
        $task->set_custom_data(array('courseid' => $this->get_courseid()));
        \core\task\manager::queue_adhoc_task($task, true);
    }
}
