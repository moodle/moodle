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
 * Scheduled task to run SDS sync.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\feature\sds\task;

use context_course;
use core\task\scheduled_task;
use core_course_category;
use Exception;
use local_o365\rest\unified;
use local_o365\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task to run SDS sync.
 */
class sync extends scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() : string {
        return get_string('task_sds_sync', 'local_o365');
    }

    /**
     * Run the sync process.
     *
     * @param unified $apiclient The unified API client.
     * @return bool
     */
    public static function runsync(unified $apiclient) : bool {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->libdir . '/accesslib.php');
        require_once($CFG->libdir . '/enrollib.php');

        // Profile data sync.
        static::mtrace('Running profile sync', 1);
        [$idandnamemappings, $additionalprofilemappings] = \local_o365\feature\sds\utils::get_sds_profile_sync_api_requirements();

        if ($idandnamemappings || $additionalprofilemappings) {
            static::mtrace('SDS fields exists in field mapping settings', 2);
            [$profilesyncenabled, $schoolid, $schoolname] = \local_o365\feature\sds\utils::get_profile_sync_status_with_id_name(
                $apiclient);

            if ($profilesyncenabled) {
                static::mtrace('SDS field mapping enabled and connected to school "' . $schoolname . '"', 2);
                $processedschoolusers = [];
                if ($additionalprofilemappings) {
                    static::mtrace('Additional SDS profile data required', 2);
                    $schooluserresults = $apiclient->get_school_users($schoolid);
                    $rawschoolusers = $schooluserresults['value'];
                    while (!empty($schooluserresults['@odata.nextLink'])) {
                        $nextlink = parse_url($schooluserresults['@odata.nextLink']);
                        $schooluserresults = [];
                        if (isset($nextlink['query'])) {
                            $query = [];
                            parse_str($nextlink['query'], $query);
                            if (isset($query['$skiptoken'])) {
                                $schooluserresults = $apiclient->get_school_users($schoolid, $query['$skiptoken']);
                                $rawschoolusers = array_merge($rawschoolusers, $schooluserresults['value']);
                            }
                        }
                    }

                    foreach ($rawschoolusers as $rawschooluser) {
                        if ($userobjectrecord = $DB->get_record('local_o365_objects', ['type' => 'user',
                            'objectid' => $rawschooluser['id']])) {
                            $processedschoolusers[$userobjectrecord->moodleid] = $rawschooluser;
                        }
                    }
                } else {
                    static::mtrace('Only basic SDS profile data required', 2);
                }

                $oidcusers = $DB->get_records('user', ['auth' => 'oidc', 'deleted' => 0]);
                foreach ($oidcusers as $userid => $oidcuser) {
                    $completeuser = get_complete_user_data('id', $userid);
                    if ($completeuser) {
                        static::mtrace('Processing user ' . $oidcuser->username, 3);
                        foreach ($idandnamemappings as $remotefield => $localfield) {
                            switch ($remotefield) {
                                case 'sds_school_id':
                                    $completeuser->$localfield = $schoolid;
                                    break;
                                case 'sds_school_name':
                                    $completeuser->$localfield = $schoolname;
                                    break;
                            }
                        }

                        if (array_key_exists($userid, $processedschoolusers)) {
                            static::mtrace('User profile found in SDS', 4);
                            $processedschooluser = $processedschoolusers[$userid];
                            $primaryrole = $processedschooluser['primaryRole'];
                            $studentexternalid = '';
                            $studentbirthdate = '';
                            $studentgrade = '';
                            $studentgraduationyear = '';
                            $studentstudentnumber = '';
                            $teacherexternalid = '';
                            $teacherteachernumber = '';
                            if ($primaryrole == 'student' && isset($processedschooluser['student'])) {
                                if (isset($processedschooluser['student']['externalId'])) {
                                    $studentexternalid = $processedschooluser['student']['externalId'];
                                }
                                if (isset($processedschooluser['student']['birthDate'])) {
                                    $studentbirthdate = $processedschooluser['student']['birthDate'];
                                }
                                if (isset($processedschooluser['student']['grade'])) {
                                    $studentgrade = $processedschooluser['student']['grade'];
                                }
                                if (isset($processedschooluser['student']['graduationYear'])) {
                                    $studentgraduationyear = $processedschooluser['student']['graduationYear'];
                                }
                                if (isset($processedschooluser['student']['studentNumber'])) {
                                    $studentstudentnumber = $processedschooluser['student']['studentNumber'];
                                }
                            }
                            if ($primaryrole == 'teacher' && isset($processedschooluser['teacher'])) {
                                if (isset($processedschooluser['teacher']['externalId'])) {
                                    $teacherexternalid = $processedschooluser['teacher']['externalId'];
                                }
                                if (isset($processedschooluser['teacher']['teacherNumber'])) {
                                    $teacherteachernumber = $processedschooluser['teacher']['teacherNumber'];
                                }
                            }

                            foreach ($additionalprofilemappings as $remotefield => $localfield) {
                                switch ($remotefield) {
                                    case 'sds_school_role':
                                        $completeuser->$localfield = $primaryrole;
                                        break;
                                    case 'sds_student_externalId':
                                        $completeuser->$localfield = $studentexternalid;
                                        break;
                                    case 'sds_student_birthDate':
                                        $completeuser->$localfield = $studentbirthdate;
                                        break;
                                    case 'sds_student_grade':
                                        $completeuser->$localfield = $studentgrade;
                                        break;
                                    case 'sds_student_graduationYear':
                                        $completeuser->$localfield = $studentgraduationyear;
                                        break;
                                    case 'sds_student_studentNumber':
                                        $completeuser->$localfield = $studentstudentnumber;
                                        break;
                                    case 'sds_teacher_externalId':
                                        $completeuser->$localfield = $teacherexternalid;
                                        break;
                                    case 'sds_teacher_teacherNumber':
                                        $completeuser->$localfield = $teacherteachernumber;
                                        break;
                                }
                            }

                            // Save user profile.
                            user_update_user($completeuser, false, false);
                            profile_save_data($completeuser);

                            static::mtrace('User profile updated', 4);
                        } else {
                            static::mtrace('User profile not found in SDS', 4);
                        }
                    }
                }
            } else {
                static::mtrace('SDS field mapping disabled', 2);
            }
        } else {
            static::mtrace('SDS fields not used in field map settings', 2);
        }

        // Course sync.
        static::mtrace('Running course sync', 1);
        $schoolobjectids = get_config('local_o365', 'sdsschools');
        $sdscoursesyncenabled = true;
        $sdscoursesyncroleconfigured = true;
        if (!$schoolobjectids) {
            $sdscoursesyncenabled = false;
        } else {
            $schoolobjectids = explode(',', $schoolobjectids);
            if (!$schoolobjectids) {
                $sdscoursesyncenabled = false;
            } else {
                $enrolenabled = get_config('local_o365', 'sdsenrolmentenabled');
                $teamsyncenabled = get_config('local_o365', 'sdsteamsenabled');

                if ($enrolenabled) {
                    // Get role records.
                    $studentrole = null;
                    $studentroleid = get_config('local_o365', 'sdsenrolmentstudentrole');
                    if ($studentroleid) {
                        $studentrole = $DB->get_record('role', ['id' => $studentroleid], '*', IGNORE_MISSING);
                    }
                    if (empty($studentrole)) {
                        $sdscoursesyncroleconfigured = false;
                    }

                    $teacherrole = null;
                    $teacherroleid = get_config('local_o365', 'sdsenrolmentteacherrole');
                    if ($teacherroleid) {
                        $teacherrole = $DB->get_record('role', ['id' => $teacherroleid], '*', IGNORE_MISSING);
                    }
                    if (empty($teacherrole)) {
                        $sdscoursesyncroleconfigured = false;
                    }
                }
            }
        }

        if (!$sdscoursesyncenabled) {
            static::mtrace('SDS course sync disabled', 2);
            return true;
        } else if ($enrolenabled && !$sdscoursesyncroleconfigured) {
            static::mtrace('SDS course sync enabled, but role mapping not configured', 2);
            return true;
        }

        $syncedschools = [];
        $schoolresults = $apiclient->get_schools();
        $schools = $schoolresults['value'];
        while (!empty($schoolresults['@odata.nextLink'])) {
            $nextlink = parse_url($schoolresults['@odata.nextLink']);
            $schoolresults = [];
            if (isset($nextlink['query'])) {
                $query = [];
                parse_str($nextlink['query'], $query);
                if (isset($query['$skiptoken'])) {
                    $schoolresults = $apiclient->get_schools($query['$skiptoken']);
                    $schools = array_merge($schools, $schoolresults['value']);
                }
            }
        }

        foreach ($schools as $school) {
            if (in_array($school['id'], $schoolobjectids)) {
                $syncedschools[$school['id']] = $school;
            }
        }

        foreach ($syncedschools as $schoolid => $syncedschool) {
            $coursecat = static::get_or_create_school_coursecategory($syncedschool['id'], $syncedschool['displayName']);
            static::mtrace('Processing school ' . $syncedschool['displayName'], 2);

            $schoolclassresults = $apiclient->get_school_classes($schoolid);
            $schoolclasses = $schoolclassresults['value'];
            while (!empty($schoolclassresults['@odata.nextLink'])) {
                $nextlink = parse_url($schoolclassresults['@odata.nextLink']);
                $schoolclassresults = [];
                if (isset($nextlink['query'])) {
                    $query = [];
                    parse_str($nextlink['query'], $query);
                    if (isset($query['$skiptoken'])) {
                        $schoolclassresults = $apiclient->get_school_classes($schoolid, $query['$skiptoken']);
                        $schoolclasses = array_merge($schoolclasses, $schoolclassresults['value']);
                    }
                }
            }

            foreach ($schoolclasses as $schoolclass) {
                static::mtrace('Processing school section ' . $schoolclass['displayName'], 3);

                // Create the course.
                $course = static::get_or_create_class_course($schoolclass['id'], $schoolclass['mailNickname'],
                    $schoolclass['displayName'], $coursecat->id);
                $coursecontext = context_course::instance($course->id);

                // Associate the section group with the course.
                $groupobjectparams = ['type' => 'group', 'subtype' => 'course', 'objectid' => $schoolclass['id'],
                    'moodleid' => $course->id];
                $groupobjectrec = $DB->get_record('local_o365_objects', $groupobjectparams);

                if (empty($groupobjectrec)) {
                    $now = time();
                    $groupobjectrec = $groupobjectparams;
                    $groupobjectrec['o365name'] = $schoolclass['displayName'];
                    $groupobjectrec['timecreated'] = $now;
                    $groupobjectrec['timemodified'] = $now;
                    $groupobjectrec['id'] = $DB->insert_record('local_o365_objects', (object) $groupobjectrec);
                    \local_o365\feature\coursesync\utils::set_course_sync_enabled($course->id);

                    if ($teamsyncenabled) {
                        $teamobjectrec = ['type' => 'group', 'subtype' => 'teamfromgroup', 'objectid' => $schoolclass['id'],
                            'moodleid' => $course->id];
                        $teamobjectrec['o365name'] = $schoolclass['displayName'];
                        $teamobjectrec['timecreated'] = $now;
                        $teamobjectrec['timemodified'] = $now;
                        $teamobjectrec['id'] = $DB->insert_record('local_o365_objects', (object) $teamobjectrec);
                    }
                }

                // Sync enrolments.
                if (!empty($enrolenabled)) {
                    static::mtrace('Running enrol sync', 4);

                    $classuserids = [];

                    // Sync teachers.
                    $existingteacherids = [];
                    if (get_config('local_o365', 'sdssyncenrolmenttosds')) {
                        $existingteacherroleassignments = get_users_from_role_on_context($teacherrole, $coursecontext);
                        foreach ($existingteacherroleassignments as $roleassignment) {
                            $existingteacherids[] = $roleassignment->userid;
                        }
                    }

                    $teachersobjectids = [];
                    $classteacherresults = $apiclient->get_school_class_teachers($schoolclass['id']);
                    $classteachers = $classteacherresults['value'];
                    while (!empty($classteacherresults['@odata.nextLink'])) {
                        $nextlink = parse_url($classteacherresults['@odata.nextLink']);
                        $classteacherresults = [];
                        if (isset($nextlink['query'])) {
                            $query = [];
                            parse_str($nextlink['query'], $query);
                            if (isset($query['$skiptoken'])) {
                                $classteacherresults = $apiclient->get_school_class_teachers($schoolclass['id'],
                                    $query['$skiptoken']);
                                $classteachers = array_merge($classteachers, $classteacherresults['value']);
                            }
                        }
                    }

                    foreach ($classteachers as $classteacher) {
                        $classuserids[] = $classteacher['id'];
                        $objectrec = $DB->get_record('local_o365_objects', ['type' => 'user', 'objectid' => $classteacher['id']]);
                        if (!empty($objectrec)) {
                            if (get_config('local_o365', 'sdssyncenrolmenttosds')) {
                                if (($key = array_search($objectrec->moodleid, $existingteacherids)) !== false) {
                                    unset($existingteacherids[$key]);
                                }
                            }

                            $teachersobjectids[] = $classteacher['id'];
                            $role = $teacherrole;

                            $roleparams = ['roleid' => $role->id, 'contextid' => $coursecontext->id,
                                'userid' => $objectrec->moodleid, 'component' => '', 'itemid' => 0];
                            if (!$DB->record_exists('role_assignments', $roleparams)) {
                                static::mtrace('Enrolling user ' . $objectrec->moodleid . ' into course ' . $course->id, 5);
                                enrol_try_internal_enrol($course->id, $objectrec->moodleid, $role->id);
                            }
                        }
                    }

                    if (get_config('local_o365', 'sdssyncenrolmenttosds')) {
                        foreach ($existingteacherids as $existingteacherid) {
                            static::mtrace('Unassign class teacher role from user ' . $existingteacherid . ' in course ' .
                                $course->id, 5);
                            role_unassign($teacherroleid, $existingteacherid, $coursecontext->id);
                        }
                    }

                    // Sync members.
                    $existingstudentids = [];
                    if (get_config('local_o365', 'sdssyncenrolmenttosds')) {
                        $existingstudentroleassignments = get_users_from_role_on_context($studentrole, $coursecontext);
                        foreach ($existingstudentroleassignments as $roleassignment) {
                            $existingstudentids[] = $roleassignment->userid;
                        }
                    }

                    $classmemberresults = $apiclient->get_school_class_members($schoolclass['id']);
                    $classmembers = $classmemberresults['value'];
                    while (!empty($classmemberresults['@odata.nextLink'])) {
                        $nextlink = parse_url($classmemberresults['@odata.nextLink']);
                        $classmemberresults = [];
                        if (isset($nextlink['query'])) {
                            $query = [];
                            parse_str($nextlink['query'], $query);
                            if (isset($query['$skiptoken'])) {
                                $classmemberresults = $apiclient->get_school_class_members($schoolclass['id'],
                                    $query['$skiptoken']);
                                $classmembers = array_merge($classmembers, $classmemberresults['value']);
                            }
                        }
                    }

                    foreach ($classmembers as $classmember) {
                        if (!in_array($classmember['id'], $teachersobjectids)) {
                            $classuserids[] = $classmember['id'];
                        }
                        $objectrec = $DB->get_record('local_o365_objects', ['type' => 'user', 'objectid' => $classmember['id']]);
                        if (!empty($objectrec)) {
                            if (get_config('local_o365', 'sdssyncenrolmenttosds')) {
                                if (($key = array_search($objectrec->moodleid, $existingstudentids)) !== false) {
                                    unset($existingstudentids[$key]);
                                }
                            }

                            $role = $studentrole;

                            $roleparams = ['roleid' => $role->id, 'contextid' => $coursecontext->id,
                                'userid' => $objectrec->moodleid, 'component' => '', 'itemid' => 0];
                            if (!$DB->record_exists('role_assignments', $roleparams)) {
                                static::mtrace('Enrolling user ' . $objectrec->moodleid . ' into course ' . $course->id, 5);
                                enrol_try_internal_enrol($course->id, $objectrec->moodleid, $role->id);
                            }
                        }
                    }

                    if (get_config('local_o365', 'sdssyncenrolmenttosds')) {
                        foreach ($existingstudentids as $existingstudentid) {
                            static::mtrace('Unassign class member role from user ' . $existingstudentid . ' in course ' .
                                $course->id, 5);
                            role_unassign($studentroleid, $existingstudentid, $coursecontext->id);
                        }
                    }

                    // Unenrol users who have been removed from the SDS class.
                    $enrolledusers = get_enrolled_users($coursecontext);
                    if (!$enrolledusers) {
                        continue;
                    }
                    [$moodleuseridsql, $params] = $DB->get_in_or_equal(array_keys($enrolledusers), SQL_PARAMS_NAMED);
                    $sql = 'SELECT objectid, moodleid AS userid
                              FROM {local_o365_objects}
                             WHERE type = :usertype
                               AND moodleid ' . $moodleuseridsql;
                    $params = array_merge($params, ['usertype' => 'user']);
                    $courseuserobjectids = $DB->get_records_sql($sql, $params);
                    $userstoberemoved = array_diff(array_keys($courseuserobjectids), $classuserids);
                    $enrols = [];
                    if ($userstoberemoved) {
                        $enrols = $DB->get_records('enrol', ['courseid' => $course->id]);
                    }
                    if ($enrols) {
                        [$enrolsql, $enrolparams] = $DB->get_in_or_equal(array_keys($enrols), SQL_PARAMS_NAMED);
                        foreach ($userstoberemoved as $userobjectid) {
                            $userid = $courseuserobjectids[$userobjectid]->userid;
                            static::mtrace('Unenrol user '. $userid . ' from course ' . $course->id, 5);
                            $sql = 'SELECT *
                                      FROM {user_enrolments}
                                     WHERE userid = :userid
                                       AND enrolid ' . $enrolsql;
                            $userenrolments = $DB->get_records_sql($sql, array_merge($enrolparams, ['userid' => $userid]));
                            foreach ($userenrolments as $userenrolment) {
                                if (isset($enrols[$userenrolment->enrolid])) {
                                    $enrolplugin = enrol_get_plugin($enrols[$userenrolment->enrolid]->enrol);
                                    $enrolplugin->unenrol_user($enrols[$userenrolment->enrolid], $userid);
                                }
                            }
                        }
                    }
                } else {
                    static::mtrace('Enrol sync disabled', 4);
                }
            }
        }

        // Clean up SDS sync records.
        static::clean_up_sds_sync_records();

        return true;
    }

    /**
     * Run mtrace if not in a unit test.
     *
     * @param string $str The trace string.
     * @param int $level
     */
    public static function mtrace(string $str, int $level = 0) {
        if (!PHPUNIT_TEST && !defined('BEHAT_SITE_RUNNING')) {
            $str = str_repeat('...', $level) . ' ' . $str;
            mtrace($str);
        }
    }

    /**
     * Retrieve or create a course for a class.
     *
     * @param string $classobjectid The object ID of the class.
     * @param string $shortname The shortname of the class course.
     * @param string $fullname The full name of the class course.
     * @param int $categoryid The ID of the category to create the course in (if necessary).
     * @return object The course object.
     */
    public static function get_or_create_class_course(string $classobjectid, string $shortname, string $fullname,
        int $categoryid = 0) : object {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        // Look for existing category.
        $params = ['type' => 'sdssection', 'subtype' => 'course', 'objectid' => $classobjectid];
        $objectrec = $DB->get_record('local_o365_objects', $params);
        if (!empty($objectrec)) {
            $course = $DB->get_record('course', ['id' => $objectrec->moodleid]);
            if (!empty($course)) {
                return $course;
            } else {
                // Course was deleted, remove object record and recreate.
                $DB->delete_records('local_o365_objects', ['id' => $objectrec->id]);
                $objectrec = null;
            }
        }

        // Create new course category and object record.
        $data = ['category' => $categoryid, 'shortname' => $shortname, 'fullname' => $fullname, 'idnumber' => $classobjectid,];
        $course = create_course((object) $data);

        $now = time();
        $objectrec = ['type' => 'sdssection', 'subtype' => 'course', 'objectid' => $classobjectid, 'moodleid' => $course->id,
            'o365name' => $shortname, 'tenant' => '', 'timecreated' => $now, 'timemodified' => $now,];
        $DB->insert_record('local_o365_objects', $objectrec);

        return $course;
    }

    /**
     * Get or create the course category for a school.
     *
     * @param string $schoolobjectid The Microsoft 365 object ID of the school.
     * @param string $schoolname The name of the school.
     * @return core_course_category A course category object for the retrieved or created course category.
     */
    public static function get_or_create_school_coursecategory(string $schoolobjectid, string $schoolname) : core_course_category {
        global $DB;

        // Look for existing category.
        $params = ['type' => 'sdsschool', 'subtype' => 'coursecat', 'objectid' => $schoolobjectid];
        $existingobject = $DB->get_record('local_o365_objects', $params);
        if (!empty($existingobject)) {
            $coursecat = core_course_category::get($existingobject->moodleid, IGNORE_MISSING, true);
            if (!empty($coursecat)) {
                return $coursecat;
            } else {
                // Course category was deleted, remove object record and recreate.
                $DB->delete_records('local_o365_objects', ['id' => $existingobject->id]);
                $existingobject = null;
            }
        }

        // Create new course category and object record.
        $data = ['visible' => 1, 'name' => $schoolname, 'idnumber' => $schoolobjectid,];
        if (strlen($data['name']) > 255) {
            static::mtrace('School name was over 255 chars when creating course category, truncating to 255.');
            $data['name'] = substr($data['name'], 0, 255);
        }

        $coursecat = core_course_category::create($data);

        $now = time();
        $objectrec = ['type' => 'sdsschool', 'subtype' => 'coursecat', 'objectid' => $schoolobjectid, 'moodleid' => $coursecat->id,
            'o365name' => $schoolname, 'tenant' => '', 'timecreated' => $now, 'timemodified' => $now,];
        $DB->insert_record('local_o365_objects', $objectrec);

        return $coursecat;
    }

    /**
     * Do the job.
     *
     * @return bool
     */
    public function execute() : bool {
        if (utils::is_connected() !== true) {
            static::mtrace('local_o365 reported unconfigured during SDS sync task, so exiting.');
            return false;
        }

        $apiclient = \local_o365\feature\sds\utils::get_apiclient();
        if (!empty($apiclient)) {
            return static::runsync($apiclient);
        } else {
            static::mtrace('Could not construct API client for SDS task, so exiting.');
            return false;
        }
    }

    /**
     * Clean up SDS sync records.
     * This will delete records of synced schools and sections when they are disabled.
     *
     * @return void
     */
    public static function clean_up_sds_sync_records() {
        global $DB;

        static::mtrace('Running course sync cleanup', 1);

        // Get existing synced records.
        $syncedsdsschools = $DB->get_records('local_o365_objects', ['type' => 'sdsschool']);
        $syncedsdssections = $DB->get_records('local_o365_objects', ['type' => 'sdssection']);

        // Clean up schools.
        $enabledschools = get_config('local_o365', 'sdsschools');
        $enabledschools = explode(',', $enabledschools);
        foreach ($syncedsdsschools as $syncedsdsschool) {
            if (!in_array($syncedsdsschool->objectid, $enabledschools)) {
                static::mtrace('Deleting SDS sync record for school ' . $syncedsdsschool->o365name . ' (' .
                    $syncedsdsschool->objectid . ')', 2);
                $DB->delete_records('local_o365_objects', ['id' => $syncedsdsschool->id]);
            }
        }

        // Clean up school sections.
        $sectionsinenabledschools = [];
        $sectionsinenabledschoolids = [];
        $apiclient = \local_o365\feature\sds\utils::get_apiclient();
        if (!$apiclient) {
            return;
        }
        foreach ($enabledschools as $schoolobjectid) {
            try {
                $schoolclassresults = $apiclient->get_school_classes($schoolobjectid);
                $schoolclasses = $schoolclassresults['value'];
                while (!empty($schoolclassresults['@odata.nextLink'])) {
                    $nextlink = parse_url($schoolclassresults['@odata.nextLink']);
                    $schoolclassresults = [];
                    if (isset($nextlink['query'])) {
                        $query = [];
                        parse_str($nextlink['query'], $query);
                        if (isset($query['$skiptoken'])) {
                            $schoolclassresults = $apiclient->get_school_classes($schoolobjectid, $query['$skiptoken']);
                            $schoolclasses = array_merge($schoolclasses, $schoolclassresults['value']);
                        }
                    }
                }
                $sectionsinenabledschools = array_merge($sectionsinenabledschools, $schoolclasses);
            } catch (Exception $e) {
                // Do nothing.
            }
        }
        foreach ($sectionsinenabledschools as $sectionsinenabledschool) {
            $sectionsinenabledschoolids[] = $sectionsinenabledschool['id'];
        }
        $sdsschoolsyncdisabledaction = get_config('local_o365', 'sdsschooldisabledaction');
        if (!$sdsschoolsyncdisabledaction) {
            $sdsschoolsyncdisabledaction = SDS_SCHOOL_DISABLED_ACTION_KEEP_CONNECTED;
        }
        foreach ($syncedsdssections as $syncedsdssection) {
            if (!in_array($syncedsdssection->objectid, $sectionsinenabledschoolids)) {
                static::mtrace('Deleting SDS sync record for school section ' . $syncedsdssection->o365name . ' (' .
                    $syncedsdssection->objectid . ')', 2);
                $DB->delete_records('local_o365_objects', ['id' => $syncedsdssection->id]);
                if ($sdsschoolsyncdisabledaction == SDS_SCHOOL_DISABLED_ACTION_DISCONNECT) {
                    static::mtrace('Deleting course sync record for school section ' . $syncedsdssection->o365name .
                        ' (' . $syncedsdssection->objectid . ')', 2);
                    $DB->delete_records('local_o365_objects', ['moodleid' => $syncedsdssection->moodleid, 'type' => 'group']);
                }
            }
        }
    }
}
