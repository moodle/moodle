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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @author     Philip Cali, Robert Russo, Jason Peak, Troy Kammerdiener
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/cps/classes/lib.php');
require_once($CFG->dirroot . '/blocks/cps/classes/profile_field_helper.php');

abstract class blocks_cps_ues_handler {

    /**
     *
     * @global type $DB
     * @param stdClass $user previously, this has been of type ues_user
     * @see enrol_ues_plugin::create_user
     * @return boolean
     */
    public static function user_updated($user) {
        global $DB;

        $firstname = cps_setting::get(array(
            'userid' => $user->id,
            'name' => 'user_firstname'
        ));

        // No preference or firstname is the same as preference.
        if (empty($firstname) or $user->firstname == $firstname->value) {
            return true;
        }

        $user->firstname = $firstname->value;
        return $DB->update_record('user', $user);
    }

    /**
     * For users who have previously set their preferred name
     * and who have now had their name changed officially (so that
     * provider returns this name as firstname), delete the setting
     * for firstname.
     * @param type $user
     */
    public static function preferred_name_legitimized($user) {
        $params = array(
            'userid' => $user->id,
            'name' => 'user_firstname'
        );
        cps_setting::delete_all($params);
    }

    public static function ues_primary_change($data) {
        // Empty enrollment / idnumber.
        ues::unenroll_users(array($data->section));

        // Safe keeping.
        $data->section->idnumber = '';
        $data->section->status = ues::PROCESSED;
        $data->section->save();

        // Set to re-enroll.
        ues_student::reset_status($data->section, ues::PROCESSED);
        ues_teacher::reset_status($data->section, ues::PROCESSED);

        return $data;
    }

    public static function ues_teacher_process($uesteacher) {
        $threshold = get_config('block_cps', 'course_threshold');

        $course = $uesteacher->section()->course();

        // Must abide by the threshold.
        if ($course->cou_number >= $threshold) {
            $unwantparams = array(
                'userid' => $uesteacher->userid,
                'sectionid' => $uesteacher->sectionid
            );

            $unwant = cps_unwant::get($unwantparams);

            if (empty($unwant)) {
                $unwant = new cps_unwant();
                $unwant->fill_params($unwantparams);
                $unwant->save();
            }
        }

        return true;
    }

    public static function ues_teacher_release($uesteacher) {
        // Check for promotion or demotion.
        $params = array(
            'userid' => $uesteacher->userid,
            'sectionid' => $uesteacher->sectionid,
            'status' => ues::PROCESSED
        );

        $otherself = ues_teacher::get($params);

        if ($otherself) {
            $promotion = $otherself->primary_flag == 1;
            $demotion = $otherself->primary_flag == 0;
        } else {
            $promotion = $demotion = false;
        }

        $deleteparams = array('userid' => $uesteacher->userid);

        $allsectionsettings = array('unwant', 'split', 'crosslist');

        if ($promotion) {
            // Promotion means all settings are in tact.
            return $uesteacher;
        } else if ($demotion) {
            // Demotion means crosslist and split behavior must be effected.
            unset($allsectionsettings[0]);
        }

        $bysuccessfuldelete = function($in, $setting) use ($deleteparams, $uesteacher) {
            $class = 'cps_'.$setting;
            return $in && $class::delete_all($deleteparams + array(
                'sectionid' => $uesteacher->sectionid
            ));
        };

        $success = array_reduce($allsectionsettings, $bysuccessfuldelete, true);

        $creationparams = array(
            'courseid' => $uesteacher->section()->courseid,
            'semesterid' => $uesteacher->section()->semesterid
        );

        $success = (
            cps_creation::delete_all($deleteparams + $creationparams) and
            cps_team_request::delete_all($deleteparams + $creationparams) and
            $success
        );

        return $uesteacher;
    }

