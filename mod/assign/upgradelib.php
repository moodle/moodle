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
 * This file contains the upgrade code to upgrade from mod_assignment to mod_assign
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** Include locallib.php */
require_once($CFG->dirroot.'/mod/assign/locallib.php');
/** Include accesslib.php */
require_once($CFG->libdir.'/accesslib.php');

/**
 * The maximum amount of time to spend upgrading a single assignment.
 * This is intentionally generous (5 mins) as the effect of a timeout
 * for a legitimate upgrade would be quite harsh (roll back code will not run)
 */
define('ASSIGN_MAX_UPGRADE_TIME_SECS', 300);

/**
 * Class to manage upgrades from mod_assignment to mod_assign
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_upgrade_manager {

    /**
     * This function converts all of the base settings for an instance of
     * the old assignment to the new format. Then it calls each of the plugins
     * to see if they can help upgrade this assignment.
     * @param int $oldassignmentid (don't rely on the old assignment type even being installed)
     * @param string $log This string gets appended to during the conversion process
     * @return bool true or false
     */
    public function upgrade_assignment($oldassignmentid, & $log) {
        // steps to upgrade an assignment
        global $DB, $CFG, $USER;
        // steps to upgrade an assignment

        // is the user the admin? admin check goes here
        if (!is_siteadmin($USER->id)) {
              return false;
        }

        // should we use a shutdown handler to rollback on timeout?
        @set_time_limit(ASSIGN_MAX_UPGRADE_TIME_SECS);


        // get the module details
        $oldmodule = $DB->get_record('modules', array('name'=>'assignment'), '*', MUST_EXIST);
        $oldcoursemodule = $DB->get_record('course_modules', array('module'=>$oldmodule->id, 'instance'=>$oldassignmentid), '*', MUST_EXIST);
        $oldcontext = context_module::instance($oldcoursemodule->id);

        // first insert an assign instance to get the id
        $oldassignment = $DB->get_record('assignment', array('id'=>$oldassignmentid), '*', MUST_EXIST);

        $oldversion = get_config('assignment_' . $oldassignment->assignmenttype, 'version');

        $data = new stdClass();
        $data->course = $oldassignment->course;
        $data->name = $oldassignment->name;
        $data->intro = $oldassignment->intro;
        $data->introformat = $oldassignment->introformat;
        $data->alwaysshowdescription = 1;
        $data->sendnotifications = $oldassignment->emailteachers;
        $data->sendlatenotifications = $oldassignment->emailteachers;
        $data->duedate = $oldassignment->timedue;
        $data->allowsubmissionsfromdate = $oldassignment->timeavailable;
        $data->grade = $oldassignment->grade;
        $data->submissiondrafts = $oldassignment->resubmit;
        $data->requiresubmissionstatement = 0;
        $data->cutoffdate = 0;
        // New way to specify no late submissions.
        if ($oldassignment->preventlate) {
            $data->cutoffdate = $data->duedate;
        }
        $data->teamsubmission = 0;
        $data->requireallteammemberssubmit = 0;
        $data->teamsubmissiongroupingid = 0;
        $data->blindmarking = 0;

        $newassignment = new assign(null, null, null);

        if (!$newassignment->add_instance($data, false)) {
            $log = get_string('couldnotcreatenewassignmentinstance', 'mod_assign');
            return false;
        }

        // now create a new coursemodule from the old one
        $newmodule = $DB->get_record('modules', array('name'=>'assign'), '*', MUST_EXIST);
        $newcoursemodule = $this->duplicate_course_module($oldcoursemodule, $newmodule->id, $newassignment->get_instance()->id);
        if (!$newcoursemodule) {
            $log = get_string('couldnotcreatenewcoursemodule', 'mod_assign');
            return false;
        }

        // convert the base database tables (assignment, submission, grade)

        // these are used to store information in case a rollback is required
        $gradingarea = null;
        $gradingdefinitions = null;
        $gradeidmap = array();
        $completiondone = false;
        $gradesdone = false;

        // from this point we want to rollback on failure
        $rollback = false;
        try {
            $newassignment->set_context(context_module::instance($newcoursemodule->id));

            // the course module has now been created - time to update the core tables

            // copy intro files
            $newassignment->copy_area_files_for_upgrade($oldcontext->id, 'mod_assignment', 'intro', 0,
                                            $newassignment->get_context()->id, 'mod_assign', 'intro', 0);


            // get the plugins to do their bit
            foreach ($newassignment->get_submission_plugins() as $plugin) {
                if ($plugin->can_upgrade($oldassignment->assignmenttype, $oldversion)) {
                    $plugin->enable();
                    if (!$plugin->upgrade_settings($oldcontext, $oldassignment, $log)) {
                        $rollback = true;
                    }
                } else {
                    $plugin->disable();
                }
            }
            foreach ($newassignment->get_feedback_plugins() as $plugin) {
                if ($plugin->can_upgrade($oldassignment->assignmenttype, $oldversion)) {
                    $plugin->enable();
                    if (!$plugin->upgrade_settings($oldcontext, $oldassignment, $log)) {
                        $rollback = true;
                    }
                } else {
                    $plugin->disable();
                }
            }

            // see if there is advanced grading upgrades required
            $gradingarea = $DB->get_record('grading_areas', array('contextid'=>$oldcontext->id, 'areaname'=>'submission'), '*', IGNORE_MISSING);
            if ($gradingarea) {
                $DB->update_record('grading_areas', array('id'=>$gradingarea->id, 'contextid'=>$newassignment->get_context()->id, 'component'=>'mod_assign', 'areaname'=>'submissions'));
                $gradingdefinitions = $DB->get_records('grading_definitions', array('areaid'=>$gradingarea->id));
            }

            // upgrade completion data
            $DB->set_field('course_modules_completion', 'coursemoduleid', $newcoursemodule->id, array('coursemoduleid'=>$oldcoursemodule->id));
            $allcriteria = $DB->get_records('course_completion_criteria', array('moduleinstance'=>$oldcoursemodule->id));
            foreach ($allcriteria as $criteria) {
                $criteria->module = 'assign';
                $criteria->moduleinstance = $newcoursemodule->id;
                $DB->update_record('course_completion_criteria', $criteria);
            }
            $completiondone = true;

            // Migrate log entries so we don't lose them.
            $logparams = array('cmid' => $oldcoursemodule->id, 'course' => $oldcoursemodule->course);
            $DB->set_field('log', 'module', 'assign', $logparams);
            $DB->set_field('log', 'cmid', $newcoursemodule->id, $logparams);


            // copy all the submission data (and get plugins to do their bit)
            $oldsubmissions = $DB->get_records('assignment_submissions', array('assignment'=>$oldassignmentid));

            foreach ($oldsubmissions as $oldsubmission) {
                $submission = new stdClass();
                $submission->assignment = $newassignment->get_instance()->id;
                $submission->userid = $oldsubmission->userid;
                $submission->timecreated = $oldsubmission->timecreated;
                $submission->timemodified = $oldsubmission->timemodified;
                $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
                $submission->id = $DB->insert_record('assign_submission', $submission);
                if (!$submission->id) {
                    $log .= get_string('couldnotinsertsubmission', 'mod_assign', $submission->userid);
                    $rollback = true;
                }
                foreach ($newassignment->get_submission_plugins() as $plugin) {
                    if ($plugin->can_upgrade($oldassignment->assignmenttype, $oldversion)) {
                        if (!$plugin->upgrade($oldcontext, $oldassignment, $oldsubmission, $submission, $log)) {
                            $rollback = true;
                        }
                    }
                }
                if ($oldsubmission->timemarked) {
                    // submission has been graded - create a grade record
                    $grade = new stdClass();
                    $grade->assignment = $newassignment->get_instance()->id;
                    $grade->userid = $oldsubmission->userid;
                    $grade->grader = $oldsubmission->teacher;
                    $grade->timemodified = $oldsubmission->timemarked;
                    $grade->timecreated = $oldsubmission->timecreated;
                    // $grade->locked = $oldsubmission->locked;
                    $grade->grade = $oldsubmission->grade;
                    $grade->mailed = $oldsubmission->mailed;
                    $grade->id = $DB->insert_record('assign_grades', $grade);
                    if (!$grade->id) {
                        $log .= get_string('couldnotinsertgrade', 'mod_assign', $grade->userid);
                        $rollback = true;
                    }

                    // copy any grading instances
                    if ($gradingarea) {

                        $gradeidmap[$grade->id] = $oldsubmission->id;

                        foreach ($gradingdefinitions as $definition) {
                            $DB->set_field('grading_instances', 'itemid', $grade->id, array('definitionid'=>$definition->id, 'itemid'=>$oldsubmission->id));
                        }

                    }
                    foreach ($newassignment->get_feedback_plugins() as $plugin) {
                        if ($plugin->can_upgrade($oldassignment->assignmenttype, $oldversion)) {
                            if (!$plugin->upgrade($oldcontext, $oldassignment, $oldsubmission, $grade, $log)) {
                                $rollback = true;
                            }
                        }
                    }
                }
            }

            $newassignment->update_calendar($newcoursemodule->id);

            // Reassociate grade_items from the old assignment instance to the new assign instance.
            // This includes outcome linked grade_items.
            $params = array('assign', $newassignment->get_instance()->id, 'assignment', $oldassignment->id);
            $sql = 'UPDATE {grade_items} SET itemmodule = ?, iteminstance = ? WHERE itemmodule = ? AND iteminstance = ?';
            $DB->execute($sql, $params);

            $gradesdone = true;

        } catch (Exception $exception) {
            $rollback = true;
            $log .= get_string('conversionexception', 'mod_assign', $exception->error);
        }

        if ($rollback) {
            // roll back the grades changes
            if ($gradesdone) {
                // Reassociate grade_items from the new assign instance to the old assignment instance.
                $params = array('assignment', $oldassignment->id, 'assign', $newassignment->get_instance()->id);
                $sql = 'UPDATE {grade_items} SET itemmodule = ?, iteminstance = ? WHERE itemmodule = ? AND iteminstance = ?';
                $DB->execute($sql, $params);
            }
            // roll back the completion changes
            if ($completiondone) {
                $DB->set_field('course_modules_completion', 'coursemoduleid', $oldcoursemodule->id, array('coursemoduleid'=>$newcoursemodule->id));
                $allcriteria = $DB->get_records('course_completion_criteria', array('moduleinstance'=>$newcoursemodule->id));
                foreach ($allcriteria as $criteria) {
                    $criteria->module = 'assignment';
                    $criteria->moduleinstance = $oldcoursemodule->id;
                    $DB->update_record('course_completion_criteria', $criteria);
                }
            }
            // Roll back the log changes
            $logparams = array('cmid' => $newcoursemodule->id, 'course' => $newcoursemodule->course);
            $DB->set_field('log', 'module', 'assignment', $logparams);
            $DB->set_field('log', 'cmid', $oldcoursemodule->id, $logparams);
            // roll back the advanced grading update
            if ($gradingarea) {
                foreach ($gradeidmap as $newgradeid => $oldsubmissionid) {
                    foreach ($gradingdefinitions as $definition) {
                        $DB->set_field('grading_instances', 'itemid', $oldsubmissionid, array('definitionid'=>$definition->id, 'itemid'=>$newgradeid));
                    }
                }
                $DB->update_record('grading_areas', array('id'=>$gradingarea->id, 'contextid'=>$oldcontext->id, 'component'=>'mod_assignment', 'areaname'=>'submission'));
            }
            $newassignment->delete_instance();

            return false;
        }
        // all is well,
        // delete the old assignment (use object delete)
        $cm = get_coursemodule_from_id('', $oldcoursemodule->id, $oldcoursemodule->course);
        if ($cm) {
            $this->delete_course_module($cm);
        }
        rebuild_course_cache($oldcoursemodule->course);
        return true;
    }


    /**
     * Create a duplicate course module record so we can create the upgraded
     * assign module alongside the old assignment module.
     *
     * @param stdClass $cm The old course module record
     * @param int $moduleid The id of the new assign module
     * @param int $newinstanceid The id of the new instance of the assign module
     * @return mixed stdClass|bool The new course module record or FALSE
     */
    private function duplicate_course_module(stdClass $cm, $moduleid, $newinstanceid) {
        global $DB, $CFG;

        $newcm = new stdClass();
        $newcm->course           = $cm->course;
        $newcm->module           = $moduleid;
        $newcm->instance         = $newinstanceid;
        $newcm->visible          = $cm->visible;
        $newcm->section          = $cm->section;
        $newcm->score            = $cm->score;
        $newcm->indent           = $cm->indent;
        $newcm->groupmode        = $cm->groupmode;
        $newcm->groupingid       = $cm->groupingid;
        $newcm->groupmembersonly = $cm->groupmembersonly;
        $newcm->completion                = $cm->completion;
        $newcm->completiongradeitemnumber = $cm->completiongradeitemnumber;
        $newcm->completionview            = $cm->completionview;
        $newcm->completionexpected        = $cm->completionexpected;
        if(!empty($CFG->enableavailability)) {
            $newcm->availablefrom             = $cm->availablefrom;
            $newcm->availableuntil            = $cm->availableuntil;
            $newcm->showavailability          = $cm->showavailability;
        }
        $newcm->showdescription = $cm->showdescription;

        $newcmid = add_course_module($newcm);
        $newcm = get_coursemodule_from_id('', $newcmid, $cm->course);
        if (!$newcm) {
            return false;
        }
        $section = $DB->get_record("course_sections", array("id"=>$newcm->section));
        if (!$section) {
            return false;
        }

        $newcm->section = course_add_cm_to_section($newcm->course, $newcm->id, $section->section);

        // make sure visibility is set correctly (in particular in calendar)
        // note: allow them to set it even without moodle/course:activityvisibility
        set_coursemodule_visible($newcm->id, $newcm->visible);

        return $newcm;
    }

    /**
     * This function deletes the old assignment course module after
     * it has been upgraded. This code is adapted from "course/mod.php".
     *
     * @param stdClass $cm The course module to delete.
     * @return bool
     */
    private function delete_course_module($cm) {
        global $CFG, $USER, $DB, $OUTPUT;
        $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

        $coursecontext = context_course::instance($course->id);
        $modcontext = context_module::instance($cm->id);

        $modlib = "$CFG->dirroot/mod/$cm->modname/lib.php";

        if (file_exists($modlib)) {
            require_once($modlib);
        } else {
            print_error('modulemissingcode', '', '', $modlib);
        }

        $deleteinstancefunction = $cm->modname."_delete_instance";

        if (!$deleteinstancefunction($cm->instance)) {
            echo $OUTPUT->notification("Could not delete the $cm->modname (instance)");
        }

        // remove all module files in case modules forget to do that
        $fs = get_file_storage();
        $fs->delete_area_files($modcontext->id);

        if (!delete_course_module($cm->id)) {
            echo $OUTPUT->notification("Could not delete the $cm->modname (coursemodule)");
        }
        if (!delete_mod_from_section($cm->id, $cm->section)) {
            echo $OUTPUT->notification("Could not delete the $cm->modname from that section");
        }

        // Trigger a mod_deleted event with information about this module.
        $eventdata = new stdClass();
        $eventdata->modulename = $cm->modname;
        $eventdata->cmid       = $cm->id;
        $eventdata->courseid   = $course->id;
        $eventdata->userid     = $USER->id;
        events_trigger('mod_deleted', $eventdata);

        add_to_log($course->id, 'course', "delete mod",
                   "view.php?id=$cm->course",
                   "$cm->modname $cm->instance", $cm->id);

        return true;
    }

}
