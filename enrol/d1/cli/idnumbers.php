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
 * @package    enrol_d1
 * @copyright  2022 onwards LSUOnline & Continuing Education
 * @copyright  2022 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 * This file is used to update course idnumbers via CLI.
 *
 */

// Make sure this can only run via CLI.
define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');

global $CFG, $DB;

require_once("$CFG->libdir/clilib.php");

// Require the magicness.
require_once('../classes/d1.php');

// Get the token.
$token = lsuid1::get_token();
mtrace("Token: $token");

$ocats = get_config('local_d1', 'ocategories');
$pcats = get_config('local_d1', 'pcategories');

$pupdate = lsuid1::update_pd_idnumbers($pcats);
$oupdate = lsuid1::update_odl_idnumbers($ocats);

class lsuid1 {


  public static function set_applicability($token, $coursename) {
    // Get the data needed.
    $s = lsud1::get_d1_settings();

    // Set the URL.
    $url = $s->wsurl . '/webservice/InternalViewREST/updateCourse?_type=json';

    // Set the POST body.
    $body = '{"updateCourseRequestDetail": {"course": {"associationMode": "update","courseNumber": "' . $coursename . '","applicability": "Public"}}}';

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

    // Return the response.
    return($response);

  }

    /**
     * Grabs the token from the D1 web services.
     *
     * @return @string $token
     */
    public static function get_token() {
        // Get the data needed.
        $s = lsud1::get_d1_settings();
        $c = lsud1::get_d1_creds();

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

                mtrace("    We found $course->shortname and created $coursename from it.");

                $token = self::get_token();

                $public = self::set_applicability($token, $coursename);

                $success = isset($public->updateCourseResult) ? true : false;
                if ($success) {
                    mtrace("    We set the applicability to public for $coursename.");
                }

                // Set the optional params to query by the custom section number.
                $optionalparms = ', "advancedCriteria": {"customSectionNumber": "' . trim($course->shortname) . '"}';

                // Get the course info from the webservice.
                $c = lsud1::get_course_by('250','courseCode', $coursename, 'Short', $optionalparms);

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
                $c = lsud1::get_course_by('250','courseCode', $coursename, 'Short', $optionalparms);

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

}

?>
