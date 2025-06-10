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

/*
    **********************************************************
    * This is only a test file and will not be used anywhere *
    **********************************************************
*/

// Make sure this can only run via CLI.
define('CLI_SCRIPT', true);

// Include the main Moodle config.
require(__DIR__ . '/../../../config.php');

// This is so we can use the CFG var.
global $CFG;

// Include the CLI lib so we can do this stuff via CLI.
require_once("$CFG->libdir/clilib.php");

// Require the main D1 class.
require_once('../classes/d1.php');

mtrace("The process for grabbing user information from D1 and setting due dates has begun.");

// Set some time for this to start.
$pretime = microtime(true);

// Set the webui token. Get this from your browser!
$token   = '4DA0C5F29C0084CA02179D5F00077E23';

// Set the webservice token.
$wstoken = lsud1::get_token();

// Get emails for pulling and populating D1 info.
$users = ihated1::get_emails();

// Fetch student data from supplied meails and populate the local DB.
$fetchandpop = ihated1::popd1_interstitial($wstoken, $users);

// Get all the d1 ids for the users we just populated.
$d1ids = ihated1::get_d1ids();

// Hardcode this reason for now.
$reason = 'Via Webservices';
$timer = microtime(true);
$counter = 0;
foreach ($d1ids as $d1id) {
$counter++;
$timer1 = microtime(true);
    mtrace("  Begin processing $d1id->email.");
    $enrollcache = ihated1::set_enrollcache($token, $d1id->d1_id);

    if ($enrollcache == true) {
        mtrace("    We were able to set enroll cache for $d1id->email.");
        if (is_null($d1id->enrollid)) {
            $enrollids = ihated1::get_enrollids($token, $d1id->d1_id);
            if (empty($enrollids)) {
                mtrace("  We did not find an enrollment in D1 for $d1id->email.");
            }

            foreach ($enrollids as $enrollid) {
                $time = microtime(true);
                mtrace("    Enroll ID for $d1id->x_number - $d1id->email - $d1id->logonid - $d1id->d1_id: $enrollid");
                $info = ihated1::set_ddcache($token, $enrollid);

                foreach (array_pop($info->courseinfo) as $courseinfo) {
                    if ($d1id->customsectionnumber == $courseinfo) {
                        $date = $d1id->duedate == '' ? '' : explode('/', $d1id->duedate);
                        if (is_array($date)) {
                            $day = (int) $date[1];
                            $month1 = (int) $date[0];
                            $month = $month1 - 1;
                            $year = (int) $date[2];
                        } else {
                            $day = null;
                            $month = null;
                            $year = null;
                        }

                        $updated = ihated1::update_scotty_d1($d1id->id, $enrollid);
                        mtrace("    Matched $d1id->customsectionnumber to $courseinfo.");
                         if ($updated) {
                             mtrace("    Updated $d1id->email" . '\'' . "s enrollid to $enrollid in course $d1id->customsectionnumber.");
                         }
                         if (ihated1::set_enrollcache($token, $d1id->d1_id) == true && $info->getStatus() == 200) {
                             $ddset = ihated1::set_duedate($token, $enrollid, $day, $month, $year, $reason);
                             $ddate = isset($day) ? "$month1/$day/$year" : "no end date";
                             mtrace("    Set duedate for $d1id->email in course $d1id->customsectionnumber to $ddate.");
                             $finished = microtime(true);
                        }
                    } else {
                        mtrace("    $d1id->customsectionnumber does not match $courseinfo.");
                    }
                }
            }
        } else {
            $time = microtime(true);
            mtrace("    Enroll ID for $d1id->x_number - $d1id->email - $d1id->logonid - $d1id->d1_id: $d1id->enrollid");
            $info = ihated1::set_ddcache($token, $d1id->enrollid);

                    $date = $d1id->duedate == '' ? '' : explode('/', $d1id->duedate);
                    if (is_array($date)) {
                        $day = (int) $date[1];
                        $month1 = (int) $date[0];
                        $month = $month1 - 1;
                        $year = (int) $date[2];
                    } else {
                        $day = null;
                        $month = null;
                        $year = null;
                    }

                     if (ihated1::set_enrollcache($token, $d1id->d1_id) == true && $info->getStatus() == 200) {
                         $ddset = ihated1::set_duedate($token, $d1id->enrollid, $day, $month, $year, $reason);
                         $ddate = isset($day) ? "$month1/$day/$year" : "no end date";
                         mtrace("    Set duedate for $d1id->email in course $d1id->customsectionnumber to $ddate.");
                         $updated = ihated1::update_scotty_d1($d1id->id, $d1id->enrollid);
                         $finished = microtime(true);
                     }

        }

/*
        foreach ($enrollids as $enrollid) {
            $time = microtime(true);
            mtrace("    Enroll ID for $d1id->x_number - $d1id->email - $d1id->logonid - $d1id->d1_id: $enrollid");
            $info = ihated1::set_ddcache($token, $enrollid);

            foreach (array_pop($info->courseinfo) as $courseinfo) {
                if ($d1id->customsectionnumber == $courseinfo) {
                    $date = $d1id->duedate == '' ? '' : explode('/', $d1id->duedate);
                    if (is_array($date)) {
                        $day = (int) $date[1];
                        $month1 = (int) $date[0];
                        $month = $month1 - 1;
                        $year = (int) $date[2];
                    } else {
                        $day = null;
                        $month = null;
                        $year = null;
                    }

                    $updated = ihated1::update_scotty_d1($d1id->id, $enrollid);
                    mtrace("    Matched $d1id->customsectionnumber to $courseinfo.");
                    if ($updated) {
                         mtrace("    Updated $d1id->email" . '\'' . "s enrollid to $enrollid in course $d1id->customsectionnumber.");
                     } 
                     if (ihated1::set_enrollcache($token, $d1id->d1_id) == true && $info->getStatus() == 200) {
                         $ddset = ihated1::set_duedate($token, $enrollid, $day, $month, $year, $reason);
                         $ddate = isset($day) ? "$month1/$day/$year" : "no end date";
                         mtrace("    Set duedate for $d1id->email in course $d1id->customsectionnumber to $ddate.");
                         $finished = microtime(true);
                    }
                } else {
                    mtrace("    $d1id->customsectionnumber does not match $courseinfo.");
                }
            }
        }
*/
    }
    $timer2 = microtime(true);
    $totaltime = $timer2 - $timer;
    $elapsed = ROUND($timer2 - $timer1, 3);
    $totalelapsed = ROUND($totaltime / $counter, 3);
    mtrace("  Finished processing $d1id->email - $d1id->x_number in $elapsed seconds, averaging $totalelapsed seconds per.");
}
// How long did this take?
$finished2 = microtime(true);
$elapsed = round($finished2 - $pretime, 2);
mtrace("Took $elapsed seconds");

