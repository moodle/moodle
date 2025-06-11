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
 * WDS Post Grades utility class.
 *
 * @package    block_wds_postgrades
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_wds_postgrades;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/report/lib.php');

/**
 * Utility class for WDS Post Grades block operations.
 */
class wdspg {

    /**
     * Record a successful final grade posting.
     *
     * @param object $grade The grade object containing student and grade information.
     * @param int $courseid The course ID.
     * @param int $sectionid The section ID.
     * @param int $userid The student's user ID.
     * @return bool Success status.
     */
    public static function record_posted_grade($grade, $courseid, $sectionid, $userid) {
        global $DB, $USER;

        // Only track final grades
        if (empty($grade->gradetype) || $grade->gradetype !== 'final') {
            return false;
        }

        // Check if this grade was already posted.
        $conditions = [
            'sectionid' => $sectionid,
            'universal_id' => $grade->universal_id
        ];

        if ($DB->record_exists('block_wds_postgrades_posts', $conditions)) {

            // Already exists, update it.
            $record = $DB->get_record('block_wds_postgrades_posts', $conditions);
            $record->grade_id = $grade->grade_id;
            $record->grade_display = $grade->grade_display;
            $record->posted_by = $USER->id;
            $record->timecreated = time();

            return $DB->update_record('block_wds_postgrades_posts', $record);
        } else {

            // Create new record.
            $record = new \stdClass();
            $record->courseid = $courseid;
            $record->sectionid = $sectionid;
            $record->universal_id = $grade->universal_id;
            $record->grade_id = $grade->grade_id;
            $record->grade_display = $grade->grade_display;
            $record->userid = $userid;
            $record->posted_by = $USER->id;
            $record->timecreated = time();

            return $DB->insert_record('block_wds_postgrades_posts', $record) ? true : false;
        }
    }

    /**
     * Get previously posted grades for a section.
     *
     * @param int $sectionid The section ID.
     * @return array Array of posted grade records.
     */
    public static function get_posted_grades($sectionid) {
        global $DB;

        $sql = "SELECT
            p.*,
            u.firstname,
            u.lastname,
            u2.firstname AS poster_firstname,
            u2.lastname AS poster_lastname
            FROM {block_wds_postgrades_posts} p
            INNER JOIN {user} u ON u.id = p.userid
            INNER JOIN {user} u2 ON u2.id = p.posted_by
            WHERE p.sectionid = :sectionid
            ORDER BY p.timecreated DESC";

        return $DB->get_records_sql($sql, ['sectionid' => $sectionid]);
    }

    /**
     * Check if a student's grade has already been posted.
     *
     * @param int $sectionid The section ID.
     * @param string $universalid The student's universal ID.
     * @return object|false The posted grade record or false if not found.
     */
    public static function check_grade_posted($sectionid, $universalid) {
        global $DB;

        $conditions = [
            'sectionid' => $sectionid,
            'universal_id' => $universalid
        ];

        return $DB->get_record('block_wds_postgrades_posts', $conditions);
    }

    /**
     * Extended version of post_grades_with_method that records successful postings.
     *
     * @param array $grades Array of grade objects to be posted.
     * @param string $gradetype Type of grades being posted ('final' or 'interim').
     * @param string $sectionlistingid The Workday Section Listing ID for the course section.
     * @param int $courseid The course ID.
     * @param int $sectionid The section ID.
     * @return object Results object containing successes and failures.
     */
    public static function post_grades_with_method_extended($grades, $gradetype, $sectionlistingid, $courseid, $sectionid) {

        // Call the original method.
        $results = self::post_grades_with_method($grades, $gradetype, $sectionlistingid);

        // If this is a final grade posting, record the successful postings.
        if ($gradetype === 'final' && !empty($results->successes)) {
            foreach ($results->successes as $grade) {

                // Add gradetype to the grade object for tracking.
                $grade->gradetype = $gradetype;
                self::record_posted_grade($grade, $courseid, $sectionid, $grade->userid);
            }
        }

        return $results;
    }

