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
 * @package    enrol_studentstaff
 * @copyright  2023 onwards LSUOnline & Continuing Education
 * @copyright  2023 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class studentstaff {

    /**
     * Get the course from the custom $courseobj provided.
     *
     * @param object $courseobj
     * @return object $course
     */
    public static function get_studentstaff_course($courseobj) {
        global $DB;

        // Set this up for getting the course.
        $ctable = 'course';
        $cconditions = array('id' => $courseobj->courseid);

        // Get the course object.
        $course = $DB->get_record($ctable, $cconditions);

        // Return the Moodle course object.
        return $course;
    }

    /**
     * Get the role object.
     *
     * @return object $ssrole
     */
    public static function get_studentstaff_role() {
        global $DB;

        // Get the configured role id.
        $ssroleid = get_config('enrol_studentstaff', 'courseroleassign');

        // Set this up for getting the course.
        $rtable = 'role';
        $rconditions = array('id' => $ssroleid);

        // Get the course object.
        $ssrole = $DB->get_record($rtable, $rconditions);

        // Return the shortname.
        return $ssrole;
    }

    /**
     * Get the enrollment instance course from the custom $courseobj and $ssrolename.
     *
     * @param object $courseobj
     * @param string $ssrolename
     * @return object $einstance
     */
    public static function get_studentstaff_einstance($courseobj, $ssrolename) {
        global $DB;

        // Set this up for getting the enroll instance.
        $etable = 'enrol';
        $econditions = array('courseid' => $courseobj->courseid, 'enrol' => $ssrolename);

        // Get the enrollment instance.
        $einstance = $DB->get_record($etable, $econditions);

        // Return the student/staff enrollment instance.
        return $einstance;
    }

    /**
     * Enroll the Student/Staff role.
     *
     * @param object $courseobj
     * @param object $ssrole
     * @return true
     */
    public static function studentstaff_enrollment($courseobj, $ssrole) {
        global $DB;

        // Instantiate the enroller.
        $enroller = new enrol_studentstaff;

        // Get the course object.
        $course = self::get_studentstaff_course($courseobj);

        // Get or create the enrollment instance.
        $einstance = self::get_studentstaff_einstance($courseobj, $ssrole->shortname);

        // If we already have an enrollment instance, use it, otherwise create one.
        if (empty($einstance)) {
            $enrollid = $enroller->add_instance($course);
            $einstance = $DB->get_record('enrol', array('id' => $enrollid));
        }

        // As long as we have an enrollment instance, rock and roll.
        if (!is_null($einstance)) {

            // Do the nasty.
            mtrace("    Trying to enroll user ID: $courseobj->userid as Student/Staff into course: $course->fullname.");
            $enrolluser = $enroller->enrol_user(
                          $einstance,
                          $courseobj->userid,
                          $ssrole->id,
                          0,
                          0,
                          $status = ENROL_USER_ACTIVE,
                          $recovergrades = false);
            mtrace("    User ID: $courseobj->userid enrolled as Student/Staff into course: $course->fullname.");
        }
        return true;
    }

    /**
     * Get the list of courses for the given user and roles.
     *
     * @param int $userid
     * @param string $roles
     * @param string $enrolls
     * @return array $courses
     */
    public static function get_user_studentstaff_courses($userid, $roles, $enrolls) {
        // Get the globals ready.
        global $DB;

        // Build the SQL to grab non-ss-assigned users' courses.
        $sql = 'SELECT c.id AS courseid,
                       ra.userid AS userid,
                       r.shortname AS rolename,
                       e.id AS enrolid,
                       e.enrol AS enrol
                FROM {role_assignments} ra
                    INNER JOIN {role} r ON r.id = ra.roleid
                    INNER JOIN {context} ctx ON ctx.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ctx.instanceid
                    INNER JOIN {enrol} e ON e.courseid = c.id
                    INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id
                        AND ra.userid = ue.userid
                WHERE ctx.contextlevel = 50
                    AND ue.status = 0
                    AND ue.timestart <= UNIX_TIMESTAMP()
                    AND (ue.timeend >= UNIX_TIMESTAMP() OR ue.timeend = 0)
                    AND r.id IN (' . $roles . ')
                    AND e.enrol IN (' . $enrolls . ')
                    AND ra.userid = ' . $userid . '
                    AND c.id NOT IN (
                        SELECT cou.id
                        FROM {role_assignments} ra
                            INNER JOIN {role} r ON r.id = ra.roleid
                            INNER JOIN {context} ctx ON ctx.id = ra.contextid
                            INNER JOIN {course} cou ON cou.id = ctx.instanceid
                            INNER JOIN {enrol} e ON e.courseid = cou.id
                                AND r.shortname = e.enrol
                            INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id
                                AND ra.userid = ue.userid
                        WHERE ctx.contextlevel = 50
                            AND ue.status = 0
                            AND r.id = 14
                            AND ra.userid = ' . $userid . ')';

         // Grab the courses.
         $courses = $DB->get_records_sql($sql);

         // Return an array of our custom course objects.
         return $courses;
    }

    /**
     * Get the list of site users with roles matching the roles specified in config.
     *
     * @return array $users
     */
    public static function get_site_users_studentstaff() {
        global $DB;

        // Set the system context.
        $systemcontext = context_system::instance();

        // Grab the defined site roles to check.
        $siteroles = explode(",", get_config('enrol_studentstaff', 'siterolescheck'));

        // Set the SR object.
        $sr = new stdClass();

        // Check to see if we have an array or not for sanity's sake.
        if (is_array($siteroles)) {

            // Loop through the site roles.
            foreach ($siteroles as $sr->id) {

                // Get the userids from the role and context.
                $userids = get_users_from_role_on_context($sr, $systemcontext);

                // Loop through the above userids.
                foreach ($userids as $userid) {

                    // Get the user object for each of these userids.
                    $user = $DB->get_record('user', array('id' => $userid->userid));

                    // Build an array of users.
                    $srusers[] = $user;
                }
            }
        } else {

            // Build the sr object.
            $sr->id = $siteroles;

            // Get the userids from the role and context.
            $userids = get_users_from_role_on_context($sr, $systemcontext);

                // Loop through the above userids.
                foreach ($userids as $userid) {

                    // Get the user object for each of these userids.
                    $user = $DB->get_record('user', array('id' => $userid->userid));

                    // Build an array of users.
                    $srusers[] = $user;
                }
        }

        // Return the array of users.
        return $srusers;
    }
}

/**
 * Student / Staff enrollment plugin.
 *
 */
class enrol_studentstaff extends enrol_plugin {
    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array $fields instance fields
     * @return int id of new instance, null if can not be created
     */
}
