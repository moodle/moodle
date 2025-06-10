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
 * @package    enrol_d1
 * @copyright  2022 onwards LSUOnline & Continuing Education
 * @copyright  2022 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lsud1 {

    /**
     * Checks if an email is in a domain.
     *
     * @return @object $s
     */
    public static function email_in_domain($email, $domain) {
        $email_domain = explode('@', $email)[1];
        return $email_domain === $domain;
    }

    /**
     * Grabs D1 Webseervice settings.
     *
     * @return @object $s
     */
    public static function get_d1_settings() {
        global $CFG;

        // Build the object.
        $s = new stdClass();
        // Get the debug file storage location.
        $s->debugfiles = get_config('enrol_d1', 'debugfiles');
        // Get the Moodle data root.
        $s->dataroot   = $CFG->dataroot;
        // Get the DestinyOne webservice url prefix.
        $s->wsurl      = $CFG->d1_wsurl;
        // Determine if we should do our extra debugging.
        $s->debugging  = get_config('enrol_d1', 'debuextra') == 1
                         && $CFG->debugdisplay == 1 ? 1 : 0;

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
        $c->username   = get_config('enrol_d1', 'username');
        // Get the webservice password.
        $c->password   = get_config('enrol_d1', 'password');
        // Get the debug file storage location.

        return $c;
    }

    /**
     * Grabs http status code key value pairs.
     *
     * @return @array
     */
    public static function get_codes() {
        // Create the array.
        $http_status_codes = array(100 => "Continue",
                                   101 => "Switching Protocols",
                                   102 => "Processing",
                                   200 => "OK",
                                   201 => "Created",
                                   202 => "Accepted",
                                   203 => "Non-Authoritative Information",
                                   204 => "No Content",
                                   205 => "Reset Content",
                                   206 => "Partial Content",
                                   207 => "Multi-Status",
                                   300 => "Multiple Choices",
                                   301 => "Moved Permanently",
                                   302 => "Found",
                                   303 => "See Other",
                                   304 => "Not Modified",
                                   305 => "Use Proxy",
                                   306 => "(Unused)",
                                   307 => "Temporary Redirect",
                                   308 => "Permanent Redirect",
                                   400 => "Bad Request",
                                   401 => "Unauthorized",
                                   402 => "Payment Required",
                                   403 => "Forbidden",
                                   404 => "Page Not Found",
                                   405 => "Method Not Allowed",
                                   406 => "Not Acceptable",
                                   407 => "Proxy Authentication Required",
                                   408 => "Request Timeout",
                                   409 => "Conflict",
                                   410 => "Gone",
                                   411 => "Length Required",
                                   412 => "Precondition Failed",
                                   413 => "Request Entity Too Large",
                                   414 => "Request-URI Too Long",
                                   415 => "Unsupported Media Type",
                                   416 => "Requested Range Not Satisfiable",
                                   417 => "Expectation Failed",
                                   418 => "I'm a teapot",
                                   419 => "Authentication Timeout",
                                   422 => "Unprocessable Entity",
                                   423 => "Locked",
                                   424 => "Failed Dependency",
                                   424 => "Method Failure",
                                   425 => "Unordered Collection",
                                   426 => "Upgrade Required",
                                   428 => "Precondition Required",
                                   429 => "Too Many Requests",
                                   431 => "Request Header Fields Too Large",
                                   444 => "No Response",
                                   449 => "Retry With",
                                   450 => "Blocked by Windows Parental Controls",
                                   451 => "Unavailable For Legal Reasons",
                                   494 => "Request Header Too Large",
                                   495 => "Cert Error",
                                   496 => "No Cert",
                                   497 => "HTTP to HTTPS",
                                   499 => "Client Closed Request",
                                   500 => "Internal Server Error",
                                   501 => "Not Implemented",
                                   502 => "Bad Gateway",
                                   503 => "Service Unavailable",
                                   504 => "Gateway Timeout",
                                   505 => "HTTP Version Not Supported",
                                   506 => "Variant Also Negotiates",
                                   507 => "Insufficient Storage",
                                   508 => "Loop Detected",
                                   509 => "Bandwidth Limit Exceeded",
                                   510 => "Not Extended",
                                   511 => "Network Authentication Required",
                                   598 => "Network read timeout error",
                                   599 => "Network connect timeout error"
        );
        return $http_status_codes;
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

        if ($s->debugging == 1) {
            // If there is a location set, use it.
            $loc = isset($s->debugfiles) ? $s->debugfiles : $s->dataroot;

            // Write this out to a file for debugging.
            $fp = fopen($loc . '/token.txt', 'w');
            fwrite($fp, $response);
            fclose($fp);
        }

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
     * Gets a list of courses that fall within the approved categories.
     *
     * @return @arrayy of @objects $courses
     */
    public static function get_courses($categories, $courseid=null) {
        global $CFG, $DB;

        // Categories are stored as CSV, so create an array of them.
        $cats = explode(',', $categories);

        // Build the empty courses array.
        $courses = array();

        // Loop through each category and grab courses that match.
        foreach ($cats as $cat) {
            // Set the select requirement.
            if (isset($courseid)) {
            $select = 'id = ' . $courseid . ' AND category = ' . $cat . ' AND '
                      . $DB->sql_like('idnumber', ':pattern', false);
            } else {
            $select = 'category = ' . $cat . ' AND '
                      . $DB->sql_like('idnumber', ':pattern', false);
            }

            // Set the params.
            $params = ['pattern' => '%\_\_%'];

            // Get the course list.
            $ccourses = $DB->get_records_select('course', $select, $params);

            // If we return some data, aggregate it into a new array.
            if (!empty($ccourses && count($cats) > 1)) {
                // Merge the contents of the ccourses array into the courses one.
                $courses = array_merge($courses, $ccourses);
            } else if (!empty($ccourses)) {
                // We only have one course category, set it appropriately.
                $courses = $ccourses;
            }
        }

        // Return the courses array.
        return $courses;
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

        if ($s->debugging == 1) {
            // Build the name of the course JSON.
            $str1 = $type;
            $str2 = $parm;
            $pattern = '/ /i';
            $stype = preg_replace($pattern, '_', $str1);
            $sparm = preg_replace($pattern, '_', $str2);

            // Write this out to a file for debugging.
            $fp = fopen($stype . '-' . $sparm . '.json', 'w');
            fwrite($fp, $json_response);
            fclose($fp);
        }

        return $response;
    }

    /**
     * Grabs the D1 ObjectID for the courseSection from the studentListItem.
     *
     * @param  @array of @objects $enrollments
     * @return @string $sectionid
     */
    public static function get_section_objectid($enrollments) {
        $sectionid = $enrollments->getClassListResult->courseSectionProfile->objectId;
        return $sectionid;
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
        return $coursesection;
    }

    /**
     * Grabs the studentListItems.
     *
     * @param  @array of @objects $enrollments
     * @return @array of @objects $students
     */
    public static function get_studentenrollments($enrollments) {
        $students = $enrollments->getClassListResult->studentListItems;
        return $students;

    }

    /**
     * Grabs an array of courseids to be processed.
     *
     * @return @array of @objects $courseids
     */
    public static function get_courseids_arr($categories) {
        global $CFG, $DB;

        // Build the table name.
        $etable = $CFG->prefix . 'enrol_d1_enrolls';
        $ctable = $CFG->prefix . 'course';

        // Build the SQL.
        $sql = 'SELECT d1e.courseidnumber AS courseidnumber, d1e.courseid AS courseid'
               . ' FROM ' . $etable . ' d1e'
               . ' INNER JOIN ' . $ctable . ' c'
               . ' ON c.id = d1e.courseid'
               . ' WHERE c.category IN(' . $categories . ')'
               . ' GROUP BY d1e.courseidnumber, d1e.courseid';

        // Get the list of qualifying course ids.
        $courseids = $DB->get_records_sql($sql);

        // Return the array.
        return $courseids;
    }

    /**
     * Master function to grab the D1 Student Enrollments for a given course.
     *
     * @param  @string $token
     * @param  @object $course
     * @return @array of @objects $enrolledstus
     */
    public static function set_student_enrollments($token, $course) {
        // Short circuit things if we don't have a token.
        if ($token == false) {
            return;
        }

        // Get the course name from the idnumber.
        $cn = self::get_coursename($course->idnumber);
        // mtrace("  Got coursename $cn from $course->idnumber.");

        // Get the course section number from the idnumber.
        $cs = self::get_mcoursesection($course->idnumber);
        // mtrace("  Got section $cs from $course->idnumber.");

        // Grab the enrollments based on the given info.
        $enrollments = self::get_section_enrollment($token, $cn, $cs);

        // If we have enrollments, start building the array of enrollment objects.
        if (!empty($enrollments->getClassListResult->studentListItems)) {
            // Set the sectionid.
            $sectionid = self::get_section_objectid($enrollments);

            // Get the section data based on the sectionid above.
            $section   = self::get_coursesection($token, $sectionid);

            // Grab the student enrollments subobjects.
            $students  = self::get_studentenrollments($enrollments);

            // Loop through the students and grab the studentlistitems.
            $enrolledstus = array();

            $tokentime = microtime(true);

            foreach ($students as $slis) {

                // If our token is older than 600 seconds, get a new one and reset the timer.
                if (microtime(true) - $tokentime > 600) {
                    mtrace("Expiring token: $token in students as slis foreach.");
                    $token = self::get_token();
                    $tokentime = microtime(true);
                    mtrace("We fetched a new token: $token.");
                }

                // If there is more than one, D1 returns an array, if 1, returns a subobject.
                $sli = is_array($slis) ? $slis : array($slis);

                // Loop through the studentlistitems.
                foreach ($sli as $studentlistitem) {

                    // If our token is older than 600 seconds, get a new one and reset the timer.
                    if (microtime(true) - $tokentime > 600) {
                        mtrace("Expiring token: $token in sli as studentlistitem foreach.");
                        $token = self::get_token();
                        $tokentime = microtime(true);
                        mtrace("We fetched a new token: $token.");
                    }

//                    mtrace("    Getting student info for $studentlistitem->studentNumber.");

                    // Get the username for this student.
                    $username = self::get_username($token, $studentlistitem->studentId);

                    // Set the usernamed variable for future use depending on what came back.
                    $usernamed = isset($username->username) ? $username->username : $username->getStudentResult->student->loginId;

                    if (isset($username->getStudentResult)) {
                        // mtrace("    Retreived remote username: " . $usernamed . " for $studentlistitem->studentNumber.");
                    } else if (isset($username->username)) {
                        // mtrace("    Retreived local username: " . $usernamed . " for $studentlistitem->studentNumber.");
                    } else {
                        mtrace("    Failed to retreive username for $studentlistitem->studentNumber.");
                    }

                    // Convert the start date to unix timestamp.
                    $sd = DateTime::createFromFormat('d M Y h:i:s A', $studentlistitem->enrollmentDate, new DateTimeZone('America/Chicago'));
                    $startdate = date_timestamp_get($sd);

                    // Conditionally get the end date if it's set.
                    if (isset($section->getCourseSectionResult->courseSection->sectionDueDateRule->daysAfterEnroll) && $section->getCourseSectionResult->courseSection->sectionDueDateRule->dueDateRuleTypeCode == "DaysAfterEnrollment") {
                        // Grab the number of days the section enrollment is valid.
                        $daysafter = $section->getCourseSectionResult->courseSection->sectionDueDateRule->daysAfterEnroll;
                        // Grab the grace period.
                        $graceperiod = $section->getCourseSectionResult->courseSection->sectionDueDateRule->gracePeriodDays;
                        // Convert it to seconds.
                        $gp = 86400 * $graceperiod;

                        // Grab the student enrollment start date.
                        $sdate = $studentlistitem->enrollmentDate;

                        // Create the date based on the start date.
                        $date = date_create("$sdate");

                        // Add the $daysafter to the start date.
                        date_add($date,date_interval_create_from_date_string($daysafter . " days"));

                        // Format the date as requested.
                        $edate = date_format($date,"d M Y h:i:s A");

                        // Set up the date to convert it to unix timestamp.
                        $ed = DateTime::createFromFormat('d M Y h:i:s A', $edate, new DateTimeZone('America/Chicago'));

                        // In case we do not have a sectionDueDate, set it to the calculated end-date.
                        $dd = isset($studentlistitem->sectionDueDate) ?
                              DateTime::createFromFormat('d M Y h:i:s A', $studentlistitem->sectionDueDate, new DateTimeZone('America/Chicago')) :
                              $ed;

                        // Convert the date to unix timestamp.
                        $enddate = date_timestamp_get($ed) + 86399 + $gp;
                        $duedate = date_timestamp_get($dd) + 86399 + $gp;

                    } else if (isset($section->getCourseSectionResult->courseSection->sectionDueDateRule->fixedDueDate) && $section->getCourseSectionResult->courseSection->sectionDueDateRule->dueDateRuleTypeCode == "FixDate") {
                        // Grab the grace period.
                        $graceperiod = $section->getCourseSectionResult->courseSection->sectionDueDateRule->gracePeriodDays;
                        // Convert it to seconds.
                        $gp = 86400 * $graceperiod;

                        // Grab the fixed due date of the course section.
                        $edate = $section->getCourseSectionResult->courseSection->sectionDueDateRule->fixedDueDate;

                        // Build the date object from the string above.
                        $ed = DateTime::createFromFormat('d M Y', $edate, new DateTimeZone('America/Chicago'));

                        // In case we do not have a sectionDueDate, set it to the calculated end-date.
                        $dd = isset($studentlistitem->sectionDueDate) ?
                              DateTime::createFromFormat('d M Y h:i:s A', $studentlistitem->sectionDueDate, new DateTimeZone('America/Chicago')) :
                              $ed;

                        // Convert the date to unix timestamp.
                        $enddate = date_timestamp_get($ed) + 86399 + $gp;
                        $duedate = date_timestamp_get($dd) + 86399 + $gp;

                    } else {
                        $enddate = 0;

                        // In case we do not have a sectionDueDate, set it to the calculated end-date.
                        $dd = isset($studentlistitem->sectionDueDate) ?
                              DateTime::createFromFormat('d M Y h:i:s A', $studentlistitem->sectionDueDate, new DateTimeZone('America/Chicago')) :
                              null;

                        $duedate = isset($studentlistitem->sectionDueDate) ? date_timestamp_get($dd) + 86399 : 0;
                    }

                    $lsuid = isset($studentlistitem->studentScoolPersonnelNumber) ? $studentlistitem->studentScoolPersonnelNumber : '';

                    if ($lsuid == 'null' || $lsuid == '' || is_null($lsuid)) {
                        $lsuid = '';
                        // mtrace("    No lsuid found for $studentlistitem->studentNumber.");
                    } else {
                        // mtrace("    Retreived lsuid: $lsuid for $studentlistitem->studentNumber.");
                    }


                    // Build the enrolled object for this student.
                    $enrolled = new stdClass();
                    $enrolled->section      = $course->idnumber;
                    $enrolled->firstname    = $studentlistitem->studentFirstName;
                    $enrolled->lastname     = $studentlistitem->studentLastName;
                    $enrolled->d1id         = $studentlistitem->studentId;
                    $enrolled->xnumber      = $studentlistitem->studentNumber;
                    $enrolled->lsuid        = $lsuid;
                    $enrolled->username     = $usernamed;
                    $enrolled->email        = $studentlistitem->studentPreferredEmail;
                    $enrolled->enrollstart  = $studentlistitem->enrollmentDate;
                    $enrolled->startstamp   = $startdate;
                    $enrolled->enrollend    = isset($edate) ? $edate : null;
                    $enrolled->endstamp     = $enddate;
                    $enrolled->duedate      = isset($studentlistitem->sectionDueDate) ? $studentlistitem->sectionDueDate : null;
                    $enrolled->duestamp     = $duedate;
                    $enrolled->enrollstatus = $studentlistitem->enrollmentStatus;

                    // Populate the array.
                    $enrolledstus[]         = $enrolled;
                }
            }
        } else {
            $enrolledstus[] = null;
        }

        return $enrolledstus;
    }

    /**
     * Grabs the section enrollment from D1 web services.
     *
     * @param  @string $token
     * @param  @string $course
     * @param  @string $section
     * @return @array of @objects $response
     */
    public static function get_section_enrollment($token, $course, $section) {
        // Short circuit things if we don't have a token.
        if ($token == false) {
            return;
        }
        mtrace("  Getting section enrollment for $course - $section.");

        // Get the data needed.
        $s = self::get_d1_settings();

        // Set the URL.
        $url = $s->wsurl . '/webservice/InternalViewRESTV2/getClassList?_type=json&overrideMaxResults=Y&sessionId=' . $token;

        // Set the POST body.
        $body = '{"getClassListRequestDetail": {"overrideMaxResults": "Y", "courseNumber": "' . $course . '","sectionNumber": "' . $section . '"}}';

        // Set the POST header.
        $header = array('Content-Type: application/json',
                'sessionId: ' . $token);

        $curl = curl_init($url);

        // Set the CURL options.
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Gett the JSON response.
        $json_response = curl_exec($curl);

        // Set the HTTP code for debugging.
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the CURL handler.
        curl_close($curl);

        // Decode the response.
        $response = json_decode($json_response);

        // Count these things if they exist and are countable.
        if (is_array(isset($response->getClassListResult->studentListItems->studentListItem))) {
            $rcount = count($response->getClassListResult->studentListItems->studentListItem);
            mtrace("  Got $rcount enrollments from $course - $section.");
        }

        if ($s->debugging == 1) {
            // Build the name of the course JSON.
            $str1 = $course;
            $str2 = $section;
            $pattern = '/ /i';
            $stype = preg_replace($pattern, '_', $str1);
            $sparm = preg_replace($pattern, '_', $str2);

            // Write this out to a file for debugging.
            $fp = fopen($stype . '-' . $sparm . '.json', 'w');
            fwrite($fp, $json_response);
            fclose($fp);
        }

        // Return the response.
        return($response);
    }

    /**
     * Grabs the course section for a given sectionid from the D1 web services.
     *
     * @param  @string $token
     * @param  @string $sectionid
     * @return @object $response
     */
    public static function get_coursesection($token, $sectionid) {
        // Short circuit things if we don't have a token.
        if ($token == false) {
            return;
        }

        // Get the data needed.
        $s = self::get_d1_settings();

        // Set the url. 
        $url = $s->wsurl . '/webservice/InternalViewRESTV2/courseSection/objectId/' . $sectionid . '?_type=json&informationLevel=Full&locale=en_US';

        // Set the get header.
        $header = array('Content-Type: application/json', 'sessionId: ' . $token);

        // Initiate the curl handler.
        $curl = curl_init($url);

        // Se the CURL options.
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Get the response.
        $json_response = curl_exec($curl);

        // Set the HTTP code for debugging.
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the curl handler.
        curl_close($curl);

        // Decode the response.
        $response = json_decode($json_response);

        if ($s->debugging == 1) {
            // Write this out to a file for debugging.
            $fp = fopen('D1_Section-' . $sectionid . '.json', 'w');
            fwrite($fp, $json_response);
            fclose($fp);
        }

        // Return the response.
        return $response;
    }

    /**
     * Grabs the username for a given user from the D1 web services.
     *
     * @param  @string $token
     * @param  @string $d1id
     * @return @object $user
     */
    public static function get_local_user($d1id) {
        global $DB;

        // Set the table.
        $table = 'enrol_d1_students';

        // Build the parm.
        $parm = array('d1id' => $d1id);

        // Get the data.
        $username = $DB->get_record($table, $parm);

        // Return the d1 local user object.
        return $username;
    }

    /**
     * Grabs the username for a given user from the D1 web services.
     *
     * @param  @string $token
     * @param  @string $d1id
     * @return @object $response
     */
    public static function get_username($token, $d1id) {
        // Short circuit things if we don't have a token.
        if ($token == false) {
            return;
        }

        // This short circuits things and speds it up, but we cannot gaurantee this.
        $localuser = lsud1::get_local_user($d1id);
        if (isset($localuser->username) && $localuser) {
            return $localuser;
        }

        // Get the data needed.
        $s = self::get_d1_settings();

        // Set the URL.
        $url = $s->wsurl . '/webservice/InternalViewRESTV2/student/objectId/' . $d1id . '?_type=json&informationLevel=full';

        // Set the header.
        $header = array('Content-Type: application/json', 'sessionId: ' . $token);

        // Initiate the curl handler.
        $curl = curl_init($url);

        // Se the CURL options.
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Get the response.
        $json_response = curl_exec($curl);

        // close the curl handler.
        curl_close($curl);

        // Decode the response.
        $response = json_decode($json_response);

        if ($s->debugging == 1) {
            // Write this out to a file for debugging.
            $fp = fopen('D1_User-' . $d1id . '.json', 'w');
            fwrite($fp, $json_response);
            fclose($fp);
        }

        // Return the response.
        return $response;
    }

    /**
     * Populates the D1 student table.
     *
     * @param  @object $enrollment
     * @return @object $response
     */
    public static function populate_stu_db($enrollment) {
        global $DB;

        // Set up the needed vars.
        $table  = 'enrol_d1_students';
        $sort   = '';
        $fields = '*';
        $wheres = "userid IS NULL";

        // Build the student object.
        $student = new stdClass();
        $student->username     = strtolower($enrollment->username);
        $student->idnumber     = $enrollment->xnumber;
        $student->d1id         = $enrollment->d1id;
        $student->lsuid        = $enrollment->lsuid;
        $student->email        = strtolower($enrollment->email);
        $student->firstname    = $enrollment->firstname;
        $student->lastname     = $enrollment->lastname;
        $student->timemodified = time();

        // Build the conditions to get some users.
        $uconditions = array("username" => $student->username);
        $iconditions = array("idnumber" => $student->idnumber);
        $dconditions = array("d1id"     => $student->d1id);
        $econditions = array("email"    => $student->email);
        if ($student->lsuid <> '') {
            $lconditions = array("lsuid"    => $student->lsuid);
        }

        $numsql = 'SELECT * FROM {enrol_d1_students} stu
                   WHERE stu.username = "' . $student->username . '"
                   OR stu.idnumber = "' . $student->idnumber . '"
                   OR stu.d1id = "' . $student->d1id . '"
                   OR stu.email = "' . $student->email . '"';

        $numreturn = $DB->get_records_sql($numsql);

        // Set up the users array and grab users if they exist.
        $existing = array();
        $existing['d1id']     = $DB->get_record($table, $dconditions, $fields='*', $strictness=IGNORE_MISSING);
        $existing['email']    = $DB->get_record($table, $econditions, $fields='*', $strictness=IGNORE_MISSING);
        $existing['idnumber'] = $DB->get_record($table, $iconditions, $fields='*', $strictness=IGNORE_MISSING);
        $existing['username'] = $DB->get_record($table, $uconditions, $fields='*', $strictness=IGNORE_MISSING);

        if (count($numreturn) > 1) {
            mtrace("*** Dupe user: $student->d1id, $student->idnumber, $student->username, $student->email in $table, skipping.");
            return $student;
        }

        // Loop through this funky stuff and update user info as needed.
        foreach ($existing as $key => $u0) {
            // If we have a matching user, upddate them or grab the user object.
            if (isset($u0->id)) {
                // Set this so we can update the user entry if needed.
                $student->id = $u0->id;
                // Now we can compare username, idnumber, email, first and last names.
                // mtrace("    User: $student->username with matching $key exsits from search.");
                if (strtolower($student->username) == strtolower($u0->username)
                  && $student->idnumber == $u0->idnumber
                  && strtolower($student->email) == strtolower($u0->email)
                  && $student->d1id == $u0->d1id
                  && strtolower($student->firstname) == strtolower($u0->firstname)
                  && strtolower($student->lastname) == strtolower($u0->lastname)) {
                    // mtrace("      User: $student->username matched all parts of the existing user object.");
                    // User object matches stored data, grab the object.
                    $uo = $DB->get_record($table, array("id"=>$student->id), $fields='*', $strictness=IGNORE_MISSING);
                    break 1;
                } else {
                    // Try to update the user object.
                    try {
                        $result = $DB->update_record($table, $student, $bulk=false);
                        if (!$result) {
                            throw new Exception('Failed to update record');
                        }
                    } catch (Exception $e) {
                        error_log($e->getMessage());
                        mtrace("Error updating $table with error: $e->getMessage().");
                    }

                    if ($result) {
                        $uo = $DB->get_record($table, array("id"=>$student->id), $fields='*', $strictness=IGNORE_MISSING);
                        break 1;
                    } else {
                        mtrace("      Failed to update user: $user->id with username: $uo->username, idnumber: $uo->idnumber, email: $uo->email, and name: $uo->firstname $uo->lastname due to a DB error.");
                        break 1;
                    }
                }
            }
        }
        // If we have a user object, return it.
        if (isset($uo->id)) {
            // Return the user object.
            return $uo;
        } else {
            // We do not have a matching or existing user, please create one.
            // mtrace("      New user will be created with username: $student->username, idnumber: $student->idnumber, fn: $student->firstname, ln: $student->lastname.");
            $id = $DB->insert_record($table, $student, $returnid=true, $bulk=false);
            // mtrace("        Created new user with username: $student->username, idnumber: $student->idnumber, fn: $student->firstname, ln: $student->lastname.");

            // Grab the newly created user object and return it.
            $uo = $DB->get_record($table, array("id"=>$id), $fields='*', $strictness=IGNORE_MISSING);

            // Return the user object.
            return $uo;
        }
    }

    /**
     * Sets enroll statuses prior to running enrollment for the same course/section.
     *
     * @param  @object $enrollment
     * @return @bool
     */
    public static function insert_or_update_enrolldb($enrollment) {
    global $CFG, $DB;

    // Build the table names.
    $etable = 'enrol_d1_enrolls';
    $stable = 'enrol_d1_students';

    // SQL for checking to see if we have a full enrollment record.
    $sql = 'SELECT e.id, e.studentsid FROM '
           . $CFG->prefix . $etable
           . ' e INNER JOIN '
           . $CFG->prefix . $stable
           . ' s ON e.studentsid = s.id'
           . ' WHERE e.courseidnumber = "'
           . $enrollment->section
           . '" AND s.d1id = "'
           . $enrollment->d1id
           . '" AND s.idnumber = "'
           . $enrollment->xnumber
           . '" AND s.email = "'
           . $enrollment->email . '"';

    // Get both the enrollment and student record id.
    $eid = $DB->get_record_sql($sql);

    // If we don't have the student id, get it.
    if (!isset($eid->studentsid)) {
        // Set the requirements for grabbing the studentsid.
        $reqs = array(
                    'd1id' => $enrollment->d1id,
                    'idnumber' => $enrollment->xnumber,
                    'username' => $enrollment->username,
                    'email' => $enrollment->email
                );

        // Grab the student id.
        $sid       = $DB->get_record($stable, $reqs, $fields = 'id');

        // Set it so we're not dealing with an object.
        if (isset($sid->id)) {
            $studentid = $sid->id;
        } else {
            return;
        }
    } else {
        // Set it so we're not dealing with an object.
        $studentid = $eid->studentsid;
    }

    // Gett eh duedate config setting.
    $ddc = get_config('enrol_d1', 'duedate');

    // Build the data array.
    $data = array();

    // Made sure we set enrollend properly.
    if ($ddc == 0) {
        // Set this to the calculated end timestamp.
        $data['enrollend'] = $enrollment->endstamp;
    } else if ($ddc == 1) {
        // Set this to the D1 provided end timestamp.
        $data['enrollend'] = $enrollment->duestamp;
    } else {
        $data['enrollend'] = 0;
    }

    // Set the rest of the data for enrollment.
    $data['studentsid']     = $studentid;
    $data['courseidnumber'] = $enrollment->section;
    $data['status']         = 'enroll';
    $data['enrollstart']    = $enrollment->startstamp;
    $data['timemodified']   = time();

    // Set the search requirements.
    $ereqs = array('studentsid' => $studentid, 'courseidnumber' => $enrollment->section);

    // If we already have an enrollment id OR are able to set one.
    if (isset($eid->id) || $eid = $DB->get_record($etable, $ereqs, $fields = 'id')) {
        $data['id'] = $eid->id;
        // mtrace("      We found an existing enrollment, update it.");

        // Update the enrollment record.
        $updated = $DB->update_record($etable, $data);
    } else {
       // mtrace("      We found a new enrollemt, add it to the table.");

       // Insert the enrollment record.
       $inserted = $DB->insert_record($etable, $data, $returnid=true);
    }

    // Set the return result based on what we have.
    $result = isset($updated) ? $eid->id : $inserted;

    // Return the id of the inserted or updated row.
    return $result;
    }

    /**
     * Sets enroll statuses prior to running enrollment for the same course/section.
     *
     * @param  @int    $courseid
     * @param  @int    $userid
     * @param  @string $enrollstatus
     * @return @bool
     */
    public static function updated1db($courseid, $userid, $enrollstatus) {
    global $CFG, $DB;

    // Build the table name.
    $etable = $CFG->prefix . 'enrol_d1_enrolls';
    $stable = $CFG->prefix . 'enrol_d1_students';

    // Generate the SQL.
    $sql = 'UPDATE '
           . $etable
           . ' d1e INNER JOIN '
           . $stable
           . ' d1s ON d1s.id = d1e.studentsid'
           . ' SET d1e.status = "'
           . $enrollstatus . 'ed'
           . '", d1e.timemodified = UNIX_TIMESTAMP()'
           . ' WHERE d1e.courseid = '
           . $courseid
           . ' AND d1s.userid = '
           . $userid;

    // Do the nasty.
    $result = $DB->execute($sql);

    // Return success or failure.
    return $result;
    }

    /**
     * Gets courseidnumbers without course ids from the d1 enroll table.
     *
     * @return @int $courseidnumbers
     */
    public static function get_courseidnumbers_noid() {
        global $DB;

        // Build the table name.
        $table  = 'enrol_d1_enrolls';

        // Set the fields.
        $fields = 'courseidnumber';

        // Set the wehres.
        $wheres = "courseid IS NULL GROUP BY $fields";

        // Get the course object.
        $cidns = $DB->get_records_select($table, $wheres, null, $sort='', $fields);

        // Set this array up.
        $courseidnumbers = array();

        // Build a boring array.
        foreach ($cidns as $cidn) {
            $courseidnumbers[] = $cidn->courseidnumber;
        }

        // Return the courseidnumbers.
        return $courseidnumbers;
    }

    /**
     * Sets matching courseid where we have a corresponding course idnumber.
     *
     * @param  @string $courseidnumber
     * @return @bool
     */
    public static function set_courseids($courseidnumber) {
        global $CFG, $DB;

        // Get the courseid.
        $courseid = self::get_courseid($courseidnumber);

        // Build the table name.
        $d1table = $CFG->prefix . 'enrol_d1_enrolls';

        // Generate the SQL.
        $sql = 'UPDATE '
           . $d1table
           . ' SET courseid = "'
           . $courseid
           . '" WHERE courseidnumber = "'
           . $courseidnumber . '"';

        // Do the nasty.
        if ($courseid > 0) {
            $result = $DB->execute($sql);
        } else {
            $result = false;
        }

        // Return success or failure.
        return $result;
    }

    /**
     * Gets matching courseid where we have a corresponding course idnumber.
     *
     * @param  @int $courseidnumber
     * @return @int $courseid
     */
    public static function get_courseid($courseidnumber) {
        global $DB;

        // Build the table name.
        $table = 'course';

        // Set the conditions.
        $conditions = 'idnumber = "' . $courseidnumber . '"';

        // Get the course object.
        $course = $DB->get_record_select($table, $conditions, null, $fields='*', $strictness=IGNORE_MISSING);

        // Set the id.
        $courseid = isset($course->id) ? $course->id : 0;

        // Return the courseid.
        return $courseid;
    }

    /**
     * Gets the list of students in the d1 students table.
     *
     * @return @array of @objects
     */
    public static function get_d1students($all, $courseid=null) {
        global $CFG, $DB;

        // Set up the needed vars.
        if ($all == true && !is_null($courseid)) {
            $wheres = "d1e.courseid = $courseid";
        } else if ($all == false && is_null($courseid)) {
            $wheres = "d1s.userid IS NULL";
        } else {
            $wheres = is_null($courseid) ? "1" : "d1e.courseid = $courseid";
        }
         
        // Build the SQL.
        $sql = 'SELECT *
                FROM mdl_enrol_d1_students d1s
                INNER JOIN mdl_enrol_d1_enrolls d1e ON d1s.id = d1e.studentsid 
                WHERE ' . $wheres;

        // Get the course object.
        $d1students = $DB->get_records_sql($sql);

        // Return the data
        return $d1students;
    }

    /**
     * Creates or updates users as needed.
     *
     * @param  @object $d1student
     * @return @object $uo
     */
    public static function create_update_user($d1student, $courseid=null) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->libdir . '/moodlelib.php');

        // Grab the table for future use.
        $table = 'user';

        // Build the auth methods and choose the default one.
        $auth = explode(',', $CFG->auth);
        $auth = reset($auth);

        // TODO: deal with this somehow.
        $auth = 'manual';


        // Set up the user object.
        $user               = new stdClass();
        $user->username     = $d1student->username;
        $user->idnumber     = $d1student->idnumber;
        $user->email        = $d1student->email;
        $user->firstname    = $d1student->firstname;
        $user->lastname     = $d1student->lastname;
        $user->lang         = $CFG->lang;
        $user->auth         = $auth;
        $user->confirmed    = 1;
        $user->timemodified = time();
        $user->mnethostid   = $CFG->mnet_localhost_id;

        if (isset($d1student->userid)) {
            $mconditions = array("id" => $d1student->userid, "mnethostid" => 1, "deleted" => 0);
            $uo = $DB->get_record($table, $mconditions, $fields='*', $strictness=IGNORE_MISSING);
            if (isset($uo->id)) {
                // mtrace("We already have a matching user in Moodle for $d1student->username - $uo->username, skipping");
                return $uo;
            }
        }

        // Build the conditions to get some users.
        $uconditions = array("username"=>$d1student->username, "mnethostid"=>1, "deleted"=>0, "confirmed"=>1, "suspended"=>0);
        $iconditions = array("idnumber"=>$d1student->idnumber, "mnethostid"=>1, "deleted"=>0, "confirmed"=>1, "suspended"=>0);
        $econditions = array("email"=>$d1student->email, "mnethostid"=>1, "deleted"=>0, "confirmed"=>1, "suspended"=>0);

        // Set up the users array and grab users if they exist.
        $users = array();

        // mtrace("Checking and updating record for user: $d1student->username, $d1student->idnumber, $d1student->email.");

        // Get users that match the email address provided above.
        $users['email']    = $DB->get_records($table, $econditions, 'id', $fields='*');


        // If we have more than one user returned for an email address, so some stuff.
        if (is_array($users['email']) && count($users['email']) > 1) {
            mtrace('Found more than one user for email: ' . $d1student->email);

            // Loop through these users to provide more info for the logs.
            foreach($users['email'] as $idn) {

                // If we're running via scheduled task.
                if (!isset($courseid)) {
                  // mtrace('  Email: ' . $d1student->email . ' = Username: ' . $idn->username . ' = ID: ' . $idn->id);
                }
            }

            // We cannot automatically update multiple users with a single email. Unset them.
            unset($users['email']);
        } else {
            $users['email'] = reset($users['email']);
        }

        $users['username'] = $DB->get_record($table, $uconditions, $fields='*', $strictness=IGNORE_MISSING);
        $users['idnumber'] = $DB->get_records($table, $iconditions, 'id', $fields='*');
        if (is_array($users['idnumber']) && count($users['idnumber']) > 1) {
            if (!isset($courseid)) {
                mtrace('Found more than one user for idnumber: ' . $d1student->idnumber);
            }
            foreach($users['idnumber'] as $idn) {
                if (!isset($courseid)) {
                    // mtrace('  IDNumber: ' . $d1student->idnumber . ' = Username: ' . $idn->username . ' = ID: ' . $idn->id);
                }
            }
            unset($users['idnumber']);
        } else {
            $users['idnumber'] = reset($users['idnumber']);
        }
        $users['email']    = $DB->get_records($table, $econditions, 'id', $fields='*');
        if (is_array($users['email']) && count($users['email']) > 1) {
            if (!isset($courseid)) {
                mtrace('Found more than one user for email: ' . $d1student->email);
            }

            foreach($users['email'] as $idn) {
                mtrace('  Email: ' . $d1student->email . ' = Username: ' . $idn->username . ' = ID: ' . $idn->id);
            }
            unset($users['email']);
        } else {
            $users['email'] = reset($users['email']);
        }

        // Loop through this funky stuff and update user info as needed.
        foreach ($users as $key => $u0) {
            // If we have a matching user, upddate them or grab the user object.
            if (isset($u0->id)) {
                // Set this so we can update the user entry if needed.
                $user->id = $u0->id;
                // Now we can compare username, idnumber, email, first and last names.
                if (!isset($courseid)) {
                    // mtrace("    User: $user->username with matching $key exsits from search.");
                }
                if (strtolower($user->username) == strtolower($u0->username)
                 && $user->idnumber == $u0->idnumber
                 && strtolower($user->email) == strtolower($u0->email)
                 && strtolower($user->firstname) == strtolower($u0->firstname)
                 && strtolower($user->lastname) == strtolower($u0->lastname)) {
                    // mtrace("    User ID: $user->id matched all parts of the Moodle user object.");
                    // User object matches stored data 1:1, grab the object.
                    $uo = $DB->get_record($table, array("id"=>$user->id), $fields='*', $strictness=IGNORE_MISSING);
		    $d1student->userid = $uo->id;
		    $d1student->id = $d1student->studentsid;
		    $DB->update_record('enrol_d1_students', $d1student);

/*
mtrace("did not touch user: $uo->id from d1 record: $d1student->id and userid $d1student->userid.");
var_dump($d1student);
die();
*/

                    break;
                } else {
                    unset($user->auth);
                    // User object matches stored data but some differences exist, try to update the object.
                    if ($DB->update_record($table, $user, $bulk=false)) {
                        // mtrace("    Updated Moodle user with username: $user->username, idnumber: $user->idnumber, email: $user->email, and name: $user->firstname $user->lastname.");
                        // We successfully updated the user object and stored the data, fetch the data.
                        $uo = $DB->get_record($table, array("id"=>$user->id), $fields='*', $strictness=IGNORE_MISSING);
                        $d1student->userid = $uo->id;
                        $d1student->id = $d1student->studentsid;
/*
mtrace("Updated user: $uo->id from d1 record: $d1student->id and userid $d1student->userid.");
var_dump($d1student);
die();
*/
                        $DB->update_record('enrol_d1_students', $d1student);
                        break;
                    } else {
                        mtrace("    Failed to update Moodle user: $user->id with username: $user->username, idnumber: $user->idnumber, email: $user->email, and name: $user->firstname $user->lastname due to a DB error.");
                        break;
                    }
                }
                // Update the userid of the d1student table JIC.
                $d1student->userid = $uo->id;
                $d1student->id = $d1student->studentsid;

mtrace("Updated d1 record: $d1student->id and userid $d1student->userid.");
var_dump($d1student);
die();

                $DB->update_record('enrol_d1_students', $d1student);
                // If we found a user and did all the above, exit the loop.
                break;
            } else {
                continue;
            }
        }
        // If we have a user object, return it. Otherwise create a new user.
        if (isset($uo->id)) {
            return $uo;
        } else {
            // Check if we're dealing with an LSU user.
            $lsuuser = self::email_in_domain($user->email, 'lsu.edu');

            // Just in case.
            $user->username = strtolower($user->username);

            // Set this to make sure passwords get generated below.
            $user->password = 'to be generated';

            // Only set passwords for non-LSU users.
            if ($lsuuser) {
                // This is how Moodle stores this.
                $user->password = 'not cached';
                // Set the auth appropriately.
                $user->auth = 'oauth2';
            }

            // Create the user.
            $id = user_create_user($user, false, false);

            // Only set passwords for non-LSU users.
            if (!$lsuuser) {
                set_user_preference('auth_forcepasswordchange', 1, $id);
                set_user_preference('create_password', 1, $id);
            }

            mtrace("Created userid: $id,  username: $user->username, idnumber: $user->idnumber, fn: $user->firstname, ln: $user->lastname.");

            // Grab the newly created user object and return it.
            $uo = $DB->get_record($table, array("id"=>$id), $fields='*', $strictness=IGNORE_MISSING);

            // Update the userid of the d1student table JIC.
            $d1student->userid = $uo->id;
            $d1student->id = $d1student->studentsid;

/*
mtrace("created user: $uo->id from d1 record: $d1student->id and userid $d1student->userid.");
var_dump($d1student);
die();
*/

            $DB->update_record('enrol_d1_students', $d1student);
            return $uo;
        }
    }

    /**
     * Get the actionable d1 enrollment data.
     *
     * @param  @string $courseid
     * @return @array of @objects $enrolldata
     */
    public static function get_d1enrolls($courseid) {
    global $CFG, $DB;

    // Build the table name.
    $etable = $CFG->prefix . 'enrol_d1_enrolls';
    $stable = $CFG->prefix . 'enrol_d1_students';

    // Generate the SQL.
    $sql = 'SELECT d1e.id, d1s.userid, d1e.courseid, '
           . ' d1e.courseidnumber, d1e.status, '
           . ' d1e.enrollstart, d1e.enrollend '
           . ' FROM ' . $etable
           . ' d1e INNER JOIN ' . $stable
           . ' d1s ON d1s.id = d1e.studentsid '
           . ' WHERE d1s.userid IS NOT NULL AND d1e.courseid = "'
           . $courseid . '"'
           . ' AND d1e.status IN ("enroll", "unenroll")';

    // Do the nasty.
    $enrolldata = $DB->get_records_sql($sql);

    // Return success or failure.
    return $enrolldata;
    }

    /**
     * Sets enroll statuses prior to running enrollment for the same course/section.
     *
     * @param  @string $courseidnumber
     * @param  @string $enrollstatus
     * @return @bool
     */
    public static function prestage_drops($courseidnumber, $enrollstatus) {
    global $CFG, $DB;

    // Build the table name.
    $table = $CFG->prefix . 'enrol_d1_enrolls';

    // Generate the SQL.
    $sql = 'UPDATE '
           . $table
           . ' SET status = "'
           . $enrollstatus
           . '" WHERE courseidnumber = "'
           . $courseidnumber . '"';

    // Do the nasty.
    $result = $DB->execute($sql);

    // Return success or failure.
    return $result;
    }

    /**
     * Enrolls a student and sets the appropriate status and time.
     *
     * @param  @int    $courseid
     * @param  @int    $userid
     * @param  @string $enrollstatus
     * @param  @int    $time
     * @return @object $status
     */
    public static function d1_enrollment($courseid, $userid, $enrollstatus, $enrollstart, $enrollend) {
        global $CFG, $DB;

        // Instantiate the enroller.
        $enroller = new enrol_d1;

        // Grab the role id if one is present, otherwise use the Moodle default.
        $roleid = isset($CFG->enrol_d1_studentrole) ? $CFG->enrol_d1_studentrole : 5;

        // Set the time in seconds from epoch.
        $time = time();

        // Add or remove this student or teacher to the course...
        $stu = new stdClass();
        $stu->userid = $userid;
        $stu->enrol = 'd1';
        $stu->course = $courseid;
        $stu->time = $time;
        $stu->timemodified = $time;

        // Set this up for getting the enroll instance.
        $etable      = 'enrol';
        $econditions = array('courseid' => $courseid, 'enrol' => $stu->enrol);

        // Get the enroll instance.
        $einstance   = $DB->get_record($etable, $econditions);

        // Set this up for getting the course.
        $ctable      = 'course';
        $cconditions = array('id' => $courseid);

        // Get the course object.
        $course      = $DB->get_record($ctable, $cconditions);

        // If we do not have an existing enrollment instance, add it.
        if (empty($einstance)) {
            $enrollid = $enroller->add_instance($course);
            $einstance = $DB->get_record('enrol', array('id' => $enrollid));
            // mtrace("    Enroll instance for $einstance->enrol with ID: $einstance->id created.");
        } else {
            // mtrace("    Existing enrollment instance for $einstance->enrol with ID: $einstance->id already here.");
        }

        // Determine if we're removing or suspending oa user on unenroll.
        $unenroll = get_config('enrol_d1', 'unenroll');

        // If we're unenrolling a student.
        if ($enrollstatus == "unenroll") {
            // If we're removing them from the course.
            if ($unenroll == 1) {
                // Do the nasty.
                $enrolluser   = $enroller->unenrol_user(
                                    $einstance,
                                    $stu->userid);
                mtrace("User $stu->userid unenrolled from course: $stu->course.");
            // Or we're suspending them.
            } else {
                // Do the nasty.
                $enrolluser   = $enroller->update_user_enrol(
                                    $einstance,
                                    $stu->userid, ENROL_USER_SUSPENDED);
                mtrace("    User ID: $stu->userid suspended from course: $stu->course.");
            }
        // If we're enrolling a student in the course.
        } else if ($enrollstatus == "enroll") {
            // Check to see if a user is enrolled via D1.
            $check = self::check_d1_enr($stu->course, $stu->userid);

            // Set the start date if it's there.
            $enrollstart = isset($enrollstart) ? $enrollstart : 0;

            // Set their end date if it's there.
            $enrollend = isset($enrollend) ? $enrollend : 0;

            if (!isset($check->enrollid)) {
                // Do the nasty.
                $enrolluser = $enroller->enrol_user(
                              $einstance,
                              $stu->userid,
                              $roleid,
                              $enrollstart,
                              $enrollend,
                              $status = ENROL_USER_ACTIVE);
                mtrace("    User ID: $stu->userid enrolled into course: $stu->course.");

                // Require check once.
                if (!function_exists('grade_recover_history_grades')) {
                    global $CFG;
                    require_once($CFG->libdir . '/gradelib.php');
                }

                $grade = grade_recover_history_grades($userid, $courseid);
                if ($grade) {
                    mtrace("      Grades recovered for userid: $userid in course: $courseid.");
                }
            } else if ($check->enrollend <> $enrollend) {
                $enrolluser = $enroller->enrol_user(
                              $einstance,
                              $stu->userid,
                              $roleid,
                              $enrollstart,
                              $enrollend,
                              $status = ENROL_USER_ACTIVE);
                mtrace("    User ID: $stu->userid enddate modified in course: $stu->course.");
            } else {
                mtrace("    Skipping user: $stu->userid already enrolled in course: $stu->course.");
            }

        }

        // Update the D1 enrollment table with the appropriate action status.
        $updated1db = self::updated1db($courseid, $userid, $enrollstatus);

        // TODO: Log some errors and return them.
        // Return some stuff.
        return true;
    }

    /**
     * Updates ODL course idnumbers.
     *
     * @return @array of @array of @objects $response
     */
    public static function update_odl_idnumbers($categories) {
        global $CFG, $DB;

        // Create an array of categories.
        $cats = explode(',', $categories);

        // Loop through the course categories.
        foreach ($cats as $cat) {
            if ($cat == '') {
                continue;
            }

            mtrace("Processing courses in category id: $cat.");

            $sql = 'SELECT * FROM mdl_course c WHERE c.category = ' . $cat . ' AND c.idnumber NOT LIKE "%\_\_%"';
            $courses = $DB->get_records_sql($sql);

            if (empty($courses)) {
                continue;
            }

            // Set the conditions required to get courses.
            // $conditions = array('category'=>$cat);

            // Get the courses in this category.
            // $courses = $DB->get_records('course', $conditions, 'id', $fields='*');

            $tokentime = microtime(true);

            // Loop through these courses.
            foreach($courses as $course) {
                mtrace("  Processing course $course->shortname.");

                // If our token is older than 600 seconds, get a new one and reset the timer.
                if (microtime(true) - $tokentime > 600) {
                    mtrace("Expiring token: $token in courses as course foreach.");
                    $token = self::get_token();
                    $tokentime = microtime(true);
                    mtrace("We fetched a new token: $token.");
                }

                // Get the course name.
                if (substr($course->shortname, 0, 3) == 'CM ') {
                    $coursename = substr($course->shortname, 0, 7);
                    $coursename = trim($coursename);
                } else {
                    $coursename = substr($course->shortname, 0, 9);
                    $coursename = trim($coursename);
                }

                // mtrace("    We found $course->shortname and created $coursename from it.");

                // Set the optional params to query by the custom section number.
                $optionalparms = ', "advancedCriteria": {"customSectionNumber": "' . trim($course->shortname) . '"}';

                // Get the course info from the webservice.
                $c = self::get_course_by('250','courseCode', $coursename, 'Short', $optionalparms);

                // Make sure we have data.
                if (!isset($c->courseSectionProfiles)) {
                    // mtrace("    We do not have a matching course for $course->shortname, skipping.");
                    continue;
                }

                // If we have too much data, filter to get an exact match.
                if (is_array($c->courseSectionProfiles->courseSectionProfile)) {
                    foreach ($c->courseSectionProfiles->courseSectionProfile as $csps) {
                        if ($csps->associatedCourse->courseNumber == $coursename) {
                            // mtrace("    We found a matching D1 course section for $course->shortname.");
                            $csp = $csps;
                        }
                    }
                } else {
                    // We have just the right amount of data.
                    $csp = $c->courseSectionProfiles->courseSectionProfile;
                }

                // Set the coursenumber in the course object.
                $course->coursenumber = $csp->associatedCourse->courseNumber;

                // Set the sectionnumber in the course object.
                $course->sectionnumber = $csp->code;

                // Set the course objectid in the course object.
                $course->objectid = $csp->objectId;

                // mtrace("    We fetched $course->coursenumber - $course->sectionnumber with objectId: $course->objectid from the webservice.");

                // Build the data object as required to update the course record.
                $do             = array();
                $do['id']       = $course->id;
                $do['idnumber'] = $course->coursenumber . '__' . $course->sectionnumber;
                if (isset($course->sectionnumber) && $course->idnumber != $course->coursenumber . '__' . $course->sectionnumber) {
                    $updated    = $DB->update_record('course', $do);
                    // mtrace("    We updated the $course->coursenumber" . "__" . "$course->sectionnumber idnumber for courseid: $course->id.");
                } else if (isset($course->sectionnumber) && $course->idnumber == $course->coursenumber . '__' . $course->sectionnumber) {
                    // mtrace("    We skipped courseid: $course->id due to matching idnumbers.");
                } else {
                    $do['idnumber'] = $course->shortname;
                    $updated   = $DB->update_record('course', $do);
                    // mtrace("    We set the idnumber for courseid: $course->id to match the shortname.");
                }

                // Build the updated array of courses.
                $ca[] = isset($course) ? $course : null;
                mtrace("  Completed processing course $course->shortname.");
            }

            // Build an array of course arrays.
            $cas[] = isset($ca) ? $ca : null;
            mtrace("Finished processing courses in category id: $cat.");
        }
        // Return the array of arrays of objects.
        $cas = isset($cas) ? $cas : null;
        return($cas);
    }

    /**
     * Updates PD course idnumbers.
     *
     * @return @array of @array of @objects $response
     */
    public static function update_pd_idnumbers($categories) {
        global $CFG, $DB;

        // Create an array of categories.
        $cats = explode(',', $categories);

        // Loop through the course categories.
        foreach ($cats as $cat) {
            if ($cat == '') {
                continue;
            }

            mtrace("Processing courses in category id: $cat.");

            $sql = 'SELECT * FROM mdl_course c WHERE c.category = ' . $cat . ' AND c.idnumber NOT LIKE "%\_\_%"';
            $courses = $DB->get_records_sql($sql);
            if (empty($courses)) {
                continue;
            }

            // Set the conditions required to get courses.
            // $conditions = array('category'=>$cat);

            // Get the courses in this category.
            // $courses = $DB->get_records('course', $conditions, 'id', $fields='*');

            $tokentime = microtime(true);

            // Loop through these courses.
            foreach($courses as $course) {

                // If our token is older than 600 seconds, get a new one and reset the timer.
                if (microtime(true) - $tokentime > 600) {
                    mtrace("Expiring token: $token in 2nd courses as course foreach.");
                    $token = self::get_token();
                    $tokentime = microtime(true);
                    mtrace("We fetched a new token: $token.");
                }

                // Set the course shortname variable to the expected value.
                $cs  = preg_match('/([A-Z][A-Z][A-Z][A-Z][A-Z])\.\(\d+\)*/', $course->shortname, $match);
                // Do the above for Geaux For Free courses.
                $cs2  = preg_match('/(POGF[0-9]+)\.\(\d+\)/', $course->shortname, $match2);
                // Get the standard shortname.
                $css = substr($course->shortname, 0, 5);
                // Get the 2 digit Geaux For Free shortname.
                $css2 = substr($course->shortname, 0, 6);

                if (!isset($match[1]) && !isset($match2[1])) {
                    mtrace("  skipping non-conforming course $css - $course->shortname.");
                    continue;
                } else if (isset($match[1]) && $css == $match[1]) {
                    $coursename = $match[1];
                    $coursename = trim($coursename);
                    mtrace("  Processing course $course->shortname - $css = $match[1].");
                } else if (isset($match2[1]) && ($css == $match2[1] || $css2 == $match2[1])) {
                    $coursename = $match2[1];
                    $coursename = trim($coursename);
                    mtrace("  Processing course $course->shortname - $css or $css2 = $match2[1].");
                } else {
                    mtrace("  skipping non-matching course $css or $css2 <> $match[1] or $match2[1].");
                    continue;
                }

                mtrace("    We found $course->shortname and created $coursename from it.");

                if (strlen($course->shortname) > 20) {
                    $course->shortname = substr($course->shortname, 0, 9);
                }

                // Set the optional params to query by the custom section number.
                $optionalparms = ', "advancedCriteria": {"customSectionNumber": "' . trim($course->shortname) . '"}';

                // Get the course info from the webservice.
                $c = self::get_course_by('250','courseCode', $coursename, 'Short', $optionalparms);

                if (!isset($c->courseSectionProfiles)) {
                    mtrace("    We do not have a matching course for $course->shortname, skipping.");
                    continue;
                }

                if (is_array($c->courseSectionProfiles->courseSectionProfile)) {
                    foreach ($c->courseSectionProfiles->courseSectionProfile as $csps) {
                        if ($csps->associatedCourse->courseNumber == $coursename) {
                            mtrace("    We found a matching D1 course section for $course->shortname.");
                            $csp = $csps;
                        }
                    }
                } else {
                    $csp = $c->courseSectionProfiles->courseSectionProfile;
                }

                // Set the coursenumber in the course object.
                $course->coursenumber = $csp->associatedCourse->courseNumber;

                // Set the sectionnumber in the course object.
                $course->sectionnumber = $csp->code;

                // Set the course objectid in the course object.
                $course->objectid = $csp->objectId;

                mtrace("    We fetched $course->coursenumber - $course->sectionnumber with objectId: $course->objectid from the webservice.");

                // Build the data object as required to update the course record.
                $do             = array();
                $do['id']       = $course->id;
                $do['idnumber'] = $course->coursenumber . '__' . $course->sectionnumber;
                if (isset($course->sectionnumber) && $course->idnumber != $course->coursenumber . '__' . $course->sectionnumber) {
                    $updated    = $DB->update_record('course', $do);
                    mtrace("    We updated the $course->coursenumber" . "__" . "$course->sectionnumber idnumber for courseid: $course->id.");
                } else if (isset($course->sectionnumber) && $course->idnumber == $course->coursenumber . '__' . $course->sectionnumber) {
                    mtrace("    We skipped courseid: $course->id due to matching idnumbers.");
                } else {
                    $do['idnumber'] = $course->shortname;
                    $updated   = $DB->update_record('course', $do);
                    mtrace("    We set the idnumber for courseid: $course->id to match the shortname.");
                }

                // Build the updated array of courses.
                $ca[] = isset($course) ? $course : null;
                mtrace("  Completed processing course $course->shortname.");
            }

            // Build an array of course arrays.
            $cas[] = isset($ca) ? $ca : null;
            mtrace("Processing courses in category id: $cat.");
        }
        // Return the array of arrays of objects.
        $cas = isset($cas) ? $cas : null;
        return($cas);
    }

    /**
     * Grabs missing Moodle idnumbers.
     *
     * @param @string $startstring
     * @param @int $fieldid
     *
     * @return @array of @objects
     */
    public static function get_missing_idnumbers($startstring, $fieldid) {
        // Set the global so we can use Moodle's DB functionality.
        global $DB;

        // Build the SQL.
        $sql = 'SELECT u.id AS userid,
                            d1s.lsuid AS idnumber
                        FROM mdl_user u
                            INNER JOIN mdl_enrol_d1_students d1s ON u.id = d1s.userid AND d1s.lsuid LIKE "' . $startstring . '%"
                            LEFT JOIN mdl_user_info_data uid ON d1s.userid = uid.userid
                                AND u.id = uid.userid
                                AND uid.fieldid = ' . $fieldid . '
                        WHERE uid.id IS NULL';

        // Fetch the data.
        $data = $DB->get_records_sql($sql);

        // Return the data.
        return $data;
    }

    /**
     * Gets the Moodle idnumber for a specifiv user and field.
     *
     * @param @int $userid
     * @param @int $fieldid
     *
     * @return @object
     */
    public static function get_moodle_idnumber($userid, $fieldid) {
        // Set the global so we can use Moodle's DB functionality.
        global $DB;

        // Set the table name.
        $table = 'user_info_data';

        // Build the conditions array.
        $conditions = array();
        $conditions['userid'] = $userid;
        $conditions['fieldid'] = $fieldid;

        // Fetch the data.
        $data = $DB->get_record($table, $conditions, $fields='*', $strictness=IGNORE_MISSING);

        // Return the data.
        return $data;
    }


    /**
     * Updates profile field idnumbers that differ from D1s.
     *
     * @param @int $fieldid
     *
     * @return @bool
     */
    public static function update_moodle_idnumbers($fieldid) {
        // Set the global so we can use Moodle's DB functionality.
        global $DB;

        // Build the SQL to update differing idnumbers.
        $sql = 'UPDATE mdl_user u
                            INNER JOIN mdl_enrol_d1_students d1s ON u.id = d1s.userid AND d1s.lsuid LIKE "89%"
                            INNER JOIN mdl_user_info_data uid ON u.id = uid.userid
                                AND d1s.lsuid != uid.data
                                AND uid.fieldid = ' . $fieldid . '
                        SET uid.data = d1s.lsuid';

        // Execute the SQL.
        $idnumbers = $DB->execute($sql);

        // Return true or false.
        return $idnumbers;
    }

    /**
     * Updates the profile field idnumber as specified.
     *
     * @param @int $dataid
     * @param @string $idnumber
     *
     * @return @bool
     */
    public static function update_moodle_idnumber($dataid, $idnumber) {
        // Set the global so we can use Moodle's DB functionality.
        global $DB;

        // Set the table name.
        $table = 'user_info_data';

        // Build the data object.
        $dataobject = new stdClass();
        $dataobject->id = $dataid;
        $dataobject->data = $idnumber;

        // Update the data.
        $data = $DB->update_record($table, $dataobject, $bulk=false);

        // Return if we were successful or not.
        return $data;
    }

    /**
     * Inserts the profile field idnumber as specified.
     *
     * @param @int $userid
     * @param @int $fieldid
     * @param @string $idnumber
     *
     * @return @int
     */
    public static function insert_moodle_idnumber($userid, $fieldid, $idnumber) {
        // Set the global so we can use Moodle's DB functionality.
        global $DB;

        // Set the table name.
        $table = 'user_info_data';

        // Build the data object.
        $dataobject = new stdClass();
        $dataobject->userid = $userid;
        $dataobject->fieldid = $fieldid;
        $dataobject->data = $idnumber;
        $dataobject->dataformat = 0;

        // Update the data.
        $data = $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);

        // Return the user_info_data.id if we were successful.
        return $data;
    }

    public static function check_d1_enr($courseid, $userid, $enrollend = null) {
        global $DB;

        if (is_null($enrollend)) {
            $where = "";
        } else {
            $where = "AND ue.timeend = ' . $enrollend . '";
        }

        $sql = 'SELECT ue.id AS enrollid,
                       ue.timeend AS enrollend
                FROM mdl_course c
                    INNER JOIN mdl_enrol e ON e.courseid = c.id
                    INNER JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
                WHERE e.enrol = "d1"
                    AND ue.status = 0
                    AND c.id = ' . $courseid . '
                    ' . $where . '
                    AND ue.userid = ' . $userid;

        $d1enrolled = $DB->get_record_sql($sql);
        return $d1enrolled;
    }

    public static function d1_lowercase() {
        global $DB;
        $sql = "UPDATE mdl_user
                 SET email = lower(email),
                   username = lower(username)
               WHERE deleted = 0
                 AND suspended = 0";

        $lower = $DB->execute($sql);

        return $lower;
    }
}


/**
 * LSU DestinyOne webservice enrollment plugin.
 *
 */
class enrol_d1 extends enrol_plugin {

    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array $fields instance fields
     * @return int id of new instance, null if can not be created
     */
    public static function add_enroll_instance($course) {
        return $instance;
    }
}