    public static function ues_section_process($section) {
        $semester = $section->semester();

        $primary = $section->primary();
        // TODO debug this: 1 why  use current ? is the choice of teacher arbitrary ?
        // We know a teacher exists for this course, so we'll use a non-primary.
        if (!$primary) {
            $primary = current($section->teachers());
        }

        // Unwanted interjection.
        $unwanted = cps_unwant::get(array(
            'userid' => $primary->userid,
            'sectionid' => $section->id
        ));

        if ($unwanted) {
            $section->status = ues::PENDING;
            return $section;
        }

        // Creation and Enrollment interjection.
        $creationparams = array(
            'userid' => $primary->userid,
            'semesterid' => $section->semesterid,
            'courseid' => $section->courseid
        );

        $creation = cps_creation::get($creationparams);
        if (!$creation) {
            $creation = new cps_creation();
            $creation->create_days = get_config('block_cps', 'create_days');
            $creation->enroll_days = get_config('block_cps', 'enroll_days');
        }

        $classesstart = $semester->classes_start;
        $diff = $classesstart - time();

        $diffdays = ($diff / 60 / 60 / 24);

        if ($diffdays > $creation->create_days) {
            $section->status = ues::PENDING;
            return $section;
        }

        if ($diffdays > $creation->enroll_days) {
            ues_student::reset_status($section, ues::PENDING, ues::PROCESSED);
        }

        foreach (array('split', 'crosslist', 'team_section') as $setting) {
            $class = 'cps_'.$setting;
            $applied = $class::get(array('sectionid' => $section->id));

            if ($applied) {
                $section->idnumber = $applied->new_idnumber();
            }
        }

        return $section;
    }

    public static function ues_section_drop($section) {
        $sectionsettings = array('unwant', 'split', 'crosslist', 'team_section');

        foreach ($sectionsettings as $settting) {
            $class = 'cps_' . $settting;

            $class::delete_all(array('sectionid' => $section->id));
        }

        return true;
    }

    public static function ues_semester_drop($semester) {
        $semestersettings = array('cps_creation', 'cps_team_request');

        foreach ($semestersettings as $class) {
            $class::delete_all(array('semesterid' => $semester->id));
        }

        return true;
    }

    /**
     * Manipulates the shortname and fullname of a split, crosslist, or team-teach
     * of a newly created course shell. It may be invoked either by a ues_course_created
     * event being triggered or by direct invocation (which passes a course object).
     * If it was invoked by the trigger, the course object is created by database lookup
     * from the courseid stored in the event object.
     *
     * @param $eventorcourse The event object or course object that invoked this handler.
     * @return               The modified course object.
     */
    public static function ues_course_created($eventorcourse) {
        global $DB;

        if ($eventorcourse instanceof \blocks_cps\event\ues_course_created) {
            $event = $eventorcourse;
            // Extract courseid from event object.  Event data is protected, so we must use the public accessor.
            $eventinfo = $event->get_data();
            $courseid = $eventinfo['courseid'];

            // TODO: The block below needs to do the standard 'Programming error detected...' output.
            if (!$courseid) {
                debugging("Course ID is null in ues_course_created event handler.");
                die("Course ID is null in ues_course_created event handler.");
            }

            $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        } else {
            $course = $eventorcourse;
        }
        $sections = ues_section::from_course($course);

        if (empty($sections)) {
            return $course;
        }

        $section = reset($sections);

        $primary = $section->primary();

        if (empty($primary)) {
            return $course;
        }

        $creationsettings = cps_setting::get_all(ues::where()
            ->userid->equal($primary->userid)
            ->name->starts_with('creation_')
        );

        $semester = $section->semester();
        $session = $semester->get_session_key();

        $uescourse = $section->course();

        $ownerparams = array(
            'userid' => $primary->userid,
            'sectionid' => $section->id
        );

        // Properly fold.
        $fullname = $course->fullname;
        $shortname = $course->shortname;

        $a = new stdClass;

        $split = cps_split::get($ownerparams);
        if ($split) {
            $a->year = $semester->year;
            $a->name = $semester->name;
            $a->session = $session;
            $a->department = $uescourse->department;
            $a->course_number = $uescourse->cou_number;
            $a->shell_name = $split->shell_name;
            $a->fullname = fullname($primary->user());

            $stringkey = 'split_shortname';
        }

        $crosslist = cps_crosslist::get($ownerparams);
        if ($crosslist) {
            $a->year = $semester->year;
            $a->name = $semester->name;
            $a->session = $session;
            $a->shell_name = $crosslist->shell_name;
            $a->fullname = fullname($primary->user());

            $stringkey = 'crosslist_shortname';
        }

        $teamteach = cps_team_section::get(array('sectionid' => $section->id));
        if ($teamteach) {
            $a->year = $semester->year;
            $a->name = $semester->name;
            $a->session = $session;
            $a->shell_name = $teamteach->shell_name;

            $stringkey = 'team_request_shortname';
        }

        if (isset($stringkey)) {
            $pattern = get_config('block_cps', $stringkey);

            $fullname = ues::format_string($pattern, $a);
            $shortname = ues::format_string($pattern, $a);
        }

        $course->fullname = $fullname;
        $course->shortname = $shortname;

        // Instructor overrides only on creation.
        if (empty($course->id)) {
            foreach ($creationsettings as $setting) {
                $key = str_replace('creation_', '', $setting->name);

                $course->$key = $setting->value;
            }
        }

        return $course;
    }