    /**
     * Generate HTML table for the grades, with posted status indicator for final grades.
     *
     * @param array $enrolledstudents Array of enrolled students.
     * @param int $courseid The course ID.
     * @param int $sectionid The section ID.
     * @param string $gradetype Type of grades ('final' or 'interim').
     * @return array Array containing table HTML and statistics.
     */
    public static function generate_grades_table_with_status($enrolledstudents, $courseid, $sectionid, $gradetype) {
        global $OUTPUT;

        $result = [
            'html' => '',
            'stats' => [
                'total' => 0,
                'posted' => 0,
                'available' => 0
            ]
        ];

        if (empty($enrolledstudents)) {
            $result['html'] = get_string('nostudents', 'block_wds_postgrades');
            return $result;
        }

        $table = new \html_table();
        $table->attributes['class'] = 'wdspgrades generaltable';

        // Different headers based on grade type.
        if ($gradetype === 'final') {
            $table->head = [
                get_string('firstname', 'block_wds_postgrades'),
                get_string('lastname', 'block_wds_postgrades'),
                get_string('universalid', 'block_wds_postgrades'),
                get_string('gradingbasis', 'block_wds_postgrades'),
                get_string('letter', 'block_wds_postgrades'),
                get_string('grade', 'block_wds_postgrades'),
                get_string('status', 'block_wds_postgrades')
            ];
        } else {
            $table->head = [
                get_string('firstname', 'block_wds_postgrades'),
                get_string('lastname', 'block_wds_postgrades'),
                get_string('universalid', 'block_wds_postgrades'),
                get_string('gradingbasis', 'block_wds_postgrades'),
                get_string('letter', 'block_wds_postgrades'),
                get_string('grade', 'block_wds_postgrades')
            ];
        }

        // Get course grade item from first student.
        $firststudent = reset($enrolledstudents);
        $coursegradeitemid = $firststudent->coursegradeitem;

        // Check if we have a valid grade item.
        $gradeitem = self::get_course_grade_item($coursegradeitemid);

        // We have no grades. Rethink your life.
        if ($gradeitem === false) {
            $result['html'] = get_string('nocoursegrade', 'block_wds_postgrades');
            return $result;
        }

        // If finals, get all previously posted grades.
        $postedgrades = [];
        $alreadyposted = 0;
        $postable = 0;
        $totalstudents = count($enrolledstudents);

        if ($gradetype === 'final') {
            $postedgrades = self::get_posted_grades($sectionid);

            // Convert to a lookup by universal_id.
            $postedgradelookup = [];
            foreach ($postedgrades as $pg) {
                $postedgradelookup[$pg->universal_id] = $pg;
            }
            $postedgrades = $postedgradelookup;
        }

        // Build the table rows.
        foreach ($enrolledstudents as $student) {

            // Get the formatted grade.
            $finalgrade = self::get_formatted_grade($student->coursegradeitem, $student->userid, $courseid);

            // Get the grade code.
            $gradecode = self::get_graded_wds_gradecode($student, $finalgrade);

            // Skip invalid grades.
            if (!$gradecode) {
                mtrace("Not processing {$student->firstname} {$student->lastname} due to multiple workday grade codes.");
                unset($student);
                continue;

            // We should not be here because I am catching this in the call to get_formatted_grade and failing them.
            } else if ($gradecode->grade_display == 'No Grade') {
                mtrace("Not processing {$student->firstname} {$student->lastname} due to them not having a final grade.");
                unset($student);
                continue;
            }

            // This is a valid grade, count it.
            $postable++;

            // Build the table row.
            $tablerow = [
                $student->firstname,
                $student->lastname,
                $student->universal_id,
                $student->grading_basis,
                $finalgrade->letter,
                $gradecode->grade_display
            ];

            // Add status column for final grades.
            if ($gradetype === 'final') {
                $status = 'Not poasted';

                // Check if this grade was already posted.
                if (isset($postedgrades[$student->universal_id])) {
                    $postedgrade = $postedgrades[$student->universal_id];
                    $alreadyposted++;

                    // Format the date.
                    $postdate = userdate($postedgrade->timecreated, get_string('strftimedatetime', 'core_langconfig'));
                    $poster = $postedgrade->poster_firstname . ' ' . $postedgrade->poster_lastname;

                    // Create status with icon.
                    $status = $OUTPUT->pix_icon('i/checkedcircle',
                        get_string('alreadyposted', 'block_wds_postgrades'),
                        'moodle',
                        ['class' => 'text-success']);

                    $status .= ' ' . get_string('alreadyposted', 'block_wds_postgrades');
                    $status .= \html_writer::tag('div',
                        get_string('dateposted', 'block_wds_postgrades', $postdate),
                        ['class' => 'small text-muted']);

                    $status .= \html_writer::tag('div',
                        get_string('postedby', 'block_wds_postgrades', $poster),
                        ['class' => 'small text-muted']);

                    // Add a hidden field to exclude this grade.
                    $status .= \html_writer::empty_tag('input', [
                        'type' => 'hidden',
                        'name' => 'already_posted[]',
                        'value' => $student->universal_id
                    ]);
                } else {

                    // Not posted yet, include for posting.
                    $status = \html_writer::empty_tag('input', [
                        'type' => 'hidden',
                        'name' => 'students_to_post[]',
                        'value' => $student->universal_id
                    ]);

                    // Prepare student data for posting.
                    foreach (['userid', 'section_listing_id', 'grading_scheme', 'grading_basis'] as $field) {
                        if (isset($student->$field)) {
                            $status .= \html_writer::empty_tag('input', [
                                'type' => 'hidden',
                                'name' => "student_data[{$student->universal_id}][{$field}]",
                                'value' => $student->$field
                            ]);
                        }
                    }
                }

                $tablerow[] = $status;
            }

            $table->data[] = $tablerow;
        }

        // Generate the table HTML if we have any data.
        if (!empty($table->data)) {
            $result['html'] = \html_writer::table($table);
        } else {
            $result['html'] = get_string('nostudents', 'block_wds_postgrades');
        }

        // Set statistics.
        $result['stats'] = [
            'total' => $totalstudents,
            'posted' => $alreadyposted,
            'available' => $postable - $alreadyposted
        ];

        return $result;
    }

    /**
     * Check if all final grades for a section have been posted.
     *
     * @param int $sectionid The section ID.
     * @param array $enrolledstudents Array of enrolled students.
     * @param int $courseid The course ID.
     * @return bool True if all grades have been posted.
     */
    public static function all_final_grades_posted($sectionid, $enrolledstudents, $courseid) {
        global $DB;

        // Count valid grades.
        $validgrades = 0;
        $postedgrades = 0;

        // Get all posted grades for this section.
        $posted = self::get_posted_grades($sectionid);
        $postedids = [];

        foreach ($posted as $p) {
            $postedids[$p->universal_id] = true;
        }

        // Check each student.
        foreach ($enrolledstudents as $student) {

            // Get the formatted grade.
            $finalgrade = self::get_formatted_grade($student->coursegradeitem, $student->userid, $courseid);

            // Get the grade code.
            $gradecode = self::get_graded_wds_gradecode($student, $finalgrade);

            // Skip invalid grades.
            if (!$gradecode || $gradecode->grade_display == 'No Grade') {
                continue;
            }

            // Count valid grades.
            $validgrades++;

            // Check if posted.
            if (isset($postedids[$student->universal_id])) {
                $postedgrades++;
            }
        }

        // All grades are posted if there are valid grades and all of them are posted.
        return ($validgrades > 0 && $validgrades == $postedgrades);
    }

