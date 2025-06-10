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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class lsuxe_helpers {

    // Redirects.
    /**
     * Convenience wrapper for redirecting to moodle URLs
     *
     * @param  string  $url
     * @param  array   $urlparams   array of parameters for the given URL
     * @param  int     $delay        delay, in seconds, before redirecting
     * @return (http redirect header)
     */
    public function redirect_to_url($url, $urlparams = [], $delay = 2) {
        $moodleurl = new \moodle_url($url, $urlparams);
        redirect($moodleurl, '', $delay);
    }

    /**
     * Config Converter - config settings that have multiple lines with
     * a key value settings will be broken down and converted into an
     * associative array, for example:
     * Monthly 720,
     * Weekly 168
     * .....etc
     * Becomes (Monthly => 720, Weekly => 168)
     * @param  string $configstring setting
     * @param  string $arraytype by default multi, use mirror to miror key/value
     *
     * @return array
     */
    public function config_to_array($configstring, $arraytype = "multi") {

        $configname = get_config('moodle', $configstring);

        // Strip the line breaks.
        $breakstripped = preg_replace("/\r|\n/", " ", $configname);
        // Make sure there are not double spaces.
        $breakstripped = str_replace("  ", " ", $breakstripped);
        // Remove any spaces or line breaks from start or end.
        $breakstripped = trim($breakstripped);

        $exploded = explode(" ", $breakstripped);
        $explodedcount = count($exploded);
        $final = array();

        if ($arraytype == "multi") {
            // Now convert to array and transform to an assoc. array.
            for ($i = 0; $i < $explodedcount; $i += 2) {
                $final[$exploded[$i + 1]] = $exploded[$i];
            }
        } else if ($arraytype == "mirror") {
            // It's possible there may be an extra line break from user input.
            for ($i = 0; $i < $explodedcount; $i++) {
                $tempval = $exploded[$i];
                $final[$tempval] = $tempval;
            }
        }
        return $final;
    }

    /**
     * Function to grab current enrollments for future use.
     *
     * @return @array of $user objects.
     */
    public static function xe_current_enrollments($parms) {
        global $DB, $CFG;

        $interval = isset($parms['intervals']) == true
                    ? ' AND (UNIX_TIMESTAMP() - (xemm.updateinterval * 3600)) > xemm.timeprocessed'
                    : '';

        if ($parms['function'] = 'course' && $parms['courseid'] > 1) {
            $wheres = "AND xemm.courseid = " . $parms['courseid'];
        } else if ($parms['function'] = 'moodle' && $parms['moodleid'] > 0) {
            $wheres = "AND xem.id = " . $parms['moodleid'];
        } else {
            $wheres = '';
        }

        // LSU UES Specific enrollemnt / unenrollment data.
        $lsql = 'SELECT CONCAT(c.id, "_", g.id, "_", u.id, "_", stu.status) AS "xeid",
                u.id AS "userid",
                c.id AS "sourcecourseid",
                c.shortname AS "sourcecourseshortname",
                g.id AS "sourcegroupid",
                g.name AS "sourcegroupname",
                u.username AS "username",
                u.email AS "email",
                u.idnumber AS "idnumber",
                u.firstname AS "firstname",
                u.lastname AS "lastname",
                u.alternatename AS "alternatename",
                stu.status AS "status",
                "student" AS "role",
                u.auth AS "auth",
                xemm.id AS "xemmid",
                xemm.authmethod AS "xauth",
                xem.url AS "destmoodle",
                xem.token AS "usertoken",
                xem.teacherrole AS "teacherrole",
                xem.studentrole AS "studentrole",
                xemm.destcourseid AS "destcourseid",
                xemm.destcourseshortname AS "destshortname",
                xemm.destgroupid AS "destgroupid",
                CONCAT(xemm.destgroupprefix, " ", xemm.groupname) AS "destgroupname"
            FROM {course} c
                INNER JOIN {block_lsuxe_mappings} xemm ON xemm.courseid = c.id
                INNER JOIN {block_lsuxe_moodles} xem ON xem.id = xemm.destmoodleid
                INNER JOIN {enrol_ues_sections} sec ON sec.idnumber = c.idnumber
                INNER JOIN {enrol_ues_courses} cou ON cou.id = sec.courseid
                INNER JOIN {enrol_ues_students} stu ON stu.sectionid = sec.id
                INNER JOIN {user} u ON u.id = stu.userid
                INNER JOIN {enrol} e ON e.courseid = c.id
                    AND e.enrol = "ues"
                INNER JOIN {groups} g ON g.courseid = c.id
                    AND g.id = xemm.groupid
                    AND g.name = xemm.groupname
                    AND g.name = CONCAT(cou.department, " ", cou.cou_number, " ", sec.sec_number)
                LEFT JOIN {groups_members} gm ON gm.groupid = g.id AND u.id = gm.userid
            WHERE sec.idnumber IS NOT NULL
                AND sec.idnumber <> ""
                AND xemm.destcourseid IS NOT NULL
                AND xemm.destgroupid IS NOT NULL
                AND UNIX_TIMESTAMP() > xemm.starttime
                AND UNIX_TIMESTAMP() < xemm.endtime
                AND xem.timedeleted IS NULL
                AND xemm.timedeleted IS NULL
                ' . $interval . $wheres . '

            UNION

            SELECT CONCAT(c.id, "_", g.id, "_", u.id, "_", stu.status) AS "xeid",
                u.id AS "userid",
                c.id AS "sourcecourseid",
                c.shortname AS "sourcecourseshortname",
                g.id AS "sourcegroupid",
                g.name AS "sourcegroupname",
                u.username AS "username",
                u.email AS "email",
                u.idnumber AS "idnumber",
                u.firstname AS "firstname",
                u.lastname AS "lastname",
                u.alternatename AS "alternatename",
                stu.status AS "status",
                "editingteacher" AS "role",
                u.auth AS "auth",
                xemm.id AS "xemmid",
                xemm.authmethod AS "xauth",
                xem.url AS "destmoodle",
                xem.token AS "usertoken",
                xem.teacherrole AS "teacherrole",
                xem.studentrole AS "studentrole",
                xemm.destcourseid AS "destcourseid",
                xemm.destcourseshortname AS "destshortname",
                xemm.destgroupid AS "destgroupid",
                CONCAT(xemm.destgroupprefix, " ", xemm.groupname) AS "destgroupname"
            FROM {course} c
                INNER JOIN {block_lsuxe_mappings} xemm ON xemm.courseid = c.id
                INNER JOIN {block_lsuxe_moodles} xem ON xem.id = xemm.destmoodleid
                INNER JOIN {enrol_ues_sections} sec ON sec.idnumber = c.idnumber
                INNER JOIN {enrol_ues_courses} cou ON cou.id = sec.courseid
                INNER JOIN {enrol_ues_teachers} stu ON stu.sectionid = sec.id
                INNER JOIN {user} u ON u.id = stu.userid
                INNER JOIN {enrol} e ON e.courseid = c.id
                    AND e.enrol = "ues"
                INNER JOIN {groups} g ON g.courseid = c.id
                    AND g.id = xemm.groupid
                    AND g.name = xemm.groupname
                    AND g.name = CONCAT(cou.department, " ", cou.cou_number, " ", sec.sec_number)
                LEFT JOIN {groups_members} gm ON gm.groupid = g.id AND u.id = gm.userid
            WHERE sec.idnumber IS NOT NULL
                AND sec.idnumber <> ""
                AND xemm.destcourseid IS NOT NULL
                AND xemm.destgroupid IS NOT NULL
                AND UNIX_TIMESTAMP() > xemm.starttime
                AND UNIX_TIMESTAMP() < xemm.endtime
                AND xem.timedeleted IS NULL
                AND xemm.timedeleted IS NULL
                ' . $interval . $wheres;

        // Generic Moodle enrollment / suspension data.
        $gsql = 'SELECT CONCAT(c.id, "_", g.id, "_", u.id, "_", IF(ue.status = 0, "enrolled", "unenrolled")) AS "xeid",
                u.id AS "userid",
                c.id AS "sourcecourseid",
                c.shortname AS "sourcecourseshortname",
                g.id AS "sourcegroupid",
                g.name AS "sourcegroupname",
                u.username AS "username",
                u.email AS "email",
                u.idnumber AS "idnumber",
                u.firstname AS "firstname",
                u.lastname AS "lastname",
                u.alternatename AS "alternatename",
                IF(ue.status = 0, "enrolled", "unenrolled") AS "status",
                mr.shortname AS "role",
                u.auth AS "auth",
                xemm.id AS "xemmid",
                xemm.authmethod AS "xauth",
                xem.url AS "destmoodle",
                xem.token AS "usertoken",
                xem.teacherrole AS "teacherrole",
                xem.studentrole AS "studentrole",
                xemm.destcourseid AS "destcourseid",
                xemm.destcourseshortname AS "destshortname",
                xemm.destgroupid AS "destgroupid",
                CONCAT(xemm.destgroupprefix, " ", xemm.groupname) AS "destgroupname"
            FROM {course} c
                INNER JOIN {block_lsuxe_mappings} xemm ON xemm.courseid = c.id
                INNER JOIN {block_lsuxe_moodles} xem ON xem.id = xemm.destmoodleid
                INNER JOIN {enrol} e ON e.courseid = c.id
                INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id
                INNER JOIN {user} u ON u.id = ue.userid
                INNER JOIN {role_assignments} mra ON mra.userid = ue.userid
                    AND mra.userid = u.id
                INNER JOIN {role} mr ON mra.roleid = mr.id
                INNER JOIN {context} ctx ON mra.contextid = ctx.id
                    AND ctx.instanceid = c.id
                    AND ctx.contextlevel = "50"
                INNER JOIN {groups} g ON g.courseid = c.id
                LEFT JOIN {groups_members} gm ON gm.groupid = g.id
                    AND u.id = gm.userid
            WHERE xemm.destcourseid IS NOT NULL
                AND xemm.destgroupid IS NOT NULL
                AND UNIX_TIMESTAMP() > xemm.starttime
                AND UNIX_TIMESTAMP() < xemm.endtime
                AND xem.timedeleted IS NULL
                AND xemm.timedeleted IS NULL
                ' . $interval . $wheres;

        // Check to see if we're forcing Moodle enrollment.
        $ues = isset($CFG->xeforceenroll) == 0 ? true : false;

        // Based on the config and if we're using ues, use the appropriate SQL.
        $sql = $ues && self::is_ues() ? $lsql : $gsql;

        // Get the enrollment / unenrollment data.
        $enrolls = $DB->get_records_sql($sql);

        // Return the data.
        return $enrolls;
    }

    /**
     * Function to count records in the UES section table.
     * This will determine if we're using LSU's enrollment method.
     *
     * @return @bool
     */
    public static function is_ues() {
        global $DB;

        // Instantiate the DB manager.
        $dbman = $DB->get_manager();

        // Set the UES table name.
        $uestable = 'enrol_ues_sections';

        // Check to see if UES is installed.
        $uesinstalled = $dbman->table_exists($uestable);

        // Get a count of records in the UES sections table.
        $uescount = $uesinstalled ? $DB->count_records($uestable) : 0;

        // Determines if we're using UES or not.
        $isues = $uescount > 0 ? true : false;

        // Return the appropriate value.
        return $isues;
    }

    /**
     *
     * @return @bool
     */
    public static function processed($xemmid) {
        global $DB;

        // Set the xemm table name.
        $xemtable = 'block_lsuxe_mappings';

        // Set the time.
        $now = time();

        // Build the minimal data object for update.
        $dataobject = array('id' => $xemmid, 'timeprocessed' => $now);

        // Update the timestamp.
        $return = $DB->update_record($xemtable, $dataobject);

        // Return the appropriate value.
        return $return;
    }

    /**
     * Function to grab the destination courseid.
     *
     * @return @array of objects
     */
    public static function xe_get_destcourse($parms=null) {
        global $DB;

        if ($parms['function'] = 'course' && $parms['courseid'] > 1) {
            $wheres = "AND xemm.courseid = " . $parms['courseid'];
        } else if ($parms['function'] = 'moodle' && $parms['moodleid'] > 0) {
            $wheres = "AND xem.id = " . $parms['moodleid'];
        } else {
            $wheres = '';
        }

        // Build the SQL for grabbing the data.
        $sql = 'SELECT xemm.id AS xemmid,
                   xem.url AS "destmoodle",
                   xem.token AS "usertoken",
                   xemm.destcourseshortname AS "destshortname"
               FROM {block_lsuxe_moodles} xem
                   INNER JOIN {block_lsuxe_mappings} xemm ON xemm.destmoodleid = xem.id
               WHERE xemm.destcourseid IS NULL
                   AND UNIX_TIMESTAMP() > xemm.starttime
                   AND UNIX_TIMESTAMP() < xemm.endtime
                   ' . $wheres;

        // Get the data from the SQL.
        $courses = $DB->get_records_sql($sql);

        return $courses;
    }

    /**
     * Function to grab the destination course id and write it locally.
     *
     * @return @array of objects
     */
    public static function xe_write_destcourse($parms=null) {
        global $DB;

        // Grab the list.
        $courses = self::xe_get_destcourse($parms);

        // Loop through the data we got above.
        foreach ($courses as $course) {
            // Set the page params for our curl requests.
            $pageparams = [
                'wstoken' => $course->usertoken,
                'wsfunction' => 'core_course_get_courses_by_field',
                'moodlewsrestformat' => 'json',
                'field' => 'shortname',
                'value' => $course->destshortname,
            ];

            // Set the defaults for our curl requests.
            $defaults = array(
                CURLOPT_URL => 'https://' . $course->destmoodle . '/webservice/rest/server.php',
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 4,
                CURLOPT_POST => false,
                CURLOPT_POSTFIELDS => $pageparams,
            );

            mtrace("Checking for a remote courseid for <strong>$course->destshortname</strong>.");
            // Instantiate the curl handler.
            $ch = curl_init();

            // Set the curl options.
            curl_setopt_array($ch, $defaults);

            // Unset this otherwise it does not overwrite.
            unset($returndata);

            // Execute the curl handler.
            $returndata = curl_exec($ch);

            // Close the curl handler.
            curl_close($ch);

            // Get the destination course id if we found one.
            $destcourseid = isset(json_decode($returndata, true)['courses'][0])
                            ? json_decode($returndata, true)['courses'][0]['id']
                            : null;

            // Add to the empty errors array if we have any errors.
            $errors[] = isset(json_decode($returndata, true)['courses'][1]) ? json_decode($returndata, true)['courses'][1] : null;

            // If we have a destination course id, update the DB.
            if (isset($destcourseid)) {
                mtrace("We found the destination courseid ($destcourseid) for <strong>$course->destshortname</strong>.");
                // Set the required data object.
                $dataobject = [
                    'id' => $course->xemmid,
                    'destcourseid' => $destcourseid,
                ];

                // Write the data.
                if ($DB->update_record('block_lsuxe_mappings', $dataobject, $bulk = false)) {
                    mtrace("We have written the destination courseid ($destcourseid) "
                        . "for <strong>$course->destshortname</strong> to the local DB.");
                } else {
                    $errors[] = array(
                        "DB Write Error" => "The destination course id: $destcourseid could not be written to the local DB."
                    );
                }
            }
        }
        // Return any errors we might have.
        return isset($errors) ? $errors : true;
    }

    /**
     * Function to grab destination group id if it exsits.
     *
     * @return true
     */
    public static function xe_get_groups($parms) {
        global $DB;

        if ($parms['function'] = 'course' && $parms['courseid'] > 1) {
            $wheres = "AND xemm.courseid = " . $parms['courseid'];
        } else if ($parms['function'] = 'moodle' && $parms['moodleid'] > 0) {
            $wheres = "AND xem.id = " . $parms['moodleid'];
        } else {
            $wheres = '';
        }

        // Build the SQL to get the appropriate data for the webservice.
        $sql = 'SELECT xemm.id AS xemmid,
                   xem.url AS "destmoodle",
                   xem.token AS "usertoken",
                   xemm.destcourseid AS "destcourseid",
                   xemm.destgroupprefix AS "destgroupprefix",
                   xemm.groupname AS "groupname"
               FROM {block_lsuxe_moodles} xem
                   INNER JOIN {block_lsuxe_mappings} xemm ON xemm.destmoodleid = xem.id
               WHERE xemm.destgroupid IS NULL
                   AND xemm.destcourseid IS NOT NULL
                   AND UNIX_TIMESTAMP() > xemm.starttime
                   AND UNIX_TIMESTAMP() < xemm.endtime
                   ' . $wheres;

        // Actually get the data.
        $groups = $DB->get_records_sql($sql);

        return $groups;
    }

    /**
     * Function to grab a matching desintation group id.
     *
     * @return @int $destgroupid
     */
    public static function xe_get_destgroup($group) {
        // Set the group check page params.
        $pageparams = [
            'wstoken' => $group->usertoken,
            'wsfunction' => 'core_group_get_course_groups',
            'moodlewsrestformat' => 'json',
            'courseid' => $group->destcourseid,
        ];

        // Set the group check defaults.
        $defaults = array(
            CURLOPT_URL => 'https://' . $group->destmoodle . '/webservice/rest/server.php',
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POST => false,
            CURLOPT_POSTFIELDS => $pageparams,
        );

        // Instantiate the curl handler.
        $ch = curl_init();
        // Set the curl options.
        curl_setopt_array($ch, $defaults);

        // Run the curl handler and store the returned data.
        unset($returndata);
        $returndata = curl_exec($ch);

        // Close the curl handler.
        curl_close($ch);

        // Decode the returned data.
        $returnedgroups = json_decode($returndata, true);

        // Loop through the returned groups and try to match the intended group name.
        foreach ($returnedgroups as $returnedgroup) {
            // Build the intended group name.
            $destgroupnamexp = $group->destgroupprefix . " " . $group->groupname;
            // Set the actual remote group name.
            $destgroupname = $returnedgroup['name'];

            // If we have a match, store the destination group id and exit the loop.
            if ($destgroupnamexp == $destgroupname) {
                $destgroupid = $returnedgroup['id'];
                mtrace("We found a destination <strong>$destgroupname</strong> "
                    . "group that matches the local group <strong>$destgroupnamexp</strong>.");
                break;
            } else {
                $destgroupid = null;
                mtrace("We did not find a remote group matching: <strong>$destgroupnamexp</strong>.");
            }
        }
        return $destgroupid;
    }

    /**
     * Write the existing group id locally.
     * If the destination group is not present, create it.
     *
     * @return true
     */
    public static function xe_write_destgroup($group) {
        global $DB;

        $destgroupid = self::xe_get_destgroup($group);
        if (isset($destgroupid)) {
            // Build the data object for writing to the local DB.
            $dataobject = [
                'id' => $group->xemmid,
                'destgroupid' => $destgroupid,
            ];
            // Write it locally.
            $destgroupnamexp = $group->destgroupprefix . " " . $group->groupname;
            $writeout = $DB->update_record('block_lsuxe_mappings', $dataobject, $bulk = false);
            mtrace("We have written a destination groupid ($destgroupid) record "
                . "for <strong>$destgroupnamexp</strong> to the local DB.");
        } else {
            // Create the remote group.
            $destgroupid = self::xe_create_remote_group($group);
        }
    }

    /**
     * Write the existing group id locally.
     * If the destination group is not present, create it.
     *
     * @return true
     */
    public static function xe_write_destgroups($groups) {
        global $DB;

        foreach ($groups as $group) {
            $destgroup = self::xe_write_destgroup($group);
        }
    }

    public static function xe_create_remote_group($group) {
        global $DB;

        // Set the group creation page params.
        $pageparams = [
            'wstoken' => $group->usertoken,
            'wsfunction' => 'core_group_create_groups',
            'moodlewsrestformat' => 'json',
            'groups[0][courseid]' => $group->destcourseid,
            'groups[0][name]' => $group->destgroupprefix . " " . $group->groupname,
            'groups[0][description]' => "From " . $group->destgroupprefix,
        ];

        // Set the group creation defaults.
        $defaults = array(
            CURLOPT_URL => 'https://' . $group->destmoodle . '/webservice/rest/server.php',
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POST => false,
            CURLOPT_POSTFIELDS => $pageparams,
        );

        // Set up another curl handler.
        $ch = curl_init();

        // Set its options.
        curl_setopt_array($ch, $defaults);

        // Execute the curl handler and store the returned data.
        unset($returndata);
        $returndata = curl_exec($ch);

        // Close the curl handler.
        curl_close($ch);

        // Decode the json data.
        $destgroupid = json_decode($returndata, true)[0]['id'];

        // Another sanity check to make sure it's set before we write it.
        if (isset($destgroupid)) {
            mtrace("We have created a remote group <strong>$group->destgroupprefix "
                . "$group->groupname</strong> with id ($destgroupid).");
            // Set the data object for writing to our DB.
            self::xe_write_destgroup($group);
        }
    }

    /**
     * Function to grab a destination user if they exsit.
     *
     * @param  @object $user object for the given user.
     * @return @array
     */
    public static function xe_remote_user_lookup($user) {

        // Set the user check page params.
        unset($pageparams);
        $pageparams = [
            'wstoken' => $user->usertoken,
            'wsfunction' => 'core_user_get_users',
            'moodlewsrestformat' => 'json',
            'criteria[0][key]' => 'username',
            'criteria[0][value]' => $user->username,
        ];

        // Set the user check defaults.
        unset($defaults);
        $defaults = array(
            CURLOPT_URL => 'https://' . $user->destmoodle . '/webservice/rest/server.php',
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POST => false,
            CURLOPT_POSTFIELDS => $pageparams,
        );

        // Create the curl handler.
        $ch = curl_init();

        // Set the curl options.
        curl_setopt_array($ch, $defaults);

        // Run the curl handler and store the returned data.
        unset($returndata);
        $returndata = curl_exec($ch);

        // Close the curl handler.
        curl_close($ch);

        // Decode the returned data.
        $returneduser = json_decode($returndata, true)['users'];
        $returneduser = isset($returneduser[0]) ? $returneduser[0] : null;

        return $returneduser;
    }

    /**
     * Check to see if the local user object matches the remote user.
     *
     * @param  @object $user object for the given user.
     * @param  @array $returneduser array of parameters for the returned user.
     * @return @bool
     */
    public static function xe_remote_user_match($user, $returneduser) {
        // Sanity check to short-circuit things.
        if (!isset($returneduser['id'])) {
            return false;
        }

        // Make sure we have an idnumber.
        $returneduser['idnumber'] = isset($returneduser['idnumber']) ? $returneduser['idnumber'] : '';

        // Set up the remote user object.
        $ruser                = new stdClass();
        $ruser->username      = $returneduser['username'];
        $ruser->email         = $returneduser['email'];
        $ruser->idnumber      = $returneduser['idnumber'];
        $ruser->firstname     = $returneduser['firstname'];
        $ruser->lastname      = $returneduser['lastname'];
        $ruser->alternatename = isset($returneduser['alternatename'])
                                ? $returneduser['alternatename']
                                : null;
        $ruser->auth          = $returneduser['auth'];

        // Set up the local user object.
        $luser                = new stdClass();
        $luser->username      = $user->username;
        $luser->email         = $user->email;
        $luser->idnumber      = $user->idnumber;
        $luser->firstname     = $user->firstname;
        $luser->lastname      = $user->lastname;
        $luser->alternatename = $user->alternatename;
        $luser->auth          = strtolower($user->xauth);

        if ($luser == $ruser) {
            mtrace("The local ($luser->username) and remote user ($ruser->username) objects match entirely. Skipping.");
            return true;
        } else {
            mtrace("Something in the user object does not match.");
            return false;
        }
    }

    /**
     * If the destination user exists but not all fields match, update.
     *
     * @param  @object $user object for the given user.
     * @param  @array $returneduser array of parameters for the returned user.
     * @return @bool
     */
    public static function xe_remote_user_update($user, $returneduser) {
        // Sanity check to short-circuit things.
        if (!isset($returneduser['id'])) {
            return false;
        }

        mtrace("We are atttempting to update the remote user "
            . $returneduser['username']
            . " to match the local user $user->username.");

        // Set the user update page params.
        $pageparams = [
            'wstoken' => $user->usertoken,
            'wsfunction' => 'core_user_update_users',
            'moodlewsrestformat' => 'json',
            'users[0][id]' => $returneduser['id'],
            'users[0][username]' => $user->username,
            'users[0][email]' => $user->email,
            'users[0][auth]' => strtolower($user->xauth),
            'users[0][firstname]' => $user->firstname,
            'users[0][lastname]' => $user->lastname,
            'users[0][alternatename]' => $user->alternatename,
            'users[0][email]' => $user->email,
            'users[0][idnumber]' => $user->idnumber,
        ];

        // Set the user update defaults.
        $defaults = array(
            CURLOPT_URL => 'https://' . $user->destmoodle . '/webservice/rest/server.php',
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POST => false,
            CURLOPT_POSTFIELDS => $pageparams,
        );

        // Create the curl handler.
        $ch = curl_init();

        // Set the curl options.
        curl_setopt_array($ch, $defaults);

        // Run the curl handler and store the returned data.
        unset($returnudata);
        $returndata = curl_exec($ch);

        // Close the curl handler.
        curl_close($ch);

        $returneduserupdate = json_decode($returndata, true);

        $returneduserupdate = isset($returneduserupdate) ? false : true;
        if ($returneduserupdate) {
            mtrace("We are have updated the remote user "
                . $returneduser['username']
                . " details to match the local user $user->username.");
        } else {
            mtrace("ERROR: We are unable to update the remote user "
                . $returneduser['username']
                . " details to match the local user $user->username.");
        }
        return $returneduserupdate;
    }

    /**
     * If the destination user does not exist, create them.
     *
     * @param  @object $user object for the given user.
     * @return @bool
     */
    public static function xe_remote_user_create($user) {
        mtrace("User <strong>$user->username</strong> not found on remote system, create them.");

        // Make sure idnumber is set.
        $user->idnumber = isset($user->idnumber) ? $user->idnumber : '';

        // Set the user creation page params.
        if (strtolower($user->xauth) <> 'manual') {
            $pageparams = [
                'wstoken' => $user->usertoken,
                'wsfunction' => 'core_user_create_users',
                'moodlewsrestformat' => 'json',
                'users[0][username]' => $user->username,
                'users[0][email]' => $user->email,
                'users[0][auth]' => strtolower($user->xauth),
                'users[0][firstname]' => $user->firstname,
                'users[0][lastname]' => $user->lastname,
                'users[0][alternatename]' => $user->alternatename,
                'users[0][email]' => $user->email,
                'users[0][idnumber]' => $user->idnumber,
            ];
        } else {
            $pageparams = [
                'wstoken' => $user->usertoken,
                'wsfunction' => 'core_user_create_users',
                'moodlewsrestformat' => 'json',
                'users[0][username]' => $user->username,
                'users[0][email]' => $user->email,
                'users[0][auth]' => 'manual',
                'users[0][createpassword]' => true,
                'users[0][firstname]' => $user->firstname,
                'users[0][lastname]' => $user->lastname,
                'users[0][alternatename]' => $user->alternatename,
                'users[0][email]' => $user->email,
                'users[0][idnumber]' => $user->idnumber,
            ];
        }
        // Set the user update defaults.
        $defaults = array(
            CURLOPT_URL => 'https://' . $user->destmoodle . '/webservice/rest/server.php',
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POST => false,
            CURLOPT_POSTFIELDS => $pageparams,
        );

        // Create the curl handler.
        $ch = curl_init();

        // Set the curl options.
        curl_setopt_array($ch, $defaults);

        // Run the curl handler and store the returned data.
        unset($returndata);
        $returndata = curl_exec($ch);

        // Close the curl handler.
        curl_close($ch);

        $returnedusercreate = json_decode($returndata, true);
        $returnedusercreate = $returnedusercreate[0];

        if ($returnedusercreate['id']) {
            mtrace("Created the missing remote user matching username: "
                    . "<strong>$user->username</strong> with id "
                    . $returnedusercreate['id'] . ".");
            return $returnedusercreate;
        } else {
            mtrace("ERROR creating the missing remote user - "
                   . "username: <strong>$user->username</strong>.");
            return false;
        }
    }

    /**
     * Enroll the user.
     *
     * @param  @object $user object for the given user.
     * @param  @int remoteuserid id of the returned user.
     * @return @bool
     */
    public static function xe_enroll_user($user, $remoteuserid) {
        global $CFG;

        $role = isset($CFG->xestudentrolename) ? $CFG->xestudentrolename : 'student';

        $studentrole = $user->studentrole < 99 ? $user->studentrole : $CFG->block_lsuxe_xestudentroleid;
        $teacherrole = $user->teacherrole < 99 ? $user->teacherrole : $CFG->block_lsuxe_xeteacherroleid;

        $roleid = $user->role == $role ? $studentrole : $teacherrole;

        // Set the enrollment page params.
        $pageparams = [
            'wstoken' => $user->usertoken,
            'wsfunction' => 'enrol_manual_enrol_users',
            'moodlewsrestformat' => 'json',
            'enrolments[0][roleid]' => $roleid,
            'enrolments[0][userid]' => $remoteuserid,
            'enrolments[0][courseid]' => $user->destcourseid,
        ];

        // Set the user update defaults.
        $defaults = array(
            CURLOPT_URL => 'https://' . $user->destmoodle . '/webservice/rest/server.php',
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POST => false,
            CURLOPT_POSTFIELDS => $pageparams,
        );

        // Create the curl handler.
        $ch = curl_init();

        // Set the curl options.
        curl_setopt_array($ch, $defaults);

        // Run the curl handler and store the returned data.
        unset($returndata);
        $returndata = curl_exec($ch);

        // Close the curl handler.
        curl_close($ch);

        // Decode the data.
        $returneduserenrol = json_decode($returndata, true);

        // If there's data, there's an error.
        if (isset($returneduserenrol)) {
            mtrace($returneduserenrol['exception']
                   . " - " .
                   $returneduserenrol['errorcode']
                   . " - " .
                   $returneduserenrol['message']);
        } else {
            // Success.
            mtrace("Enrolled " . $user->username . " with remote userid"
                    . " $remoteuserid as $user->role in"
                    . " the remote courseid " . $user->destcourseid
                    . " on https://" . $user->destmoodle . ".");
        }

        $returndata = isset($returneduserenrol) ? false : true;
        return $returndata;
    }

    /**
     * Unenroll the user.
     *
     * @param  @object $user object for the given user.
     * @param  @int remoteuserid id of the returned user.
     * @return @bool
     */
    public static function xe_unenroll_user($user, $remoteuserid) {
        $role = isset($CFG->xestudentrolename) ? $CFG->xestudentrolename : 'student';
        $roleid = $user->role == 'student' ? $user->studentrole : $user->teacherrole;

        // Set the enrollment page params.
        $pageparams = [
            'wstoken' => $user->usertoken,
            'wsfunction' => 'enrol_manual_unenrol_users',
            'moodlewsrestformat' => 'json',
            'enrolments[0][roleid]' => $roleid,
            'enrolments[0][userid]' => $remoteuserid,
            'enrolments[0][courseid]' => $user->destcourseid,
        ];

        // Set the user update defaults.
        $defaults = array(
            CURLOPT_URL => 'https://' . $user->destmoodle . '/webservice/rest/server.php',
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POST => false,
            CURLOPT_POSTFIELDS => $pageparams,
        );

        // Create the curl handler.
        $ch = curl_init();

        // Set the curl options.
        curl_setopt_array($ch, $defaults);

        // Run the curl handler and store the returned data.
        unset($returndata);
        $returndata = curl_exec($ch);

        // Close the curl handler.
        curl_close($ch);

        $returneduserunenrol = json_decode($returndata, true);

        if (isset($returneduserunenrol)) {
            mtrace($returneduserunenrol['exception']
                   . " - " .
                   $returneduserunenrol['errorcode']
                   . " - " .
                   $returneduserunenrol['message']);
        } else {
            mtrace("Unenrolled $user->username with remote userid "
                    . "$remoteuserid from $user->role role from "
                    . " the remote courseid $user->destcourseid "
                    . " on https://$user->destmoodle.");
        }

        $returndata = isset($returneduserunenrol) ? false : true;
        return $returndata;
    }

    /**
     * Add user to a group.
     *
     * @param  @object $user object for the given user.
     * @param  @int remoteuserid id of the returned user.
     * @return @bool
     */
    public static function xe_add_user_to_group($user, $remoteuserid) {
        // Set the group-add page params.
        $pageparams = [
            'wstoken' => $user->usertoken,
            'wsfunction' => 'core_group_add_group_members',
            'moodlewsrestformat' => 'json',
            'members[0][userid]' => $remoteuserid,
            'members[0][groupid]=' => $user->destgroupid,
        ];

        // Set the group-add defaults.
        $defaults = array(
            CURLOPT_URL => 'https://' . $user->destmoodle . '/webservice/rest/server.php',
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POST => false,
            CURLOPT_POSTFIELDS => $pageparams,
        );

        // Create the curl handler.
        $ch = curl_init();

        // Set the curl options.
        curl_setopt_array($ch, $defaults);

        // Run the curl handler and store the returned data.
        unset($returndata);
        $returndata = curl_exec($ch);

        // Close the curl handler.
        curl_close($ch);

        $returnedgroupenrol = json_decode($returndata, true);

        if (isset($returnedgroupenrol)) {
            mtrace($returnedgroupenrol['exception']
                   . " - " .
                   $returnedgroupenrol['errorcode']
                   . " - " .
                   $returnedgroupenrol['message']);
        } else {
            mtrace("Added $user->username with remote userid"
                    . " $remoteuserid to remote group id $user->destgroupid"
                    . " in the remote courseid $user->destcourseid"
                    . " on https://$user->destmoodle.");
        }

        $returndata = isset($returnedgroupenrol) ? false : true;
        return $returndata;
    }
}