class ihated1 {

  public static function popd1_interstitial($wstoken, $users) {
    // Set the counter.
    $counter2 = 0;

    $errors = array();

    // Loop through the supplied users, get the D1 info, and populate the appropriate table.
    foreach ($users as $user) {
      // Increment the counter.
      $counter2++;
      // Set the initial time.
      $t1 = microtime(true);

      if($counter2 % 100 == 0) {
          // Set the webservice token.
          $wstoken = lsud1::get_token();
          mtrace("Fetched new token: $wstoken.");
      }

      // Get the student info from the D1 webservice.
      $userinfo = self::get_student_info($wstoken, $user);

      // Get the time for calculating the elapsed time.
      $t2 = microtime(true);
      $elapsed = $t2 - $t1;
      $elapsed = round($elapsed, 2);

      if (isset($userinfo->student[0])) {
        // Log to screen.
        mtrace("  The process to fetch user data from D1 took $elapsed seconds for $user->email.");

        // Grab the 1st user returned as it's an array of one.
        $student = $userinfo->student[0];

        // Update the user info with the returned data.
        $update = self::update_scotty($student, $user);

        // Get the time for calculating the elapsed time.
        $t3 = microtime(true);
        // Calculate the elapsed time.
        $elapsed2 = $t3 - $t1;
        $elapsed2 = round($elapsed2, 2);

        mtrace("  The process to update the DB took $elapsed2 seconds for $user->email.");

        // Log some stuff.
        if (!$update) {
          $errors["update " . $user->id] = "$user->email Failed to update local DB.";
        }
      } else {
        // Log some stuff.
        mtrace("  Failed to fetch any user data from D1 for email: $user->email.");
        $errors["student " . $user->id] = "$user->email Failed to fetch data from D1.";
      }
    }
    return $errors;
  }

  public static function update_scotty($student, $user) {
    global $DB;
    $table = 'scotty_enr';
    $dataobject = array('id'         => $user->id,
                        'logonid'    => isset($student->loginId) ? $student->loginId : null,
    //                    'email'      => isset($student->preferredEmail) ? $student->preferredEmail->emailAddress : null,
                        'x_number'   => isset($student->studentNumber) ? $student->studentNumber : null,
                        'd1_id'      => isset($student->objectId) ? $student->objectId : null,
                        'lsuid'      => isset($student->schoolPersonnelNumber) ? $student->schoolPersonnelNumber : null,
                        'updated'    => 1,
                        'updatedate' => time()
    );

    $update = $DB->update_record($table, $dataobject);
    if ($update) {
        mtrace("    Updated $user->email with relevant info.");
    }
    return $update;

  }

  public static function get_emails() {
    global $DB;
    $sql = 'SELECT s.* FROM mdl_scotty_enr s
            WHERE updated = 0
            AND enrollid IS NULL
            AND duedate LIKE "%\/%"';
    $table = 'scotty_enr';
    $parms = null;
    // $users = $DB->get_records($table, $parms);
    $users = $DB->get_records_sql($sql);
    return $users;
  }