    /**
     * Post grades to Workday using the configured posting method.
     *
     * @param array $grades Array of grade objects to be posted.
     * @param string $gradetype Type of grades being posted ('final' or 'interim').
     * @param string $sectionlistingid The Workday Section Listing ID for the course section.
     * @return object Results object containing successes and failures.
     */
    public static function post_grades_with_method($grades, $gradetype, $sectionlistingid) {

        // Get the configured posting method.
        $postingmethod = get_config('block_wds_postgrades', 'postingmethod');

        // Initialize results object.
        $results = new \stdClass();
        $results->successes = array();
        $results->failures = array();

        if ($postingmethod == 'individual') {

            // Post grades one at a time.
            foreach ($grades as $grade) {

                // Create an array with just this student.
                $singlegrade = array($grade);

                // Post the individual grade.
                $result = self::post_grade($singlegrade, $gradetype, $sectionlistingid);

                if ($result === 'error') {

                    // Handle connection error.
                    $grade->errormessage = get_string('connectionerror', 'block_wds_postgrades');
                    $results->failures[] = $grade;
                } else if (is_object($result) && isset($result->error)) {

                    // Handle response error.
                    $grade->errormessage = get_string('servererror', 'block_wds_postgrades', $result->error);

                    // Check for more specific error if XML is available.
                    if (isset($result->xmlstring)) {

                        // Parse for specific errors
                        $errors = self::parseerrors($result->xmlstring);
                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                                $grade->errormessage = $error->message;
                            }
                        }
                    }

                    $results->failures[] = $grade;
                } else {

                    // Success.
                    $results->successes[] = $grade;
                }
            }
        } else {

            // Default to batch posting (all students at once).
            $result = self::post_grade($grades, $gradetype, $sectionlistingid);

            if ($result === 'error') {

                // Handle general error - all grades failed.
                foreach ($grades as $grade) {
                    $grade->errormessage = get_string('connectionerror', 'block_wds_postgrades');
                    $results->failures[] = $grade;
                }
            } else if (is_object($result) && isset($result->error)) {

                // Process detailed errors.
                if (isset($result->xmlstring)) {
                    $errors = self::parseerrors($result->xmlstring);
                    $errorindexes = array();

                    // Process error details.
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            $errindex = $error->index;

                            if (is_numeric($errindex) && isset($grades[$errindex - 1])) {

                                // Build the failure object.
                                $stugrade = clone $grades[$errindex - 1];
                                $stugrade->errormessage = $error->message;
                                $results->failures[] = $stugrade;

                                // Track which indexes had errors.
                                $errorindexes[] = $errindex - 1;
                            }
                        }
                    }

                    // Any grades not in the error list were successful.
                    foreach ($grades as $index => $grade) {
                        if (!in_array($index, $errorindexes)) {
                            $results->successes[] = $grade;
                        }
                    }
                } else {

                    // No detailed error info - consider all as failed.
                    foreach ($grades as $grade) {
                        $grade->errormessage = get_string('servererror', 'block_wds_postgrades', $result->error);
                        $results->failures[] = $grade;
                    }
                }
            } else {

                // All successful.
                $results->successes = $grades;
            }
        }

        // Check for section-wide status issues.
        if (is_object($result) && isset($result->xmlstring)) {
            $sectionstatus = self::pg_section_status($result->xmlstring);
            if ($sectionstatus) {
                $results->section_status = true;
            }
        }

        return $results;
    }

    public static function pg_section_status($errors) {

        // TODO: WTF am I going to do with this? There are actually 3 of these potential messages and I probably have to deal with all of them.
        $searchmessage1 = "The Student Course Section for the Section Listing must have already started to be valid for grading.";
        $searchmessage2 = "The Student Course Section for the Section Listing must be of the Instructional Format that controls grading.";
        $searchmessage3 = "The Student Course Section for the Section Listing must have Registered Student Course Registrations for grading.";

        // Load the XML string.
        $xml = simplexml_load_string($errors, null, LIBXML_NOCDATA);

        // Register the namespaces for XPath.
        $xml->registerXPathNamespace('SOAP-ENV', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('wd', 'urn:com.workday/bsvc');

        // Use XPath to find all <wd:Message> elements that contain the error message.
        $messages = $xml->xpath('//wd:Message');

        $found = false;

        // Loop through all messages to check if the target message exists.
        foreach ($messages as $message) {
            if ((string)$message == $searchmessage1) {
                $found = true;
                break;
            }
        }

        // Return true if found, false otherwise.
        return $found ? "true" : "false";
    }

    public static function parseerrors($data) {

        // Load the XML string into a SimpleXML object.
        $xml = simplexml_load_string($data, null, LIBXML_NOCDATA);

        // Register the namespaces for XPath.
        $xml->registerXPathNamespace('SOAP-ENV', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('wd', 'urn:com.workday/bsvc');

        // Find all Validation_Error elements.
        $errors = $xml->xpath('//wd:Validation_Error');

        // Initialize an array to store the results.
        $results = [];

        foreach ($errors as $error) {

            // Build out the object for inclusion in the array.
            $returnerror = new \stdClass();

            // Get the message and xpath values.
            $message = (string) $error->xpath('wd:Message')[0];
            $xpath = (string) $error->xpath('wd:Xpath')[0];

            // Extract the index from the XPath.
            preg_match('/Student_Grades_Data\[(\d+)\]/', $xpath, $matches);

            if (isset($matches[1])) {

                // The index inside the Student_Grades_Data[X].
                $index = $matches[1];
            } else {

                // In case we don't find the expected index.
                $index = 'unknown';
            }

            // Build out the returnerror object.
            $returnerror->index = $index;
            $returnerror->message = $message;

            // Store the result in the array.
            $results[] = $returnerror;
        }

        // Return the results.
        return $results;
    }










    /**
     * Builds the XML structure for student grades to be posted to Workday.
     *
     * This function constructs the XML data required for posting either final or interim grades
     * to Workday's API. It processes each student grade and generates the appropriate XML structure
     * based on the grade type (final or interim).
     *
     * @param @array $grades An array of grade objects containing student and grade information.
     * @param @string $gradetype The type of grades being posted ('final' or any other value for interim).
     * @return @string The constructed XML string representing student grades data.
     */
    public static function buildgradestopost($grades, $gradetype) {
        $today = date('Y-m-d');

        $studentgrades = '';
        foreach ($grades as $grade) {

            // Student Registration Data.
            $sectionlistingid = $grade->section_listing_id;
            $universalid = $grade->universal_id;

            // Grade for the registration in question.
            $gradeid = $grade->grade_id;

            // Check to see if we're in final or this is an interim grade.
            if ($gradetype == "final") {

                // Posting final grades.
                $sdtype = "Student_Grades_Data";

                // If we have a last date of attendance set, send it.
                if (isset($grade->wdladate)) {
                    $ld = $grade->wdladate;
                    $ldoa = "<wd:Student_Last_Date_of_Attendance>$ld</wd:Student_Last_Date_of_Attendance>";
                } else {
                    $ldoa = "";
                }

                // If we have an interim grade note, use it.
                if (isset($grade->grade_note_required)) {
                    $note = $grade->grade_note_required;
                    $gnote = "<wd:Student_Grade_Note>$note</wd:Student_Grade_Note>";
                } else {
                    $gnote = "";
                }

                $gdate = "";
            } else {

                // Posting interim grades.
                $sdtype = "Student_Interim_Grades_Data";

                // If we have an interim grade note, use it.
                if (isset($grade->grade_note_required)) {
                    $note = $grade->grade_note_required;
                    $gnote = "<wd:Student_Interim_Grade_Note>$note</wd:Student_Interim_Grade_Note>";
                } else {
                    $gnote = "";
                }

                // Set the interim grade date to today and send it.
                $gdate =  "<wd:Student_Interim_Grade_Date>$today</wd:Student_Interim_Grade_Date>";

                // Last date of attendance never required for interim grades?
                $ldoa = "";
            }

            // Build out the xml.
            $studentsgrade = '
                            <wd:' . $sdtype . '>
                                <wd:Student_Reference>
                                    <wd:ID wd:type="Universal_Identifier_ID">' . $universalid . '</wd:ID>
                                </wd:Student_Reference>
                                <wd:Student_Grade_Reference>
                                    <wd:ID wd:type="Student_Grade_ID">' . $gradeid . '</wd:ID>
                                </wd:Student_Grade_Reference>
                                ' . $gnote . '
                                ' . $gdate . '
                                ' . $ldoa . '
                            </wd:' . $sdtype . '>';

            // Send this to the $studentgrades loop.
            $studentgrades .= $studentsgrade;
        }

        return $studentgrades;
    }

    /**
     * Grabs the workday student settings from config_plugins.
     *
     * @return @object $s
     */
    public static function get_settings() {
        $s = new \stdClass();
        $bs = new \stdClass();

        // Get the PG settings.
        $bs = get_config('block_wds_postgrades');

        // Get the WDS settings.
        $s = get_config('enrol_workdaystudent');

        // Add the posting method.
        $s->postingmethod = $bs->postingmethod;

        // Add the suffix.
        $s->usernamesuffix = $bs->usernamesuffix;

        $s->workdayapiurl = $bs->workdayapiurl;
        $s->workdayapiversion = $bs->workdayapiversion;

        return $s;
    }

    /**
     * Posts grades to Workday via SOAP API.
     *
     * This function sends the constructed grade data to Workday's API using a SOAP request.
     * It handles both final and interim grades, builds the necessary XML structure,
     * and processes the response.
     *
     * @param @object $s Object containing API credentials and configuration.
     * @param @array $grades Array of grade objects to be posted.
     * @param @string $gradetype Type of grades being posted ('final' or any other value for interim).
     * @param @string $sectionlistingid The Workday Section Listing ID for the course section.
     * @return @string | @object The cleaned XML response string on success, error or object on failure.
     */
    public static function post_grade($grades, $gradetype, $sectionlistingid) {

        // Get settings.
        $s = self::get_settings();

        // Build out the xml.
        $xml = self::buildsoapxml($s, $grades, $gradetype, $sectionlistingid);

        // Workday API credentials.
        $username = $s->username . $s->usernamesuffix;
        $password = $s->password;

        $version = $s->workdayapiversion;

        // Workday API endpoint for the Submit_Grades_for_Registrations SOAP operation.
        $workdayurl = rtrim($s->workdayapiurl, '/') . '/' . $version;

        // Initiate the curl handler.
        $ch = curl_init($workdayurl);

        // Set cURL options for curl request.
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($xml)
        ]);

        // Execute cURL request.
        $response = curl_exec($ch);

        // Get the http code for later.
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Store any curl error before closing the handle.
        $curlerrno = curl_errno($ch);
        $curlerror = $curlerrno ? curl_error($ch) : null;

        // Close the curl handle to free resources.
        curl_close($ch);

        // Check if the cURL request was successful.
        if(!is_null($curlerror)) {

            // Return the error.
            mtrace("cURL ERROR: $curlerror. Aborting.");
            return "error";

        // Check to see that we have a proper response.
        } else if ($httpcode != "200") {
            if ($httpcode != "500") {

                // Return the HTTP status code.
                mtrace("SERVER ERROR - HTTP Status Code: $httpcode. Aborting.");
                return "error";
            } else {

                // Clean the resulting response.
                $xmlstring = self::cleanxml($response);

                // Build an object to store the error code and XML string.
                $xmlobj = new \stdClass();

                // Add the error.
                $xmlobj->error = $httpcode;

                // Add ths XML string.
                $xmlobj->xmlstring = $xmlstring;

                return $xmlobj;
            }
        }

        // Clean the resulting response.
        $xmlstring = self::cleanxml($response);

        return $xmlstring;
    }

    /**
     * Builds the complete SOAP XML request for submitting grades to Workday.
     *
     * This function constructs the complete SOAP envelope with authentication headers and
     * the appropriate payload structure based on whether final or interim grades are being submitted.
     * It integrates the student grades data generated by buildgradestopost() into the full SOAP request.
     *
     * @param @object $s Object containing API credentials and configuration.
     * @param @array $grades Array of grade objects to be posted.
     * @param @string $gradetype Type of grades being posted ('final' or any other value for interim).
     * @param @string $sectionlistingid The Workday Section Listing ID for the course section.
     * @return @string The complete SOAP XML request as a cleaned string.
     */
    public static function buildsoapxml($s, $grades, $gradetype, $sectionlistingid) {

        // Build out if it's interim or final grades.
        if ($gradetype == "final") {
            $wdendpoint = "Submit_Grades_for_Registrations_Request";
            $wddata = "Submit_Grades_for_Registrations_Data";
            $bpparms = "<wd:Business_Process_Parameters>" .
                "<wd:Auto_Complete>true</wd:Auto_Complete>" .
                "<wd:Run_Now>true</wd:Run_Now>" .
                "</wd:Business_Process_Parameters>";
        } else {
            $wdendpoint = "Put_Interim_Grades_for_Registrations_Request";
            $wddata = "Put_Interim_Grades_for_Registrations_Data";
            $bpparms = "";
        }

        // Workday API credentials.
        $username = $s->username . $s->usernamesuffix;
        $password = $s->password;
        $version = "v" . $s->workdayapiversion;

        // Build out the student grades portion of the xml.
        $gradesxml = self::buildgradestopost($grades, $gradetype);

        // Create SOAP Envelope.
        $xml = new \SimpleXMLElement('<env:Envelope
            xmlns:env="http://schemas.xmlsoap.org/soap/envelope/"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <env:Header>
                    <wsse:Security env:mustUnderstand="1">
                        <wsse:UsernameToken>
                            <wsse:Username>'
                                . $username .
                            '</wsse:Username>
                            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'
                                . $password .
                            '</wsse:Password>
                        </wsse:UsernameToken>
                    </wsse:Security>
                </env:Header>
                <env:Body>
                    <wd:' . $wdendpoint . '
                        xmlns:wd="urn:com.workday/bsvc"
                        wd:version="' . $version . '">
                        ' . $bpparms . '
                        <wd:' . $wddata . '>
                            <wd:Section_Listing_Reference>
                                <wd:ID wd:type="Section_Listing_ID">' . $sectionlistingid . '</wd:ID>
                            </wd:Section_Listing_Reference>
                            ' . $gradesxml . '
                        </wd:' . $wddata . '>
                    </wd:' . $wdendpoint . '>
                </env:Body>
           </env:Envelope>');

        // Convert SimpleXMLElement to string.
        $xmlstring = $xml->asXML();

        $xmlstr = self::cleanxml($xmlstring);

        // Return the XML as a string.
        return $xmlstr;
    }

    /**
     * Cleans and validates XML strings.
     *
     * This function processes XML strings to ensure they are well-formed and properly formatted.
     * It removes unwanted patterns (like '{+1}'), validates the XML structure using DOMDocument,
     * and formats the output for better readability.
     *
     * @param @string $xmlstring The XML string to be cleaned and validated.
     * @return @string | @null The cleaned and formatted XML string, or null if the XML is invalid.
     */
    public static function cleanxml($xmlstring) {

        // Use a regex to remove `{+1}` entirely.
        $xmlstring = preg_replace('/\{[^}]*\}/', '', $xmlstring);

        // Ensure that the XML is well-formed using DOMDocument.
        $dom = new \DOMDocument('1.0', 'UTF-8');

        // Suppress warnings to handle them programmatically.
        libxml_use_internal_errors(true);

        // Load the XML string into the DOMDocument.
        if (!$dom->loadXML($xmlstring)) {

            // If there's an error loading XML, print the errors for debugging.
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                echo ("XML Error: " . $error->message . "\n");
            }

            libxml_clear_errors();

            // Return null if XML is invalid.
            return null;
        }

        // Format the output (pretty print) for easier reading.
        $dom->formatOutput = true;

        // Return the cleaned and formatted XML string.
        return $dom->saveXML();
    }

    /**
     * Get enrolled students and their associated data.
     *
     * @param @int $courseid The course ID.
     * @return @array The enrolled students data.
     */
    public static function get_enrolled_students($courseid, $sectionid) {
        global $DB;

        // Build out the parms for getting students.
        $parms = ['courseid' => $courseid, 'sectionid' => $sectionid];

        // The sql for getting students.
        $sql = "SELECT
                stuenr.id AS studentenrollid,
                COALESCE(stu.preferred_firstname, stu.firstname) AS firstname,
                COALESCE(stu.preferred_lastname, stu.lastname) AS lastname,
                u.id AS userid,
                stu.universal_id,
                stuenr.grading_scheme,
                stuenr.grading_basis,
                sec.course_section_definition_id,
                sec.section_listing_id,
                sec.section_number,
                sec.course_subject_abbreviation,
                cou.course_number,
                sec.moodle_status AS courseid,
                COALESCE(sm.data, 'Undergraduate') AS academic_level,
                per.academic_period_id,
                per.start_date AS periodstart,
                per.end_date AS periodend,
                gi.id AS coursegradeitem
            FROM {course} c
            INNER JOIN {enrol_wds_sections} sec
                ON sec.moodle_status = c.id
                AND sec.id = :sectionid
            INNER JOIN {enrol_wds_periods} per
                ON per.academic_period_id = sec.academic_period_id
            INNER JOIN {enrol_wds_courses} cou
                ON cou.course_listing_id = sec.course_listing_id
            INNER JOIN {enrol_wds_student_enroll} stuenr
                ON stuenr.section_listing_id = sec.section_listing_id
                AND stuenr.status = 'enrolled'
            INNER JOIN {enrol_wds_students} stu
                ON stu.universal_id = stuenr.universal_id
            INNER JOIN {user} u
                ON stu.userid = u.id
            INNER JOIN {grade_items} gi
                ON gi.courseid = c.id
                AND gi.itemtype = 'course'
            LEFT JOIN {enrol_wds_students_meta} sm
                ON sm.academic_period_id = sec.academic_period_id
                AND stu.id = sm.studentid
                AND sm.datatype = 'Academic_Level'
            WHERE
                c.id = :courseid
            ORDER BY stu.lastname ASC, stu.firstname ASC";

        // Get em.
        $enrollments = $DB->get_records_sql($sql, $parms);

        return $enrollments;
    }

    public static function get_wds_sla($userid, $courseid) {
        global $DB;

        // Set the table.
        $slatable = 'user_lastaccess';

        // Set the parms.
        $slaparms = ['userid' => $userid, 'courseid' => $courseid];

        // get the data.
        $sla = $DB->get_record($slatable, $slaparms, '*');

        // If we have a $sla return it, otherwise they never accessed and the date should be 0.
        return $sla ? $sla : 0;
    }

    /**
     * Get the course grade item.
     *
     * @param @int $gradeitemid The grade item ID.
     * @return @object $formattedgrade The grade item object.
     */
    public static function get_course_grade_item($gradeitemid) {
        return \grade_item::fetch(['id' => $gradeitemid]);
    }

    public static function get_scale_grade_display($scaleid, $finalgrade, $gradeitem) {
        global $DB;

        // Get the scale record..
        $scalerecord = $DB->get_record('scale', array('id' => $scaleid), '*', MUST_EXIST);

        // Instantiate the scale.
        $gradescale = new \grade_scale($scalerecord, false);

        // If we do not have these set, set them.
        if (empty($gradescale->scale_items)) {
            $gradescale->scale_items = explode(',', $gradescale->scale);
        }

        // For later.
        $scaleitems = $gradescale->scale_items;

        // Make sure we're dealing with integers for comparison.
        $gradevalue = (int)$finalgrade;

        // We're not dealing with anything weird.
        $scalegradetext = $scaleitems[$gradevalue - 1];

        // If we have a 0, return the 1st entry.
        if ($gradevalue < 1) {
            $scalegradetext = reset($scaleitems);
            return $scalegradetext;
        }

        // If the grade is higher than the highest scale, return the last value.
        if ($gradevalue > count($scaleitems)) {
            $scalegradetext = end($scaleitems);
            return $scalegradetext;
        }

        // We're not dealing with anything weird.
        $scalegradetext = $scaleitems[$gradevalue - 1];

        // Apply any additional formatting if needed.
        if ($gradeitem && method_exists($gradeitem, 'format_grade')) {
            return $gradeitem->format_grade($finalgrade, true);
        }

        return $scalegradetext;
    }

    /**
     * Get formatted grade for a student.
     *
     * @param @int $gradeitemid The grade item ID.
     * @param @int $userid The user ID.
     * @param @int $courseid The course ID.
     * @return @string The formatted grade.
     */
    public static function get_formatted_grade($gradeitemid, $userid, $courseid) {
        global $DB, $CFG;

        // Build this to store the formatted grades later.
        $formattedgrades = new \stdClass();
        $formattedgrades->real = get_string('nograde', 'block_wds_postgrades');
        $formattedgrades->percent = get_string('nograde', 'block_wds_postgrades');
        $formattedgrades->letter = get_string('nograde', 'block_wds_postgrades');

        // Get the grade item.
        $gradeitem = self::get_course_grade_item($gradeitemid);

        // We don't have grades yet. Deal.
        if ($gradeitem === false) {
            return $formattedgrades;
        }

        // Get the grade.
        $grade = new \grade_grade(['itemid' => $gradeitemid, 'userid' => $userid]);

        // Set the grade item grademax to the user grade rawgrademax to account for excluding hiddens.
        $gradeitem->grademax = $grade->rawgrademax;

        // Check if grade exists.
        if (!isset($grade->finalgrade) || $grade->finalgrade === null) {
            return $formattedgrades;
        }

        // Get grade decimal points setting.
        $gradedecimalpoints = grade_get_setting($courseid, 'decimalpoints', 2);

        // Format the grade according to different display types. Real.
        $formattedgrades->real = grade_format_gradevalue(
            $grade->finalgrade,
            $gradeitem,
            true,
            GRADE_DISPLAY_TYPE_REAL,
            $gradedecimalpoints
        );

        // Format the grade according to different display types. Percent.
        $formattedgrades->scale = grade_format_gradevalue(
            $grade->finalgrade,
            $gradeitem,
            true,
            GRADE_DISPLAY_TYPE_PERCENTAGE,
            $gradedecimalpoints
        );

        // Format the grade according to different display types. Percent.
        $formattedgrades->percent = grade_format_gradevalue(
            $grade->finalgrade,
            $gradeitem,
            true,
            GRADE_DISPLAY_TYPE_PERCENTAGE,
            $gradedecimalpoints
        );

        // If we're dealing with scales, set the grade letter to be the scale name.
        if (!is_null($gradeitem->scaleid)) {
            $gradescale = self::get_scale_grade_display($gradeitem->scaleid, $grade->finalgrade, $gradeitem);

            // Format the grade according to different display types. Scale.
            $formattedgrades->letter = $gradescale;

        } else {

            // Format the grade according to different display types. Letter.
            $formattedgrades->letter = grade_format_gradevalue(
                $grade->finalgrade,
                $gradeitem,
                true,
                GRADE_DISPLAY_TYPE_LETTER,
                $gradedecimalpoints
            );
        }

        return $formattedgrades;
    }

    /**
     * Get the required grade code for the grade in question.
     *
     * @param @object $student The student object with grade and section.
     * @param @object $finalgrade The student final grade with all variations.
     * @return @string The student's final grade code to be sent to WDS.
     */
    public static function get_graded_wds_gradecode($student, $finalgrade) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_grade_schemes';

        // Deal with graded ones 1st as they should always be 1:1.
        if ($student->grading_basis == 'Graded') {

            // If they do not have a final grade, fail them.
            $fg = isset($finalgrade->letter) && $finalgrade->letter != 'No grade' ? $finalgrade->letter : 'F';

            // Build out the parms for the graded codes.
            $parms = [
                'grading_scheme_id' => $student->grading_scheme,
                'grading_basis' => $student->grading_basis,
                'grade_display' => $fg
            ];

        // Pass / Fail grades.
        } else if ($student->grading_basis == 'Pass/Fail') {

            // If they do not have a final grade, fail them.
            $fg = isset($finalgrade->letter) && $finalgrade->letter != 'No grade' ? $finalgrade->letter : 'F';

            // Build out an array for passing grades.
            $keywordarray = [
                'A+' => 'Pass',
                'A' => 'Pass',
                'A-' => 'Pass',
                'B+' => 'Pass',
                'B' => 'Pass',
                'B-' => 'Pass',
                'C+' => 'Pass',
                'C' => 'Pass',
                'Pass' => 'Pass',
                'C-' => 'F',
                'D+' => 'F',
                'D' => 'F',
                'D-' => 'F',
                'F' => 'F',
                'Fail' => 'F'
            ];

            // Get the appropriate keyword to use to look up the code.
            $letter = $keywordarray[$fg] ?? 'Unknown';

            // Build out the parms for the PF codes.
            $parms = [
                'grading_scheme_id' => $student->grading_scheme,
                'grading_basis' => $student->grading_basis,
                'grade_display' => $letter
            ];

        // Auditors.
        } else if ($student->grading_basis == 'Audit') {

            // Build out the parms for the PF codes.
            $parms = [
                'grading_scheme_id' => $student->grading_scheme,
                'grading_basis' => $student->grading_basis,
                'grade_display' => 'Audit'
            ];

        // Non-credit / Creditors.
        } else if ($student->grading_basis == 'Credit/Non Credit') {

            if ($student->grading_scheme == 'LSUAM Standard Grading Scheme') {

                // If they do not have a final grade, fail them.
                $fg = isset($finalgrade->letter) && $finalgrade->letter != 'No grade' ? $finalgrade->letter : 'F';

                // Build out an array for passing grades.
                $keywordarray = [
                    'A+' => 'Pass',
                    'A' => 'Pass',
                    'A-' => 'Pass',
                    'B+' => 'Pass',
                    'B' => 'Pass',
                    'B-' => 'Pass',
                    'C+' => 'Pass',
                    'C' => 'Pass',
                    'Pass' => 'Pass',
                    'C-' => 'In Progess',
                    'D+' => 'In Progess',
                    'D' => 'In Progess',
                    'D-' => 'In Progess',
                    'F' => 'In Progess',
                    'Fail' => 'In Progess'
                ];

                // Get the appropriate keyword to use to look up the code.
                $letter = $keywordarray[$fg] ?? 'Unknown';

                // Build out the parms for the codes.
                $parms = [
                    'grading_scheme_id' => $student->grading_scheme,
                    'grading_basis' => $student->grading_basis,
                    'grade_display' => $letter
                ];

            // Honors.
            } else if ($student->grading_scheme == 'LSUAM Honors Grading Scheme') {

                // If they do not have a final grade, fail them.
                $fg = isset($finalgrade->letter) && $finalgrade->letter != 'No grade' ? $finalgrade->letter : 'F';

                // Build out an array for passing grades.
                $keywordarray = [
                    'A+' => 'Pass',
                    'A' => 'Pass',
                    'A-' => 'Pass',
                    'B+' => 'Pass',
                    'B' => 'Pass',
                    'B-' => 'Pass',
                    'C+' => 'Pass',
                    'C' => 'Pass',
                    'Pass' => 'Pass',
                    'C-' => 'No Credit (HNR)',
                    'D+' => 'No Credit (HNR)',
                    'D' => 'No Credit (HNR)',
                    'D-' => 'No Credit (HNR)',
                    'F' => 'No Credit (HNR)',
                    'Fail' => 'No Credit (HNR)'
                ];

                // Get the appropriate keyword to use to look up the code.
                $letter = $keywordarray[$fg] ?? 'Unknown';

                // Build out the parms for the codes.
                $parms = [
                    'grading_scheme_id' => $student->grading_scheme,
                    'grading_basis' => $student->grading_basis,
                    'grade_display' => $letter
                ];

            // I have no idea what to do here.
            } else {

                // If they do not have a final grade, fail them.
                $fg = isset($finalgrade->letter) && $finalgrade->letter != 'No grade' ? $finalgrade->letter : 'F';

                // Build out an array for passing grades.
                $keywordarray = [
                    'A+' => 'Pass',
                    'A' => 'Pass',
                    'A-' => 'Pass',
                    'B+' => 'Pass',
                    'B' => 'Pass',
                    'B-' => 'Pass',
                    'C+' => 'Pass',
                    'C' => 'Pass',
                    'Pass' => 'Pass',
                    'C-' => 'In Progess',
                    'D+' => 'In Progess',
                    'D' => 'In Progess',
                    'D-' => 'In Progess',
                    'F' => 'In Progess',
                    'Fail' => 'In Progess'
                ];

                // Get the appropriate keyword to use to look up the code.
                $letter = $keywordarray[$fg] ?? 'Unknown';

                // Build out the parms for the codes.
                $parms = [
                    'grading_scheme_id' => $student->grading_scheme,
                    'grading_basis' => $student->grading_basis,
                    'grade_display' => $letter
                ];
            }
        }

        // Get the data.
        $gradecode = $DB->get_records($table, $parms);

        // Student has no final grade.
        if (!$gradecode) {
            $gradecode = new \stdClass();
            $gradecode->grading_scheme_id = $student->grading_scheme;
            $gradecode->grading_basis = $student->grading_basis;
            $gradecode->grade_id = 'No Grade';
            $gradecode->grade_display = 'No Grade';

        // Student has a weird situation where more than one grade code is returned.
        } else if (count($gradecode) > 1) {

            mtrace("More than one possible grade for " .
                "$student->firstname $student->lastname in " .
                "$student->course_section_definition_id.");

            $gradecode = false;

        // We returned one grade code.
        } else {
            $gradecode = is_array($gradecode) ? reset($gradecode) : $gradecode;
        }

        return $gradecode;
    }

    /**
     * Generate HTML table for the grades.
     *
     * @param array $enrolledstudents Array of enrolled students.
     * @param int $courseid The course ID.
     * @return string HTML representation of the table.
     */
    public static function generate_grades_table($enrolledstudents, $courseid) {
        if (empty($enrolledstudents)) {
            return get_string('nostudents', 'block_wds_postgrades');
        }

        $table = new \html_table();
        $table->attributes['class'] = 'wdspgrades generaltable';
        $table->head = [
            get_string('firstname', 'block_wds_postgrades'),
            get_string('lastname', 'block_wds_postgrades'),
            get_string('universalid', 'block_wds_postgrades'),
//            get_string('section', 'block_wds_postgrades'),
//            get_string('gradingscheme', 'block_wds_postgrades'),
            get_string('gradingbasis', 'block_wds_postgrades'),
//            get_string('real', 'grades'),
//            get_string('percentage', 'grades'),
            get_string('letter', 'block_wds_postgrades'),
            get_string('grade', 'block_wds_postgrades'),
//            get_string('gradecode', 'block_wds_postgrades')
        ];

        // Get course grade item from first student.
        $firststudent = reset($enrolledstudents);
        $coursegradeitemid = $firststudent->coursegradeitem;

        // Check if we have a valid grade item.
        $gradeitem = self::get_course_grade_item($coursegradeitemid);

        // We have no grades. Rethink your life.
        if ($gradeitem === false) {
            return get_string('nocoursegrade', 'block_wds_postgrades');
        }

        // Build the table rows.
        foreach ($enrolledstudents as $student) {

            // Get the formatted grade.
            $finalgrade = self::get_formatted_grade($student->coursegradeitem, $student->userid, $courseid);

            // Get the grade code.
            $gradecode = self::get_graded_wds_gradecode($student, $finalgrade);

            // We have more or less than one gradecodefor this person/grade, leave this place.
            if (!$gradecode) {
                mtrace("Not processing $student->firstname $student->lastname due to multiple workday grade codes.");
                unset($student);
                continue;
            } else if ($gradecode->grade_display == 'No Grade') {
                mtrace("Not processing $student->firstname $student->lastname due to them not having a final grade.");
                unset($student);
                continue;
            }

            // Section identifier.
            $sectionidentifier = $student->course_subject_abbreviation .
                ' ' .
                $student->course_number .
                ' ' .
                $student->section_number;


            // Build out the table.
            $tablerow = [
                $student->firstname,
                $student->lastname,
                $student->universal_id,
//                $sectionidentifier,
//                $student->grading_scheme,
                $student->grading_basis,
//                $finalgrade->real,
//                $finalgrade->percent,
                $finalgrade->letter,
                $gradecode->grade_display,
//                $gradecode->grade_id,
            ];

            // Populate it.
            $table->data[] = $tablerow;
        }

        // Burn it to disk.
        return \html_writer::table($table);
    }
}
