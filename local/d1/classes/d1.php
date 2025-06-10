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
 * @copyright 2022 onwards LSUOnline & Continuing Education
 * @copyright 2022 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @package    local_d1
 * @copyright  2022 onwards LSUOnline & Continuing Education
 * @copyright  2022 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lsupgd1 {

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
     * Grabs D1 Webseervice settings.
     *
     * @return @object $s
     */
    public static function get_d1_settings() {
        global $CFG;

        // Build the object.
        $s           = new stdClass();
        // Get the Moodle data root.
        $s->dataroot = $CFG->dataroot;
        // Get the DestinyOne webservice url prefix.
        $s->wsurl    = get_config('local_d1', 'd1_wsurl');
        $s->pd_dp    = get_config('local_d1', 'pd_daysprior');
        $s->odl_dp   = get_config('local_d1', 'odl_daysprior');

        return $s;
    }

    /**
     * Grabs D1 Webseervice credentials.
     *
     * @return @object $c
     */
    public static function get_d1_creds() {
        // Build the object.
        $c = new stdClass();
        // Get the username from the config settings.
        $c->username   = get_config('local_d1', 'username');
        // Get the webservice password.
        $c->password   = get_config('local_d1', 'password');
        // Get the debug file storage location.

        return $c;
    }

    /**
     * Grabs the token from the D1 web services.
     *
     * @return @string $token
     */
    public static function get_token() {
        // Get the data needed.
        $s = self::get_d1_settings();
        $c = self::get_d1_creds();

        // Set the URL for the REST command to get our token.
        $url = "$s->wsurl/webservice/InternalViewREST/login?_type=json&username=$c->username&password=$c->password";

        // Set up the CURL handler.
        $curl = curl_init($url);

        // Set the CURL options.
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, false);

        // Grab the response.
        $response = curl_exec($curl);

        // Set the HTTP code for debugging.
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the curl handler.
        curl_close($curl);

        // Conditionally return the response.
        if ($status == 200) {
            return $response;
        } else if ($status == 400) {
           mtrace("Error: $status.\nError retreiving token.\nPlease check password for $c->username.");
           die();
        } else {
           mtrace("Error: $status.\nError retreiving token.\nPlease check the D1 webservice.");
           die();
        }
    }

    /**
     * Searches for the course section from the D1 web services.
     *
     * @return @string $token
     */
    public static function get_course_by($size, $type, $parm, $level, $optionalparms = null) {
        // Get the data needed.
        $s = self::get_d1_settings();

        // Set the URL for the post command to get a list of the courses matching the parms.
        $url = $s->wsurl . '/webservice/PublicViewREST/searchCourseSection?informationLevel=' . $level . '&locale=en_US&_type=json';

        // Set the POST body.
        $body = '{"searchCourseSectionProfileRequestDetail": {"paginationConstruct": {"pageNumber": "1","pageSize": "'. $size . '"},"courseSectionSearchCriteria": {"' . $type . '": "' . $parm . '"' . $optionalparms . '}}}';

        // Set the POST header.
        $header = array('Content-Type: application/json');

        // Set up the CURL handler.
        $curl = curl_init($url);

        // Se the CURL options.
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Grab the response.
        $json_response = curl_exec($curl);

        // Set the HTTP code for debugging.
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the CURL handler.
        curl_close($curl);

        // Decode the response.
        $response = json_decode($json_response);

        // Return the response.
        $response = isset($response->SearchCourseSectionProfileResult) ? $response->SearchCourseSectionProfileResult : $response;

        return $response;
    }

    public static function update_applicability($coursenumber) {
        // Get the data needed.
        $s = self::get_d1_settings();

        $token = self::get_token();

        // Set the URL for the post command to get a list of the courses matching the parms.
        $url = $s->wsurl . '/webservice/InternalViewREST/updateCourse?_type=json';

        // Set the POST body.
        $body = '{ "updateCourseRequestDetail": {  "course": {   "associationMode": "update",   "courseNumber": "' . $coursenumber . '",   "applicability": "Public"  } }}';

        // Set the POST header.
        $header = array('Content-Type: application/json',
                'sessionId:' . $token);

        // Set up the CURL handler.
        $curl = curl_init($url);

        // Se the CURL options.
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Grab the response.
        $json_response = curl_exec($curl);

        // Set the HTTP code for debugging.
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the CURL handler.
        curl_close($curl);

        // Decode the response.
        $response = json_decode($json_response);

        // Return the response.
        return $response;
    }

    public static function get_cs_objectid($coursenumber, $sectionnumber) {
        // Set the optional parameters to search for the section.
        $optionalparms = ', "advancedCriteria": {"sectionCode": "' . $sectionnumber . '"}';

        // Return the list of courses that match the course number and section number.
        $courses = self::get_course_by('250','courseCode', $coursenumber, 'Short', $optionalparms);

        if (!isset($courses->courseSectionProfiles->courseSectionProfile)) {
            mtrace("Course Section ID not found for $coursenumber - $sectionnumber. Trying to update its applicability to Public.");
            $updated = self::update_applicability($coursenumber);
            $updated = $updated->updateCourseResult;

            if (isset($updated->responseCode) && $updated->responseCode == "Success") {
                mtrace("Updated the aplpicability for $coursenumber to public. We will try to get the objectId for this course again in the next run.");
            } else {
                mtrace("We were unable to set the applicability for $coursenumber. Please check the course in D1.");
            }
            return null;
        }

        if (is_array($courses->courseSectionProfiles->courseSectionProfile)) {
            // Loop through the courses and find the one that matches EXACTLY.
            foreach ($courses->courseSectionProfiles->courseSectionProfile as $course) {

                // If we have a course to process.
                if (isset($course->associatedCourse)) {
                    // Find the exact course number match.
                    if ($course->associatedCourse->courseNumber == $coursenumber) {
                        // Set the course section object id.
                        $csobjectid = $course->objectId;
                        mtrace("Found the CS Objectid: $csobjectid for $coursenumber - $sectionnumber.");
                        break 1;
                    } else {
                        $csobjectid = null;
                        mtrace("Did not find the CS Objectid for $coursenumber - $sectionnumber.");
                        continue;
                    }
                }
            }
        } else {
            // Build anbd set the new object.
            $course = new stdClass();
            $course = $courses->courseSectionProfiles->courseSectionProfile;

            // If we have a course to process.
            if (isset($course->associatedCourse)) {
                // Find the exact course number match.
                if ($course->associatedCourse->courseNumber == $coursenumber) {
                    // Set the course section object id.
                    $csobjectid = $course->objectId;
                }
            } else {
                // Log the fact that we did not find a match.
                mtrace("No associated course section for $coursenumber" . "__" . "$sectionnumber.");
                // Set the object id as null so we can return it without error.
                $csobjectid = null;
            }
        }
        // Return the course section object id.
        return isset($csobjectid) ? $csobjectid : false;
    }

    /**
     * Grabs the D1 Course Name from the courseidnumber.
     *
     * @param  @string $courseidnumber
     * @return @string $coursename
     */
    public static function get_coursename($courseidnumber) {
        $pos = strpos($courseidnumber, '__');
        $coursename = substr($courseidnumber, 0, $pos);
        $coursename = trim($coursename);
        return $coursename;
    }

    /**
     * Grabs the D1 Course Section Number from the courseidnumber.
     *
     * @param  @string $courseidnumber
     * @return @string $coursesection
     */
    public static function get_mcoursesection($courseidnumber) {
        $coursesection = substr($courseidnumber, -3, 3);
        $coursesection = trim($coursesection);
        return $coursesection;
    }

    /**
     * Gets the daily grade postings array.
     *
     * @return @array of @objects $odl_dgps
     */
    public static function get_odl_dgps($unique, $courseidnumber=null, $limits=true) {
        global $DB;

        // Get the ODL course categories in settings.
        $ocats = get_config('local_d1', 'ocategories');

        // For getting courses, we only return distinct course records, otherwise we get all grade records.
        if ($unique) {
            $grouper = " GROUP BY coursenumber, sectionnumber";
        } else {
            $grouper = " GROUP BY coursenumber, sectionnumber, x_number, FinalLetterGrade";
        }

        // If a courseidnumber is present, only fetch enrollments for that course.
        if (isset($courseidnumber)) {
            $wheres = ' AND c.idnumber = "' . $courseidnumber . '"';
        } else {
            $wheres = '';
        }

        // Get the D1 settings data needed.
        $s = self::get_d1_settings();

        if (!$limits) {
           $s->odl_dp = time() / 86400;
        }

        // Build the PD sql for fetching the requested data.
        $sql = 'SELECT CONCAT(c.idnumber, " ", d1s.idnumber, " ", gg.finalgrade) AS uniqer,
                  ccx.name,
                  SUBSTRING_INDEX(c.idnumber, "__", 1) AS coursenumber,
                  SUBSTRING_INDEX(c.idnumber, "__", -1) AS sectionnumber,
                  d1s.idnumber AS x_number,
                  IF(ls.id IS NULL,
                    (SELECT DISTINCT(letter) FROM mdl_grade_letters WHERE contextid = 1 AND lowerboundary = (SELECT(MAX(gl1.lowerboundary)) FROM mdl_grade_letters gl1 WHERE 1 = gl1.contextid AND gg.finalgrade / gg.rawgrademax * 100 >= gl1.lowerboundary)),
                    (SELECT DISTINCT(letter) FROM mdl_grade_letters WHERE contextid = ctx.id AND lowerboundary = (SELECT(MAX(gl1.lowerboundary)) FROM mdl_grade_letters gl1 WHERE ctx.id = gl1.contextid AND gg.finalgrade / gg.rawgrademax * 100 >= gl1.lowerboundary))
                  ) AS FinalLetterGrade,
                  DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(sub.timemodified), @@GLOBAL.time_zone, "America/Chicago"), "%d %b %Y") AS FinalDate
                FROM mdl_course c
                  INNER JOIN mdl_course_categories ccx ON ccx.id = c.category
                  INNER JOIN mdl_enrol e ON e.courseid = c.id AND e.enrol = "d1"
                  INNER JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
                  INNER JOIN mdl_user u ON u.id = ue.userid
                  INNER JOIN mdl_enrol_d1_students d1s ON d1s.userid = u.id
                  INNER JOIN mdl_enrol_d1_enrolls d1e ON d1s.id = d1e.studentsid AND d1e.courseid = c.id
                  INNER JOIN mdl_assign a ON a.course = c.id
                  INNER JOIN mdl_assign_submission sub ON sub.assignment = a.id AND sub.userid = u.id AND sub.status = "submitted" AND sub.latest = 1
                  INNER JOIN mdl_context ctx ON ctx.instanceid = c.id AND ctx.contextlevel = "50"
                  INNER JOIN mdl_grade_letters gl2 ON gl2.contextid = 1
                  INNER JOIN mdl_grade_items gi ON gi.courseid = c.id AND gi.itemtype = "course"
                  INNER JOIN mdl_grade_grades gg ON gg.itemid = gi.id AND u.id = gg.userid
                  INNER JOIN mdl_course_completions cc ON cc.course = c.id AND cc.userid = u.id
                  LEFT JOIN mdl_grade_letters ls ON ls.contextid = ctx.id
                  LEFT JOIN mdl_user_info_data ud ON ud.userid = u.id AND ud.fieldid = 3 
                WHERE gg.finalgrade IS NOT NULL
                  AND gg.finalgrade >= 0
                  AND cc.timecompleted IS NOT NULL
                  AND (sub.timemodified > UNIX_TIMESTAMP() - (86400 * ' . $s->odl_dp .')
                  OR gg.timemodified > UNIX_TIMESTAMP() - (86400 * ' . $s->odl_dp .'))
                  AND cc.reaggregate = 0
                  AND (a.name = "Final Examination" OR a.name = "Final Examination Verification" OR a.name = "Final Exam" OR a.name = "Final Quiz" OR a.name = "Final Capstone" OR a.name = "Capstone" OR a.name = "Final Project" OR a.name LIKE "Final Exam Part %" OR a.name LIKE "Final Exam V%")
                  AND u.deleted = 0
                  AND c.category IN (' . $ocats . ')'
                  . $wheres . $grouper . '

                UNION

                SELECT CONCAT(c.idnumber, " ", d1s.idnumber, " ", gg.finalgrade) AS uniqer,
                  ccx.name,
                  SUBSTRING_INDEX(c.idnumber, "__", 1) AS coursenumber,
                  SUBSTRING_INDEX(c.idnumber, "__", -1) AS sectionnumber,
                  d1s.idnumber AS x_number,
                  IF(ls.id IS NULL,
                    (SELECT DISTINCT(letter) FROM mdl_grade_letters WHERE contextid = 1 AND lowerboundary = (SELECT(MAX(gl1.lowerboundary)) FROM mdl_grade_letters gl1 WHERE 1 = gl1.contextid AND gg2.finalgrade / gg2.rawgrademax * 100 >= gl1.lowerboundary)),
                    (SELECT DISTINCT(letter) FROM mdl_grade_letters WHERE contextid = ctx.id AND lowerboundary = (SELECT(MAX(gl1.lowerboundary)) FROM mdl_grade_letters gl1 WHERE ctx.id = gl1.contextid AND gg2.finalgrade / gg2.rawgrademax * 100 >= gl1.lowerboundary))
                  ) AS FinalLetterGrade,
                  DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(IF(qa.timefinish IS NULL, gg.timemodified, qa.timefinish)), @@GLOBAL.time_zone, "America/Chicago"), "%d %b %Y") AS FinalDate
                FROM mdl_course c
                  INNER JOIN mdl_course_categories ccx ON ccx.id = c.category
                  INNER JOIN mdl_course_categories cat ON cat.id = c.category
                  INNER JOIN mdl_course_completions cc ON cc.course = c.id
                  INNER JOIN mdl_user u ON cc.userid = u.id
                  INNER JOIN mdl_enrol_d1_students d1s ON d1s.userid = u.id
                  INNER JOIN mdl_enrol_d1_enrolls d1e ON d1s.id = d1e.studentsid AND d1e.courseid = c.id
                  INNER JOIN mdl_quiz q ON q.course = c.id
                  INNER JOIN mdl_grade_items gi ON gi.courseid = c.id AND gi.itemtype = "mod" AND gi.itemmodule = "quiz" AND gi.iteminstance = q.id
                  INNER JOIN mdl_grade_grades gg ON gg.itemid = gi.id AND gg.userid = u.id
                  INNER JOIN mdl_grade_items gi2 ON gi2.courseid = c.id AND gi2.itemtype = "course"
                  INNER JOIN mdl_grade_grades gg2 ON gg2.itemid = gi2.id AND gg2.userid = u.id
                  INNER JOIN mdl_context ctx ON ctx.instanceid = c.id AND ctx.contextlevel = "50"
                  LEFT JOIN mdl_grade_letters ls ON ls.contextid = ctx.id
                  LEFT JOIN mdl_quiz_attempts qa ON qa.quiz = q.id AND qa.userid = u.id AND qa.state = "finished"
                WHERE gi2.itemtype = "course"
                  AND gg2.finalgrade IS NOT NULL
                  AND gg.finalgrade >= 0
                  AND gg.finalgrade IS NOT NULL
                  AND gg2.finalgrade >= 0
                  AND ctx.contextlevel = "50"
                  AND cc.timecompleted IS NOT NULL
                  AND (qa.timefinish > UNIX_TIMESTAMP() - (86400 * ' . $s->odl_dp .')
                  OR gg.timemodified > UNIX_TIMESTAMP() - (86400 * ' . $s->odl_dp .'))
                  AND (q.name = "Final Examination" OR q.name = "Final Exam" OR q.name = "Final Examination Verification" OR q.name = "Final Quiz" OR q.name = "Final Capstone" OR q.name = "Capstone" OR q.name = "Final Project" OR q.name LIKE "Final Exam Part %" OR q.name LIKE "Final Exam V%")
                  AND u.deleted = 0
                  AND u.idnumber IS NOT NULL
                  AND u.idnumber <> ""
                  AND c.category IN (' . $ocats . ')'
                  . $wheres . $grouper;

        // Actually fetch the data.
        $odl_dgps = $DB->get_records_sql($sql);

        // Return the data.
        return $odl_dgps;
    }

    /**
     * Gets the daily grade postings array.
     *
     * @return @array of @objects $pd_dgps
     */
    public static function get_pd_dgps($unique, $courseidnumber=null, $limits=true) {
        global $DB;

        // Get the PD course categories in settings.
        $pcats = get_config('local_d1', 'pcategories');

        // For getting courses, we only return distinct course records, otherwise we get all grade records.
        if ($unique) {
            $grouper = " GROUP BY coursenumber, sectionnumber";
        } else {
            $grouper = " GROUP BY coursenumber, sectionnumber, x_number, FinalLetterGrade";
        }

        // If a courseidnumber is present, only fetch enrollments for that course.
        if (isset($courseidnumber)) {
            $wheres = ' AND c.idnumber = "' . $courseidnumber . '"';
        } else {
            $wheres = '';
        } 

        // Get the D1 settings data needed.
        $s = self::get_d1_settings();

        if (!$limits) {
           $s->pd_dp = time() / 86400;
        }

        // Build the PD sql for fetching the requested data.
        $sql = 'SELECT CONCAT(c.idnumber, " ", d1s.idnumber, " ", gg2.finalgrade) AS uniqer,
                  SUBSTRING_INDEX(c.idnumber, "__", 1) AS coursenumber,
                  SUBSTRING_INDEX(c.idnumber, "__", -1) AS sectionnumber,
                  d1s.idnumber AS x_number,
                  IF (((((gg1.finalgrade/gg1.rawgrademax) >= .70) AND (gg2.finalgrade/gg2.rawgrademax) >= .70)), "Pass", "Fail") AS FinalLetterGrade,
                  CONCAT(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(IF(cmc.timemodified > 0, cmc.timemodified, gg1.timemodified)), @@GLOBAL.time_zone, "America/Chicago"), "%d %b %Y")) AS FinalDate
                FROM mdl_course c
                 INNER JOIN mdl_context ctx ON ctx.instanceid = c.id AND ctx.contextlevel = "50"
                 INNER JOIN mdl_grade_items gi1 ON gi1.courseid = c.id
                 INNER JOIN mdl_grade_items gi2 ON gi2.courseid = c.id AND gi2.itemtype = "course"
                 INNER JOIN mdl_enrol e ON e.courseid = c.id AND e.enrol = "d1"
                 INNER JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
                 INNER JOIN mdl_grade_grades gg1 ON gg1.itemid = gi1.id
                 INNER JOIN mdl_grade_grades gg2 ON gg2.itemid = gi2.id
                 INNER JOIN mdl_user u ON ue.userid = u.id
                 INNER JOIN mdl_enrol_d1_students d1s ON d1s.userid = u.id
                 INNER JOIN mdl_enrol_d1_enrolls d1e ON d1s.id = d1e.studentsid AND d1e.courseid = c.id
                 INNER JOIN mdl_course_modules cm ON cm.course = c.id AND gi1.iteminstance = cm.instance
                 INNER JOIN mdl_modules m ON m.id = cm.module AND m.name = gi1.itemmodule
                 LEFT JOIN mdl_course_modules_completion cmc ON cmc.coursemoduleid = cm.id AND u.id = cmc.userid AND cmc.completionstate = 1
                 LEFT JOIN mdl_grade_letters ls ON ls.contextid = ctx.id
                 LEFT JOIN mdl_user_info_data uid ON uid.userid = u.id AND uid.fieldid = 3
               WHERE u.id = gg1.userid
                 AND u.id = gg2.userid
                 AND c.fullname NOT LIKE "Master %"
                 AND gg1.finalgrade IS NOT NULL
                 AND (gi1.itemname = "Final Examination" OR gi1.itemname = "Final Exam" OR gi1.itemname = "Final Quiz" OR gi1.itemname = "Final Capstone" OR gi1.itemname = "Capstone" OR gi1.itemname = "Final Project" OR gi1.itemname LIKE "Final Exam Part %")
                 AND c.shortname NOT LIKE "LCCON.Online%"
                 AND c.shortname NOT LIKE "LCRES.Online%"
                 AND ((gg1.finalgrade/gg1.rawgrademax) >= .70)
                 AND ((gg2.finalgrade/gg2.rawgrademax) >= .70)
                 AND ((cmc.timemodified > ' . 'UNIX_TIMESTAMP() - (86400 * ' . $s->pd_dp .'))
                   OR (gg1.timemodified > ' . 'UNIX_TIMESTAMP() - (86400 * ' . $s->pd_dp .'))
                   OR (gg2.timemodified > ' . 'UNIX_TIMESTAMP() - (86400 * ' . $s->pd_dp .'))
                 )
                 AND c.category IN (' . $pcats . ')'
                 . $wheres . $grouper;

        // Actually fetch the data.
        $pd_dgps = $DB->get_records_sql($sql);

        // Return the data.
        return $pd_dgps;
    }

    /**
     * Returns the course type.
     *
     * @param  @object $course
     * @return @string pd, odl, or other
     */
    public static function pd_odl($course) {
        // Get the ODL course categories in settings.
        $ocats = get_config('local_d1', 'ocategories');
        $ocats = explode(',', $ocats);

        // Get the PD course categories in settings.
        $pcats = get_config('local_d1', 'pcategories');
        $pcats = explode(',', $pcats);

        if (in_array($course->category, $ocats)) {
            return "odl";
        } else if (in_array($course->category, $pcats)) {
            return "pd";
        } else {
            return "other";
        }
    }

    /**
     * Posts grades for course completions for a single course.
     *
     * @param  @array of @objects $dgps
     * @return @bool  $response
     */
    public static function course_grade_postings($postings) {
        // Get the token for later.
        $token   = self::get_token();

        // Build the return array.
        $pgs     = array();

        // Set the counter at 0.
        $counter = 0;
        $counter2 = 0;

        // Loop through the daily grade postings array.
        foreach ($postings as $posting) {

            // Get a new token every 100 rows.
            if ($counter2 % 100 == 0) {
                $token = self::get_token();
                mtrace("Got new token: $token.");
            }

            // If we do not have a course section object id.
            if (!isset($posting->csobjectid)) {

                // Get and set the coruse section object id from D1 to the posting.
                $posting->csobjectid = self::get_cs_objectid($posting->coursenumber, $posting->sectionnumber);
            }

            mtrace("    Posting $posting->finallettergrade with date of $posting->finaldate for student $posting->x_number.");

            // Post the grade.
            $post = self::post_update_grade($token, $posting->x_number, $posting->csobjectid, $posting->finallettergrade, $posting->finaldate);

            // Depending on the return from the D1 Webservice, log and set the returner appropriately.
            if (isset($post->createOrUpdateStudentFinalGradeResult)) {

                // Build the returner object from the posting and post objects.
                $returner = (object) array_merge((array) $posting, (array) $post->createOrUpdateStudentFinalGradeResult);
                mtrace("      " . $post->createOrUpdateStudentFinalGradeResult->responseCode);
                mtrace("    Posted \"$posting->finallettergrade\" for $posting->x_number with status of \"" . $post->createOrUpdateStudentFinalGradeResult->status . "\".\n");
            } else {

                // Get rid of some extraneous BS that D1 sends.
                $errmsg = trim(trim($post->SRSException->message, "["), "]");

                // Build the returner object from the posting and post objects.
                $returner = (object) array_merge((array) $posting, (array) $post->SRSException);
                mtrace("      " . $errmsg);
                mtrace("    Unable to post grade for $posting->x_number due to the error: $errmsg.\n");
            }

            // Merge the returner into the array with the approriate count.
            $pgs[$counter] = $returner;

            // Increment the couneter.
            $counter++;
            $counter2++;
        }

        // Return the array of postings and statuses.
        return $pgs;
    }

    /**
     * Post / updates a grade for a user in a course.
     *
     * @param  @string $token
     * @param  @string $stunumber // X Number.
     * @param  @int    $csobjectid // Course objectId.
     * @param  @string $grade
     * @param  @string $date
     *
     * @return @bool   $response
     */
    public static function post_update_grade($token, $stunumber, $csobjectid, $grade, $date) {
        // Get the data needed.
        $s = self::get_d1_settings();

        // Set the URL for the post command to post a grade for a student
        $url = $s->wsurl . '/webservice/InternalViewRESTV2/createOrUpdateStudentFinalGrade?_type=json';

        // Set the POST body.
        $body = '{"createOrUpdateStudentFinalGradeRequestDetail":
                 {"studentGrade": {
                  "completionDate": "' . $date . '",
                  "isInstructorApproved": "Yes",
                  "isProgramApproved": "Yes",
                  "isRegistrarApproved": "Yes",
                  "isCertificateRequirementsMet": "Yes",
                  "gradingSheet": 
                 {"courseSectionProfile":
                 {"objectId": "' . $csobjectid . '"}},"student":
                 {"personNumber": "' . $stunumber . '"},"studentGradeItems":
                 {"studentGradeItem": {"grade": "' . $grade . '"}}}}}';

        // Set the POST header.
        $header = array('Content-Type: application/json',
                'sessionId:' . $token);

        // Set up the CURL handler.
        $curl = curl_init($url);

        // Set the CURL options.
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Grab the response.
        $json_response = curl_exec($curl);

        // Set the HTTP code for debugging.
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the CURL handler.
        curl_close($curl);

        // Decode the response.
        $response = json_decode($json_response);

        // Return the response.
        return $response;
    }
}