  public static function get_student_info($token, $user) {
    // Get the data needed.
    $s = lsud1::get_d1_settings();

    // Set the URL.
    $url = $s->wsurl . '/webservice/InternalViewREST/searchStudent?informationLevel=Full&_type=json';

    $email = $user->x_number;

    // Set the POST body.
    $body = '{"studentSearchCriteria": {"studentNumber": "' . $email . '", "searchType": "begin_with"}}';

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

  public static function update_scotty_d1($d1id, $enrollid) {
    global $DB;
    $table = 'scotty_enr';
    $dataobject = array('id'         => $d1id,
                        'enrollid'   => $enrollid,
                        'updated'    => 2,
                        'updatedate' => time()

    );

    $update = $DB->update_record($table, $dataobject);
    if ($update) {
        mtrace("    Updated mdl_scotty_enr id #$d1id with relevant info.");
    }
    return $update;

  }

    public static function get_d1ids() {
        global $CFG, $DB;
        $sql = 'SELECT id, x_number, d1_id, logonid, email, customsectionnumber, enrollid, duedate
            FROM mdl_scotty_enr
            WHERE updated = 1
            AND d1_id IS NOT NULL
            AND enrollid IS NULL
            AND duedate LIKE "%\/2023"
#            AND (duedate LIKE "%\/2022"
#            OR duedate LIKE "%\/2023")
            ORDER BY RAND()';

        $d1ids = $DB->get_records_sql($sql);
        return $d1ids;
    }

    public static function set_ddcache($token, $enrollid) {
        require_once 'HTTP/Request2.php';
        $url = 'https://lsuonlinesv.destinyone.moderncampus.net/srs/enrolmgr/common/course/studentEnrolledCourses.do'
            . '?method=changeDueDate&businessObjectId='
            . $enrollid;
        $request = new HTTP_Request2();
        $request->setUrl($url);

        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
            'follow_redirects' => TRUE
        ));
        $request->setHeader(array(
            'cookie' => 'JSESSIONID=' . $token
        ));
        $request->setBody('');
        try {
            $response = $request->send();
            if ($response->getStatus() == 200) {
                preg_match_all('/<h1>(.+?) - (\d+) \(Custom Section# (.+?\)).*\)/', $response->getBody(), $courseinfo);

                $response->courseinfo = $courseinfo;
                return $response;
            } else {
                mtrace('  Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
            }
        }
        catch(HTTP_Request2_Exception $e) {
            return 'Error: ' . $e->getMessage() . "\n";
        }
    }

    public static function set_duedate($token, $enrollid, $day, $month, $year, $reason) {
        require_once 'HTTP/Request2.php';
        $url = 'https://lsuonlinesv.destinyone.moderncampus.net/srs/enrolmgr/common/course/assignmentDueDate.do'
            . '?method=save&businessObjectId='
            . $enrollid
            . '&dueDateRecord.day='
            . $day
            . '&dueDateRecord.month='
            . $month
            . '&dueDateRecord.year='
            . $year
            . '&dueDateReason='
            . $reason;
        $request = new HTTP_Request2();
        $request->setUrl($url);

        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
            'follow_redirects' => TRUE
        ));
        $request->setHeader(array(
            'cookie' => 'JSESSIONID=' . $token
        ));
        $request->setBody('');
        try {
            $response = $request->send();
            if ($response->getStatus() == 200) {
                return $response->getBody();
            } else {
                mtrace('  Unexpected HTTP status: ' . $response->getStatus() . ' ' .
                $response->getReasonPhrase());
            }
        }
        catch(HTTP_Request2_Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public static function set_enrollcache($token, $d1id) {
        require_once 'HTTP/Request2.php';
        $url = 'https://lsuonlinesv.destinyone.moderncampus.net/srs/enrolmgr/student/profile/studentRapidProfile.do?method=edit&businessObjectId='
            . $d1id
            . '&originate=studentSelectedCoursesForm&searchResultsContext=true&refresh=true';
        $request = new HTTP_Request2();
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
            'follow_redirects' => TRUE
        ));
        $request->setHeader(array(
            'cookie' => 'JSESSIONID=' . $token
        ));
        $request->setBody('');
        try {
            $response = $request->send();

            if ($response->getStatus() == 200) {
                return true;
            } else {
                mtrace('  Unexpected HTTP status: ' . $response->getStatus() . ' ' .
                $response->getReasonPhrase());
            }
        }
        catch(HTTP_Request2_Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public static function get_enrollids($token, $d1id) {
        require_once 'HTTP/Request2.php';
        $request = new HTTP_Request2();
        $request->setUrl('https://lsuonlinesv.destinyone.moderncampus.net/srs/enrolmgr/common/course/studentEnrolledCourses.do?method=load&businessObjectId=' . $d1id . '&refresh=true');
        $request->setMethod(HTTP_Request2::METHOD_GET);
        $request->setConfig(array(
            'follow_redirects' => TRUE
        ));
        $request->setHeader(array(
            'cookie' => 'JSESSIONID=' . $token
        ));
        $request->setBody('');
        try {
            $response = $request->send();
            if ($response->getStatus() == 200) {
                preg_match_all('/dueDate_(\d+)/', $response->getBody(), $enrolls);
                return $enrolls[1];
            } else {
                mtrace('  Unexpected HTTP status: ' . $response->getStatus() . ' ' .
                $response->getReasonPhrase());
            }
        }
        catch(HTTP_Request2_Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
