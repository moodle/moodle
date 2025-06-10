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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_pu_helpers {

    /**
     * Checks to see if a user is a GUILD user in the given course context
     *
     * @param  array $params  [user_id, course_id]
     * @return bool
     */
    public static function guilduser_check($params) {
        global $DB, $USER;
        // Check to see if the user is who they say they are.
        if (!isset($params['pcmid']) && $USER->id === $params['user_id']) {
            // Now check to see if the user (verified) is a guild user in the course in question.
            if ($DB->get_record('block_pu_guildmaps', array('course' => $params['course_id'], 'user' => $USER->id, 'current' => 1))) {
                return true;
            }
        } else if (isset($params['pcmid']) && $USER->id === $params['user_id']) {
            if (self::codemappings(array('course_id' => $params['course_id'], 'user_id' => $params['user_id'], 'pcmid' => $params['pcmid']))) {
                return true;
            }
        } else {
           return false;
        }
    }

    /**
     * Grabs a list of all invalid codes and whatever mappings they may have.
     *
     * @return @array
     */
    public static function get_invalids($perms='any') {
        global $DB;

        $wheres = $perms == 'any' ? 'pc.valid IN (0,2)' : 'pc.valid = 0';

        $sql = 'SELECT pc.id AS pcid,
                    pcm.id AS pcmid,
                    pc.couponcode AS accesscode,
                    COALESCE(c.shortname, "-") AS course,
                    COALESCE(CONCAT(u.firstname, " ", u.lastname), "-") AS user,
                    COALESCE(u.idnumber, "-") AS idnumber,
                    COALESCE(u.email, "-") AS email
                FROM {block_pu_codes} pc
                    LEFT JOIN {block_pu_codemaps} pcm ON pc.id = pcm.code
                    LEFT JOIN {block_pu_guildmaps} pgm ON pgm.id = pcm.guild
                    LEFT JOIN {course} c ON c.id = pgm.course
                    LEFT JOIN {user} u ON u.id = pgm.user
                WHERE ' . $wheres . '
                ORDER BY u.lastname DESC,
                         pc.used DESC,
                         pc.valid DESC,
                         pcm.updatedate ASC,
                         c.shortname ASC';

        $invalids = $DB->get_records_sql($sql);

        return $invalids;
    }

    /**
     * Resets invalid codes to unused usable codes.
     *
     * @return @bool
     */
    public static function reset_invalid($pcid, $pcmid) {
        global $CFG, $DB;

        // Set the tables.
        $pctable  = $CFG->prefix . "block_pu_codes";
        $pcmtable = $CFG->prefix . "block_pu_codemaps";

        // Build the SQL.
        $sql = 'UPDATE ' . $pctable . ' pc
                SET pc.used = 0, pc.valid = 1
                WHERE pc.id = ' . $pcid;

        // First delete the mapping.
        if ((isset($pcid)) && isset($pcmid)) {
            $DB->delete_records('block_pu_codemaps', array('id' => $pcmid));
        }

        // Now update the coupon code.
        if (isset($pcid)) {
            $DB->execute($sql);
        }
    }

    /**
     * Marks a known invalid code to make it not show.
     *
     * @return @bool
     */
    public static function known_invalid($pcid) {
        global $CFG, $DB;

        // Set the table.
        $pctable  = $CFG->prefix . "block_pu_codes";

        // Build the SQL.
        $sql = 'UPDATE ' . $pctable . ' pc
                SET pc.used = 0, pc.valid = 2
                WHERE pc.id = ' . $pcid;

        // Set this for future use.
        $return = false;

        // Update the code.
        if (isset($pcid)) {
            // Both execute the query and set the return status.
            $return = $DB->execute($sql);
        }

        // Return the status.
        return $return;
    }

    /**
     * Retreives the code mappings for a user/course and a given coupon code mapping.
     *
     * @return array of objects containing
                      [ pcmid,
                        coursefullname,
                        userfirstname,
                        userlastname,
                        username,
                        LSUID,
                        useremail,
                        couponcode,
                        used,
                        valid ]
     */
    public static function codemappings($params) {
        // Needed to invoke the DB.
        global $DB;

        // Set up the course id for later.
        $cid = $params['course_id'];

        // Set up the user id for later.
        $uid = $params['user_id'];

        $ands = isset($params['pcmid']) ? 'AND pcm.id = ' . $params['pcmid'] : '';

        // The SQL.
        $mappedsql = "SELECT pcm.id AS pcmid,
               c.fullname AS coursefullname,
               u.firstname AS userfirstname,
               u.lastname AS userlastname,
               u.username AS username,
               u.idnumber AS LSUID,
               u.email AS useremail,
               pc.couponcode AS couponcode,
               pc.used AS used,
               pc.valid AS valid
        FROM {block_pu_guildmaps} pgm
            INNER JOIN {course} c ON c.id = pgm.course
            INNER JOIN {user} u ON u.id = pgm.user
            INNER JOIN {block_pu_codemaps} pcm ON pcm.guild = pgm.id
            INNER JOIN {block_pu_codes} pc ON pcm.code = pc.id
        WHERE u.deleted = 0
            AND pgm.current = 1
            AND c.id = $cid
            AND u.id = $uid
            $ands";

        // Build the array(s).
        $mapped = $DB->get_records_sql($mappedsql);

        // Return the data.
        return $mapped;
    }

    /**
     * Marks a given code as used or invalid.
     *
     * @return bool
     */
    public static function pu_mark($params) {
        // Needed to invoke the DB.
        global $DB;

        // Set up these for later.
        $cid   = $params['course_id'];
        $uid   = $params['user_id'];
        $pcmid = $params['pcmid'];
        $func  = $params['function'];

        // Grab the count of used codes for this course / user.
        $numused  = self::pu_uvcount($params = array('course_id' => $cid, 'user_id' => $uid, 'uv' => $func));

        // Grab the total number of codes allocated for this course.
        $total = self::pu_codetotals($params = array('course_id' => $cid, 'uv' => $func));

        // Build the setter.
        $setval = $func == 'used' ? 1 : 0;
        $setter = $func == 'used' ? "SET pc.used=$setval" : "SET pc.valid=$setval";

        // The SQL.
        $usedsql = "UPDATE {block_pu_guildmaps} pgm
            INNER JOIN {course} c ON c.id = pgm.course
            INNER JOIN {user} u ON u.id = pgm.user
            INNER JOIN {block_pu_codemaps} pcm ON pcm.guild = pgm.id
            INNER JOIN {block_pu_codes} pc ON pcm.code = pc.id
            $setter
        WHERE u.deleted = 0
            AND pgm.current = 1
            AND c.id = $cid
            AND u.id = $uid
            AND pcm.id = $pcmid";

        // Build the array(s).
        if ($numused < $total) {
            $used = $DB->execute($usedsql);
        } else {
            $used = false;
        }

        // Return the boolean.
        return $used;
    }

    /**
     * Marks a given code as used or invalif.
     *
     * @return object
     */
    public static function pu_assign($params) {
        // Needed to invoke the DB.
        global $DB;

        // Set up these for later.
        $cid   = $params['course_id'];
        $uid   = $params['user_id'];

        // Find the guildmap id for this person / course.
        $gmid = $DB->get_record('block_pu_guildmaps', array('course' => $cid, 'user' => $uid, 'current' => 1));

        $randsql = "SELECT pc.id AS id
            FROM {block_pu_codes} pc
            LEFT JOIN {block_pu_codemaps} pcm ON pcm.code = pc.id
            WHERE pcm.id IS NULL
            AND pc.valid = 1
            AND pc.used = 0
            ORDER BY RAND()
            LIMIT 1";

        // Grab a random valid unassigned record.
        $pcid = $DB->get_record_sql($randsql);

        if (!isset($pcid->id)) {
            $url = new moodle_url('/');
            redirect($url, get_string('nomorecodes', 'block_pu'), null, \core\output\notification::NOTIFY_ERROR); 
        }

        // Build the data object.
        $assigned = new \stdClass();
        $assigned->code = $pcid->id;
        $assigned->guild = $gmid->id;
        $assigned->updater = $uid;
        $assigned->updatedate = time(); 

        $assigned->id = $DB->insert_record('block_pu_codemaps', $assigned);

        // Return the data.
        return $assigned;
    }

    /**
     * Returns the count of used codes for this course / user.
     *
     * @return int
     */
    public static function pu_uvcount($params) {
        // Needed to invoke the DB.
        global $DB;

        // Set up these for later.
        $cid   = $params['course_id'];
        $uid   = $params['user_id'];
        $uv    = $params['uv'];

        if ($uv == "used") {
            $uvands = "AND pc.used = 1 AND pc.valid = 1";
        } else if ($uv == "invalid") {
            $uvands = "AND pc.valid IN (0,2)";
        } else {
            $uvands = "AND pc.valid = 1";
        }

        $uvsql = "SELECT COUNT(pcm.id) AS pcmcount
            FROM {block_pu_guildmaps} pgm
                INNER JOIN {block_pu_codemaps} pcm ON pcm.guild = pgm.id
                INNER JOIN {block_pu_codes} pc ON pcm.code = pc.id
            WHERE pgm.current = 1
                AND pgm.course = $cid
                AND pgm.user = $uid
                $uvands";

        // Grab a random valid unassigned record.
        $uvcount = $DB->get_record_sql($uvsql);

        // Return the data.
        return $uvcount->pcmcount;
    }

    /**
     * Returns the ProctorU code map object, if there is one.
     *
     * @return @object
     */
    public static function pu_pcmexists($params) {
        // Needed to invoke the DB.
        global $DB;

        // Set up these for later.
        $cid   = $params['course_id'];
        $uid   = $params['user_id'];
        $pcm   = $params['pcm_id'];

        $pesql = "SELECT 1 as tf
                  FROM {block_pu_codemaps} pcm
                      INNER JOIN {block_pu_guildmaps} pgm ON pgm.id = pcm.guild
                      INNER JOIN {block_pu_codes} pc ON pc.id = pcm.code
                  WHERE pc.valid = 1
                      AND pc.used = 0
                      AND pcm.id = $pcm
                      AND pgm.user = $uid
                      AND pgm.course = $cid";

        // Grab a random valid unassigned record.
        $data = $DB->get_record_sql($pesql);

        // Set up the boolean for return.
        $tf = isset($data->tf) == 1 ? true: false;

        // Return true or false.
        return $tf;
    }

    /**
     * Returns the number of codes and replacements allowed.
     *
     * @param array $params [ courseid ]
     * @return object
     */
    public static function pu_codetotals($params) {
        global $CFG, $DB;

        // Grab the course id for later use.
        $cid = $params['course_id'];

        // Set this up for later.
        $sitesetting = isset($CFG->block_pu_defaultcodes) ? $CFG->block_pu_defaultcodes : 3;

        // Set up the site level default object.
        $defaults = new \stdClass();
        $defaults->id = 0;
        $defaults->course = $cid;
        $defaults->codecount = $sitesetting;
        $defaults->invalidcount = $sitesetting;
        $defaults->overridecode = false;
        $defaults->overrideinvalid = false;

        // Build the override object.
        $override = $DB->get_record('block_pu_overrides', array('course' => $cid));

        if (isset($override->id)) {
            // Make sure there is actually a code count set.
            $override->overridecode = isset($override->codecount) ? true : false;
            $override->codecount = $override->overridecode ? $override->codecount : $defaults->codecount;

            // Make sure there is actually an override set.
            $override->overrideinvalid = isset($override->invalidcount) ? true : false;
            $override->invalidcount = $override->overrideinvalid ? $override->invalidcount : $defaults->invalidcount;

            // Return the override object.
            return $override;
        } else {
           // Return the site default object.
           return $defaults;
        }
    }

    /**
     * Returns an array of objects.
     *
     * The overrides array is a collection of the GUILD courseid and it's specific 
     *     number of codes and replacements allowed for all GUILD courses.
     *
     * @return array
     */
    public static function pu_overrides($guildcourses) {
        // Set up the final object.
        $data = new \stdClass;

        // Loop through these to get the data we need.
        foreach ($guildcourses as $guildcourse) {

            // Build the array of overrides and their statuses.
            $overrides[] = self::pu_codetotals(array('course_id' => $guildcourse->course));
        }

    return $overrides;
    }


    /**
     */
    public static function pu_override($guildcourse) {
        // Build the array of the override and its status.
        $override = self::pu_codetotals(array('course_id' => $guildcourse));

        return $override;
    }

    /**
     * Returns an array of courseids.
     *
     * @return array
     */
    public static function pu_guildcourses() {
        global $DB;

        // Build the SQL for use.
        $sql = 'SELECT course FROM {block_pu_guildmaps} WHERE current = 1 GROUP BY course';

        // Get the GUILD course data.
        $guildcourses = $DB->get_records_sql($sql);

    return $guildcourses;
    }

    /**
     * @return array
     */
    public static function pu_writeoverrides($fromform, $userid) {
        global $DB;

        // Loop through the key value pair data sent by the form.
        foreach ($fromform as $key => $value) {

           // Build the types for use in the future.
           $types = explode("_", $key);

           // If we have not set data, set the value to null.
           $intvalue = $value == '' ? null : (int)$value;

           // If we have set the command and courseid, do stuff.
           if (isset($types[0]) && isset($types[1])) {

               // Set the command.
               $command  = $types[0];

               // Set the courseid.
               $courseid = $types[1];

               // Check and insert / update records as required.
               $orcomplete = self::pu_updaterecords($command, $courseid, $intvalue);
           }
       }

       return true;
    }


    /**
     * @return array
     */
    public static function pu_writevalidates($fromform, $userid) {
        global $DB;

        // Loop through the key value pair data sent by the form.
        foreach ($fromform as $key => $value) {

           // Build the types for use in the future.
           $types = explode("_", $key);

           // If we have not set data, set the value to 0 (invalid, but shows in the UI).
           $command = $value == '' ? 0 : (int)$value;

           // If we have set the pcid, and pcmid, do stuff.
           if (isset($types[0]) && isset($types[2])) {

               // Set the ProctorU code id.
               $pcid  = $types[1];

               // Set the ProctorU mapping id.
               $pcmid  = $types[3];

               if ($command == 1) {
                   $invalidate = self::reset_invalid($pcid, $pcmid);
               } else if ($command == 2) {
                   $invalidate = self::known_invalid($pcid);
               }
           }
       }

       return true;
    }



    public static function pu_updaterecords($command, $courseid, $intvalue=null) {
        // First we check to make sure we're working with a set value.
        if (isset($intvalue)) {

            // Check to see if this record exists.
            $recordexists = self::pu_checkrecord($command, $courseid, $intvalue);

            // Just in case we don't find a specific matching record, look for course level entries.
            $courseexists = self::pu_checkcourse($courseid);

            // If we don't have a course level entry.
            if (!$courseexists) {
                // Insert the new record.
                self::pu_updaterecord($id=null, $courseid, $command, $intvalue, 'insert');

            // If we have a 100% matching record, leave it.
            } else if ($recordexists) {

            // If we have a course entry but not a 100% matching record, update it.
            } else if (!$recordexists) {
                // Update the record.
                self::pu_updaterecord($courseexists->id, $courseid, $command, $intvalue, 'update');
            }
        }
    }

    /**
     * Returns an object from the block_pu_overrides table.
     *
     * @return object
     */
    public static function pu_checkrecord($command, $courseid, $intvalue) {
        global $DB;

        // Build the SQL for use.
        $recordsql = 'SELECT id FROM {block_pu_overrides} WHERE course = ' . $courseid . ' AND ' . $command . ' = ' . $intvalue;

        // Get the record.
        $recordexists = $DB->get_record_sql($recordsql);

        return $recordexists;
    }

    /**
     * Returns an object from the block_pu_overrides table.
     *
     * @return object
     */
    public static function pu_checkcourse($courseid) {
        global $DB;

        // Build the SQL for use.
        $coursesql = 'SELECT id FROM {block_pu_overrides} WHERE course = ' . $courseid;

        // Get the course entry.
        $courseexists = $DB->get_record_sql($coursesql);

        return $courseexists;
    }

    /**
     * Updates the block_pu_overrides table.
     *
     * @return bool
     */
    public static function pu_updaterecord($id, $courseid, $command, $intvalue, $action) {
        global $DB;

        // Build the data object.
        $data = new \stdClass();

        // Set the courseid from the passed value.
        $data->course = $courseid;

        // Set what data we need to.
        $data->$command = $intvalue;

        // If we're updating an existing record.
        if ($action == 'update') {

            // Set the id.
            $data->id = $id;

            // Update the record.
            $updated = $DB->update_record('block_pu_overrides', $data, $bulk=false);

        // If we're inserting a new record.
        } else {

            // Update the record.
            $updated = $DB->insert_record('block_pu_overrides', $data, $returnid=true, $bulk=false);
        }

        return $updated;
    }

    /**
     * Returns the system's custom user profile fields as array
     *
     * @return @array [shortname => name]
     */
    public static function get_user_profile_field_array() {
        global $DB;

        // Set up the array.
        $userprofilefields = [];

        // Make sure we have any profile fields.
        if ($profilefields = $DB->get_records('user_info_field')) {
            // Set the idnumber as the 1st one.
            $userprofilefields['pu_idnumber'] = get_string('idnumber');

            // Loop through the profile fields.
            foreach ($profilefields as $profilefield) {

                // Keep building the array.
                $userprofilefields[$profilefield->shortname] = $profilefield->name;
            }
        }
        // Return the array of user profile fields.
        return $userprofilefields;
    }
}