    public static function ues_course_severed($course) {
        // This event only occurs when a Moodle course will no longer be supported.
        // Good news is that the section that caused this severage will still be link to the idnumber until the end of the
        // unenrollment process. Should there be no grades, no activities, and no resourceswe can safely assume that
        // this course is no longer used.

        $performdelete = (bool) get_config('block_cps', 'course_severed');

        if (!$performdelete) {
            return true;
        }

        global $DB;

        $res = $DB->get_records('resource', array('course' => $course->id));

        $gradeitemsparams = array(
            'courseid' => $course->id,
            'itemtype' => 'course'
        );

        $ci = $DB->get_record('grade_items', $gradeitemsparams);

        $grades = function($ci) use ($DB) {
            if (empty($ci)) {
                return false;
            }

            $countparams = array('itemid' => $ci->id);
            $grades = $DB->count_records('grade_grades', $countparams);

            return !empty($grades);
        };

        if (empty($res) and !$grades($ci)) {
            delete_course($course, false);
            return true;
        }

        $sections = ues_section::from_course($course);

        if (empty($sections)) {
            return true;
        }

        $section = reset($sections);

        $primary = $section->primary();

        $byparams = array (
            'userid' => $primary->userid,
            'sectionid' => $section->id
        );

        if (cps_unwant::get($byparams)) {
            delete_course($course, false);
        }

        return true;
    }

    public static function ues_lsu_student_data_updated($user) {
        if (empty($user->user_keypadid)) {
            return blocks_cps_profile_field_helper::clear_field_data($user, 'user_keypadid');
        }

        return blocks_cps_profile_field_helper::process($user, 'user_keypadid');
    }

    public static function ues_azure_student_data_updated($user) {
        if (empty($user->user_keypadid)) {
            return blocks_cps_profile_field_helper::clear_field_data($user, 'user_keypadid');
        }

        return blocks_cps_profile_field_helper::process($user, 'user_keypadid');
    }

    // Accommodate the Generic XML provider.
    public static function ues_xml_student_data_updated($user) {
        // Todo: Refactor to actually use Event 2 rather than simply calling the handler directly.
        self::ues_lsu_student_data_updated($user);
    }

    public static function ues_azure_anonymous_updated($user) {
        if (empty($user->user_anonymous_number)) {
            return blocks_cps_profile_field_helper::clear_field_data($user, 'user_anonymous_number');
        }

        return blocks_cps_profile_field_helper::process($user, 'user_anonymous_number');
    }

    public static function ues_lsu_anonymous_updated($user) {
        if (empty($user->user_anonymous_number)) {
            return blocks_cps_profile_field_helper::clear_field_data($user, 'user_anonymous_number');
        }

        return blocks_cps_profile_field_helper::process($user, 'user_anonymous_number');
    }

    // Accommodate the Generic XML provider.
    public static function ues_xml_anonymous_updated($user) {
        mtrace(sprintf("xml_anon event triggered !"));
        // Todo: Refactor to actually use Event 2 rather than simply calling the handler directly.
        self::ues_lsu_anonymous_updated($user);
    }

    public static function ues_group_emptied($params) {
        return true;
    }
}
