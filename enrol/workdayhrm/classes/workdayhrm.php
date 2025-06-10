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
 * @copyright 2023 onwards LSUOnline & Continuing Education
 * @copyright 2023 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @package    enrol_workdayhrm
 * @copyright  2023 onwards LSUOnline & Continuing Education
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class workdayhrm {

    /**
     * Grabs the settings from config_plugins.
     *
     * @return @object $s
     */
    public static function get_wdhrm_settings() {
        $s = new stdClass();

        // Get the settings.
        $s = get_config('enrol_workdayhrm');

        return $s;
    }

    /**
     * Gets the list of courses matching the required courses.
     *
     * @param  @object $s
     *
     * @return @array of @object $courses
     */
    public static function get_wdhrm_courses($s) {
        global $CFG, $DB;

        // Set the table for use below.
        $table = $CFG->prefix . 'course';

        // Super basic SQL.
        $sql = 'SELECT *
                  FROM ' . $table . '
                WHERE id IN ( ' . $s->courseids . ' )';

        // Get the list of courses.
        $courses = $DB->get_records_sql($sql);

        // Return the data.
        return $courses;
    }

    /**
     * Gets the list of employees from the webservice endpoint.
     *
     * @param  @object $s
     *
     * @return @array of @object $employees
     */
    public static function get_wdhrm_employees($s) {

        // Build the header.
        $header = array('Authorization: Basic ' . $s->token);

        // Initiate the curl.
        $curl = curl_init($s->wsurl);

        // Set the curl options.
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Get the data.
        $json_response = curl_exec($curl);

        // Get the status.
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the curl.
        curl_close($curl);

        // Decode the json.
        $dataobj = json_decode($json_response);

        // Get the data we need.
        $employees = $dataobj->Report_Entry;

        // Return the data.
        return $employees;
    }

    /**
     * Updates or inserts employees from the data provided.
     *
     * @param  @object $s
     * @param  @object $employee
     *
     * @return @object $returndata
     */
    public static function get_wdhrm_exists($s, $employee) {
        global $DB;

        // We will need this later, define it now.
        $returndata = new stdClass();

        // Define table for lookup later.
        $table = 'enrol_workdayhrm';

        // Do some stupid logic because we don't know what will be populated or not.
        if (isset($employee->Work_Email)) {
            $field = 'Work_Email';
            $fd = $employee->Work_Email;
            $conditions = array('work_email' => $employee->Work_Email);
        } else if (isset($employee->Work_Email) && isset($employee->Employee_ID)) {
            $field = 'Work_Email & Employee_ID';
            $fd = $employee->Work_Email . ' & ' . $employee->Employee_ID;
            $conditions = array('work_email' => $employee->Work_Email, 'employee_id' => $employee->Employee_ID);
        } else if (isset($employee->Employee_ID)) {
            $field = 'Employee_ID';
            $fd = $employee->Employee_ID;
            $conditions = array('employee_id' => $employee->Employee_ID);
        } else if (isset($employee->Universal_ID)) {
            $field = 'Universal_ID';
            $fd = $employee->Universal_ID;
            $conditions = array('universal_id' => $employee->Universal_ID);
        } else if (isset($employee->Work_Email)) {
            $field = 'Work_Email';
            $fd = $employee->Work_Email;
            $conditions = array('work_email' => $employee->Work_Email);
        } else if (isset($employee->LSUAM_LSU_ID)) {
            $field = 'LSUAM_LSU_ID';
            $fd = $employee->LSUAM_LSU_ID;
            $conditions = array('school_id' => $employee->LSUAM_LSU_ID);
        } else {
            $errordata = json_encode($employee);
            self::dtrace("  Employee $employee->Legal_First_Name $employee->Legal_Last_Name has no identifying characteristics. Employee record from endpoint follows.");
            self::dtrace("  $errordata\n");
            $returndata->id = null;
            $returndata->message = 'error';
            return $returndata;
        }

        // Actually do the lookup.
        $rd = $DB->get_records($table, $conditions);

        if (count($rd) > 1) {
            mtrace("      Found more than one record for $field = $fd - $employee->Legal_First_Name $employee->Legal_Last_Name, NOT UPDATING THEM!!!");
        } else {
            foreach($rd as $record) {
                $returndata = $record;
            }
        }

        // Set the return values for findable employees.
        if (isset($returndata->id)) {
            self::dtrace("      Found matching user on $field.");
            $returndata->message = 'exists';
        } else {
            self::dtrace("      No matching user found using $field.");
            $returndata->message = 'new';
        }

        // Return the data.
        return $returndata;
    }

    /**
     * Finds dupes in DB.
     *
     * @return @array of @objects $dupes
     */
    public static function wdhrm_find_duplicates() {
        global $DB;

        // Define the SQL.
        $sql = 'SELECT
                  w1.*
                FROM {enrol_workdayhrm} w1
                  INNER JOIN {enrol_workdayhrm} w2 ON w1.work_email = w2.work_email
                WHERE w1.id != w2.id
                AND w1.iscurrent != 2
                ORDER BY w1.work_email ASC,
                  w1.legal_last_name ASC,
                  w1.legal_first_name ASC';

        // Grab the duplicates.
        $dupes = $DB->get_records_sql($sql);

        // Return the duplicates.
        return $dupes;
    }

    /**
     * Updates dupe in DB.
     *
     * @param  @object $dupe
     * @return @bool $ud
     */
    public static function wdhrm_update_duplicates($dupe) {
        // Get the DB.
        global $DB;

        // Set the table.
        $table = 'enrol_workdayhrm';

        // Set this to be ignored later on.
        $dupe->iscurrent = 2;

        // Set the time.
        $dupe->lastupdated = time();

        // Update the record.
        $ud = $DB->update_record($table, $dupe);

        return $ud;
    }

    /**
     * Finds similar objects.
     *
     * @param  @object $obj1
     * @param  @object $obj2
     *
     * @return @float $similarity
     */
    public static function wdhrm_compareobjects($obj1, $obj2) {
        // Get the object variables as arrays for each of the objects.
        $properties1 = get_object_vars($obj1);
        $properties2 = get_object_vars($obj2);

        // Set this up for later.
        $countsimilar = 0;

        // Count the total object variables across both objects.
        $counttotal = count($properties1) + count($properties2);

        // Loop through the properties for 1st object and find matching values.
        foreach ($properties1 as $key => $value) {
            // If we find a match, increment the similarity count.
            if (isset($properties2[$key]) && $properties2[$key] == $value) {
                $countsimilar++;
            }
        }

        // Get the similarity percentage.
        $similarity = round(($countsimilar / $counttotal) * 100, 2);

        // Return the similarity percentage.
        return $similarity;
    }

    /**
     * Updates employees from the data provided.
     *
     * @param  @object $s
     * @param  @object $employee
     *
     * @return @object $returndata
     */
    public static function get_wdhrm_employee($s, $employee) {
        global $DB;

        // Define table for lookup later.
        $table = 'enrol_workdayhrm';

        // Do some stupid logic because we don't know what will be populated or not.
        if (isset($employee->Work_Email) && isset($employee->Employee_ID)) {
            $field = 'Work_Email & Employee_ID';
            $conditions = array('work_email' => $employee->Work_Email, 'employee_id' => $employee->Employee_ID);
        } else if (isset($employee->Employee_ID)) {
            $field = 'Employee_ID';
            $conditions = array('employee_id' => $employee->Employee_ID);
        } else if (isset($employee->Universal_ID)) {
            $field = 'Universal_ID';
            $conditions = array('universal_id' => $employee->Universal_ID);
        } else if (isset($employee->Work_Email)) {
            $field = 'Work_Email';
            $conditions = array('work_email' => $employee->Work_Email);
        } else if (isset($employee->LSUAM_LSU_ID)) {
            $field = 'LSUAM_LSU_ID';
            $conditions = array('school_id' => $employee->LSUAM_LSU_ID);
        } else {
            $errordata = json_encode($employee);
            self::dtrace("  Employee $employee->Legal_First_Name $employee->Legal_Last_Name has no identifying characteristics. Employee record from endpoint follows.");
            self::dtrace("  $errordata\n");
            return 'error';
        }

        // Actually do the lookup.
        $record = $DB->get_record($table, $conditions);
    }

    /**
     * Fixes malformed school ids.
     *
     * @param  @string $schoolid
     *
     * @return @int $schoolid
     */
    public static function fix_wdhrm_schoolid($schoolid) {
        // Get rid of dashes.
        $schoolid = str_replace("-", "", $schoolid);

        // Return the data.
        return (int) $schoolid;
    }

    /**
     * Cleans up unwanted data from the array of users.
     *
     * @param  @object $s
     * @param  @object $employees
     *
     * @return @array
     */
    public static function clean_wdhrm_employees($s, $employees) {
        global $DB;

        // Find the keys for the bad email addresses as defined in settings.
        $bademails = isset($s->bademails) ? $s->bademails : 'unknown@lsu.edu,retemploDNU*@lsu.edu';

        // Create an array from the csv settings.
        $bademails = explode(',', $bademails);

        // Create an empty array for the future.
        $badkeys = array();

        $time = microtime(true);

        // Loop through the array of bad and null emails and find the keys for matching employees.
        foreach ($bademails as $bademail) {
/*
            // Get the emails.
            $emails = array_column($employees, 'Work_Email');

            // Remove the bad emails.
            $badkeys = array_keys($emails, $bademail);
            $missingemails = array_filter($employees,
                function ($employee) {
                    return !isset($employee->Work_Email);
                }
            );

            // Just return the keys.
            $missingkeys = array_keys($missingemails);

            // Merge the missing and bad email arrays.
            $keys = array_merge($badkeys, $missingkeys);
*/

            // This is faster than the above. Leaving the commented out code JIC.

            // Loop through the employees.
            foreach ($employees as $key => $employee) {
                // If we find a bad email, add it to the array.
                if (isset($employee->Work_Email) && $employee->Work_Email == $bademail) {
                    $keys[] = $key;
                // If we find a missing email, add it to the array.
                } else if (!isset($employee->Work_Email)) {
                    $keys[] = $key;
                }
            }
        }

        // Loop through the keys and remove the bad objects from the array.
        foreach ($keys as $key) {
            unset($employees[$key]);
        }

        $newtime = microtime(true);
        $elapsed = round($newtime - $time, 3);
        mtrace("    Cleaning bad emails took $elapsed seconds.");
        // Return the remaining employees.
        return $employees;
    }

    public static function update_wdhrm_statuses_expired() {
        global $DB;
        $sql = 'UPDATE {enrol_workdayhrm} wd SET wd.iscurrent = 0 WHERE wd.iscurrent = 1';
        $updater = $DB->execute($sql);
        return($updater);
    }

    public static function update_wdhrm_employee($record, $employee) {
        global $DB;

        $schoolid = isset($employee->LSUAM_LSU_ID) ? $employee->LSUAM_LSU_ID : null;
        $manschoolid = isset($employee->Manager_LSU_ID) ? $employee->Manager_LSU_ID : null;

        // Update the existing record.
        $record->employee_id = $employee->Employee_ID;
        $record->universal_id = isset($employee->Universal_ID) ? $employee->Universal_ID : null;
        $record->school_id = isset($employee->LSUAM_LSU_ID) ? $employee->LSUAM_LSU_ID : null;
        if (!is_null($schoolid)) {
            $record->school_id = self::fix_wdhrm_schoolid($schoolid);
        }
        $record->work_email = isset($employee->Work_Email) ? $employee->Work_Email : null;
        $record->legal_first_name = self::capit($employee->Legal_First_Name);
        $record->legal_middle_name = isset($employee->Legal_Middle_Name) ? self::capit($employee->Legal_Middle_Name) : null;
        $record->legal_last_name = self::capit($employee->Legal_Last_Name);
        $record->preferred_first_name = isset($employee->Preferred_First_Name) ? self::capit($employee->Preferred_First_Name) : null;
        $record->preferred_middle_name = isset($employee->Preferred_Middle_Name) ? self::capit($employee->Preferred_Middle_Name) : null;
        $record->preferred_last_name = isset($employee->Preferred_Last_Name) ? self::capit($employee->Preferred_Last_Name) : null;
        $record->company_id = isset($employee->Company_ID) ? $employee->Company_ID : null;
        $record->manager_employee_id = isset($employee->Manager_Employee_ID) ? $employee->Manager_Employee_ID : null;
        $record->manager_universal_id = isset($employee->Manager_Universal_ID) ? $employee->Manager_Universal_ID : null;
        $record->manager_school_id = isset($employee->Manager_LSU_ID) ? $employee->Manager_LSU_ID : null;
        if (!is_null($manschoolid)) {
            $record->manager_school_id = self::fix_wdhrm_schoolid($manschoolid);
        }
        $record->iscurrent = 1;
        $record->lastupdated = time();
        unset($record->message);

        // Set the table.
        $table = 'enrol_workdayhrm';

        // Update the enrol_workdayhrm table entry.
        try {
            $update = $DB->update_record($table, $record, $bulk=false);
        }
        catch(Exception $e) {
            $error = $e->getMessage();
        }

        // If we are successful.
        if (isset($update) && $update) {
            self::dtrace("        Updated $record->legal_first_name $record->legal_last_name's information in the enrol_workdayhrm table.");
        } else {
            mtrace("        Error: $record->legal_first_name $record->legal_last_name update failed with: $error.");
        }
        return $record->id;
    }

    /**
     * Inserts new employee from the data provided.
     *
     * @param  @object $s
     * @param  @object $employee
     *
     * @return @object
     */
    public static function insert_wdhrm_employee($s, $employee) {
        global $DB;

        // Build the base object.
        $record = new stdClass();

        // Define table for lookup later.
        $table = 'enrol_workdayhrm';
        $schoolid = isset($employee->LSUAM_LSU_ID) ? $employee->LSUAM_LSU_ID : null;
        $manschoolid = isset($employee->Manager_LSU_ID) ? $employee->Manager_LSU_ID : null;

        // Map the data because I hate school specific tags.
        $record->employee_id = $employee->Employee_ID;
        $record->universal_id = isset($employee->Universal_ID) ? $employee->Universal_ID : null;
        $record->school_id = isset($employee->LSUAM_LSU_ID) ? $employee->LSUAM_LSU_ID : null;
        if (!is_null($schoolid)) {
            $record->school_id = self::fix_wdhrm_schoolid($schoolid);
        }
        $record->work_email = isset($employee->Work_Email) ? $employee->Work_Email : null;
        $record->legal_first_name = $employee->Legal_First_Name;
        $record->legal_middle_name = isset($employee->Legal_Middle_Name) ? $employee->Legal_Middle_Name : null;
        $record->legal_last_name = $employee->Legal_Last_Name;
        $record->preferred_first_name = isset($employee->Preferred_First_Name) ? $employee->Preferred_First_Name : null;
        $record->preferred_middle_name = isset($employee->Preferred_Middle_Name) ? $employee->Preferred_Middle_Name : null;
        $record->preferred_last_name = isset($employee->Preferred_Last_Name) ? $employee->Preferred_Last_Name : null;
        $record->company_id = isset($employee->Company_ID) ? $employee->Company_ID : null;
        $record->manager_employee_id = isset($employee->Manager_Employee_ID) ? $employee->Manager_Employee_ID : null;
        $record->manager_universal_id = isset($employee->Manager_Universal_ID) ? $employee->Manager_Universal_ID : null;
        $record->manager_school_id = isset($employee->Manager_LSU_ID) ? $employee->Manager_LSU_ID : null;
        if (!is_null($manschoolid)) {
            $record->manager_school_id = self::fix_wdhrm_schoolid($manschoolid);
        }
        $record->iscurrent = 1;
        $record->lastupdated = time();

        // Insert the record and return the id.
        try {
        $id = $DB->insert_record($table, $record, $returnid=true);
        } catch(Exception $e) {
            $error = $e->getMessage();
        }
        if (isset($error)) {
var_dump($error);
die();
        }

        $record->id = $id;

        // Return the id.
        return $record;
    }

    public static function workdayhrm_enrollment($s, $course, $user, $enrollstatus) {
        global $CFG, $DB;

        // Instantiate the enroller.
        $enroller = new enrol_workdayhrm;

        // Grab the role id if one is present, otherwise use the Moodle default.
        $roleid = isset($s->studentrole) ? $s->studentrole : 5;

        // Set the time in seconds from epoch.
        $time = time();

        // Add or remove this student or teacher to the course...
        $stu = new stdClass();
        $stu->userid = $user->id;
        $stu->enrol = 'workdayhrm';
        $stu->course = $course->id;
        $stu->time = $time;
        $stu->timemodified = $time;

        // Set this up for getting the enroll instance.
        $etable      = 'enrol';
        $econditions = array('courseid' => $course->id, 'enrol' => $stu->enrol);

        // Get the enroll instance.
        $einstance   = $DB->get_record($etable, $econditions);

        // If we do not have an existing enrollment instance, add it.
        if (empty($einstance)) {
            self::dtrace("    Creating enroll instance for $stu->enrol in course $course->shortname.");
            $enrollid = $enroller->add_instance($course);
            $einstance = $DB->get_record('enrol', array('id' => $enrollid));
            self::dtrace("    Enroll instance for $einstance->enrol with ID: $einstance->id in course $course->shortname has been created.");
        } else {
            self::dtrace("    Existing enrollment instance for $einstance->enrol with ID: $einstance->id in course $course->shortname is already here.");
        }

        // Determine if we're removing or suspending oa user on unenroll.
        $unenroll = $s->unenroll;

        if ($enrollstatus == "unenroll") {
            // If we're removing them from the course.
            if ($unenroll == 1) {
                // Do the nasty.
                $enrolluser   = $enroller->unenrol_user(
                                    $einstance,
                                    $stu->userid);
                self::dtrace("      User $stu->userid unenrolled from course: $stu->course.");
            // Or we're suspending them.
            } else {
                // Do the nasty.
                $enrolluser   = $enroller->update_user_enrol(
                                    $einstance,
                                    $stu->userid, ENROL_USER_SUSPENDED);
                self::dtrace("    User ID: $stu->userid suspended from course: $stu->course.");
            }
        // If we're enrolling a student in the course.
        } else if ($enrollstatus == "enroll") {
            $enrollstart = 0;
            $enrollend = 0;
            // Do the nasty.
            $enrolluser = $enroller->enrol_user(
                              $einstance,
                              $stu->userid,
                              $roleid,
                              $enrollstart,
                              $enrollend,
                              $status = ENROL_USER_ACTIVE);
            self::dtrace("    User ID: $stu->userid enrolled into course: $stu->course.");
        }

        return true;
    }

    public static function wdhrm_employee_helper($employee, $s) {
        global $CFG;
        // Check to see if the employee exists in the interstital database.
        $exists = self::get_wdhrm_exists($s, $employee);

        // Count the fields.
        $ecount = count((array)$employee);

        // If they exist, do stuff.
        if ($exists->message == 'exists') {

            // Set the user to current.
            $updated = self::update_wdhrm_employee($exists, $employee);

            // Let us know how many fields this user has.
            self::dtrace("      Exsiting employee $employee->Legal_First_Name $employee->Legal_Last_Name has $ecount fields.");

            // Only display this if we ABSOLUTELY need it.
            if ($CFG->debug > 0 && $CFG->debugdisplay == 1) {
                // Display the data we found.
                if(isset($employee->Employee_ID)) {
                    self::dtrace("      Employee_ID: $employee->Employee_ID");
                } else {
                    self::dtrace("      Employee_ID is NULL");
                }
                if(isset($employee->Universal_ID)) {
                    self::dtrace("      Universal_ID: $employee->Universal_ID");
                } else {
                    self::dtrace("      Universal_ID is NULL");
                }
                if(isset($employee->LSUAM_LSU_ID)) {
                    self::dtrace("      LSUAM_LSU_ID: $employee->LSUAM_LSU_ID");
                } else {
                    self::dtrace("      LSUAM_LSU_ID is NULL");
                }
                if(isset($employee->Work_Email)) {
                    self::dtrace("      Work_Email: $employee->Work_Email");
                } else {
                    self::dtrace("      Work_Email is NULL");
                }
                if(isset($employee->Legal_First_Name)) {
                    self::dtrace("      Legal_First_Name: $employee->Legal_First_Name");
                } else {
                    self::dtrace("      Legal_First_Name is NULL");
                }
                if(isset($employee->Legal_Middle_Name)) {
                    self::dtrace("      Legal_Middle_Name: $employee->Legal_Middle_Name");
                } else {
                    self::dtrace("      Legal_Middle_Name is NULL");
                }
                if(isset($employee->Legal_Last_Name)) {
                    self::dtrace("      Legal_Last_Name: $employee->Legal_Last_Name");
                } else {
                    self::dtrace("      Legal_Last_Name is NULL");
                }
                if(isset($employee->Preferred_First_Name)) {
                    self::dtrace("      Preferred_First_Name: $employee->Preferred_First_Name");
                } else {
                    self::dtrace("      Preferred_First_Name is NULL");
                }
                if(isset($employee->Preferred_Middle_Name)) {
                    self::dtrace("      Preferred_Middle_Name: $employee->Preferred_Middle_Name");
                } else {
                    self::dtrace("      Preferred_Middle_Name is NULL");
                }
                if(isset($employee->Preferred_Last_Name)) {
                    self::dtrace("      Preferred_Last_Name: $employee->Preferred_Last_Name");
                } else {
                    self::dtrace("      Preferred_Last_Name is NULL");
                }
                if(isset($employee->Company_ID)) {
                    self::dtrace("      Company_ID: $employee->Company_ID");
                } else {
                    self::dtrace("      Company_ID is NULL");
                }
                if(isset($employee->Manager_Employee_ID)) {
                    self::dtrace("      Manager_Employee_ID: $employee->Manager_Employee_ID");
                } else {
                    self::dtrace("      Manager_Employee_ID is NULL");
                }
                if(isset($employee->Manager_Universal_ID)) {
                    self::dtrace("      Manager_Universal_ID: $employee->Manager_Universal_ID");
                } else {
                    self::dtrace("      Manager_Universal_ID_NULL");
                }
                if(isset($employee->Manager_LSU_ID)) {
                    self::dtrace("      Manager_LSU_ID: $employee->Manager_LSU_ID");
                } else {
                    self::dtrace("      Manager_LSU_ID_NULL");
                }
            }
            return $exists;
        } else if ($exists->message == 'new') {
            self::dtrace("      Inserting new employee $employee->Employee_ID: $employee->Legal_First_Name $employee->Legal_Last_Name with $ecount fields.");
            $insert = self::insert_wdhrm_employee($s, $employee);
            mtrace("      Inserted new employee $employee->Employee_ID: $employee->Legal_First_Name $employee->Legal_Last_Name id $insert->id with $ecount fields.");
            return ($insert);
        } else {
            mtrace("      Error - No identifying fields found for user $employee->Legal_First_Name $employee->Legal_Last_Name.");
        }
    }

    public static function get_clean_employees() {
        global $DB;
        $table = 'enrol_workdayhrm';
        $conditions = null;
        $wdemployees = $DB->get_records($table, $conditions, $sort='', $fields='*', $limitfrom=0, $limitnum=0);
        return $wdemployees;
    }

    public static function dtrace($message) {
        global $CFG;
        if ($CFG->debugdisplay == 1) {
            $mtrace = mtrace($message);
            return $mtrace;
        }
    }

   public static function capit($s) {
        $charcount = strlen($s);
        if ($charcount > 1 && ($s === strtoupper($s) || $s === strtolower($s))) {
            $words = array();
            // Split the string into an array of words using dashes as boundaries.
            if (preg_grep('/-/', array($s))) {
                $words = explode('-', $s);
            } else if (preg_grep('/\s/', array($s))) {
                $words = explode(" ", $s);
            } else if (preg_grep('/\'/', array($s))) {
                $words = explode("'", $s);
            } else {
                $capitalizedstring = ucfirst(strtolower($s));
            }

            $capitalizedwords = array();
            foreach ($words as $word) {
                // Capitalize the first letter of each word.
                $capitalizedwords[] = ucfirst(strtolower($word));
            }
            // Join the capitalized words back into a string.
            if (preg_grep('/-/', array($s))) {
                $capitalizedstring = implode('-', $capitalizedwords);
            } else if (preg_grep('/\s/', array($s))) {
                $capitalizedstring = implode(' ', $capitalizedwords);
            } else if (preg_grep('/\'/', array($s))) {
                $capitalizedstring = implode('\'', $capitalizedwords);
            }
             self::dtrace("      Capitalizing $s to $capitalizedstring");
            return $capitalizedstring;
        } else if ($charcount == 1) {
            $capitalizedstring = ucfirst(strtolower($s));
             self::dtrace("      Capitalizing $s to $capitalizedstring");
            return $capitalizedstring;
        } else {
             self::dtrace("      Leaving $s alone");
            return $s;
        }
    }

    /**
     * Creates or updates users as needed.
     *
     * @param  @object $employee
     * @param  @object $s
     * @return @object $muser
     */
    public static function create_update_user($student, $s) {
        global $CFG, $DB;

        // Build the auth methods and choose the default one.
        $auth = explode(',', $CFG->auth);
        $auth = reset($auth);

        // Set up the user object.
        $user               = new stdClass();
        $user->username     = strtolower($student->work_email);
        $user->idnumber     = isset($student->school_id) ? $student->school_id : '';
        $user->email        = strtolower($student->work_email);
        $user->firstname    = empty($student->preferred_first_name) ? $student->legal_first_name : $student->preferred_first_name;
        $user->firstname    = self::capit($user->firstname);
        $user->lastname     = $student->preferred_last_name != $student->legal_last_name ? $student->preferred_last_name : $student->legal_last_name;
        $user->lastname     = self::capit($user->lastname);
        $user->lang         = $CFG->lang;
        $user->auth         = $auth;
        $user->confirmed    = 1;
        $user->timemodified = time();
        $user->mnethostid   = $CFG->mnet_localhost_id;

        // Get the configured home domain.
        $homedomain = strtolower($s->homedomain);

        // Get the configured external domain.
        $extdomain = strtolower($s->extdomain);

        // Isolate the domain.
        $inputdomain = strtolower(substr(strrchr($student->work_email, "@"), 0));

        // perform the logic.
        if ($inputdomain == $homedomain) {
            // Set the username to lowercase email.
            $username = strtolower($student->work_email);
        } else {
            // Build the new username (lowercase it in case the admin is a moron).
            $username = strtolower(str_replace('@', '_', $student->work_email) . '#ext#' . $extdomain);
        }

        // Build the conditions to get some users.
        $conditions = array();
        $conditions[] = array("idnumber"=>$student->school_id, "mnethostid"=>1, "deleted"=>0, "confirmed"=>1, "suspended"=>0);
        $conditions[] = array("username"=>$user->username, "mnethostid"=>1, "deleted"=>0, "confirmed"=>1, "suspended"=>0);
        $conditions[] = array("username"=>$username, "mnethostid"=>1, "deleted"=>0, "confirmed"=>1, "suspended"=>0);
        $conditions[] = array("email"=>$user->email, "mnethostid"=>1, "deleted"=>0, "confirmed"=>1, "suspended"=>0);

        $muser = new stdClass();
        $counter = 0;
        foreach ($conditions as $condition) {
            // Increment the counter.
            $counter++;

            self::dtrace(      $counter . ': ' . json_encode($condition));

            // Get the Moodle user.
            $muser = self::get_matching_employee($condition);
            if (isset($muser->id)) {
                self::dtrace("      We found the matching employee id: $muser->id, email: $muser->email, idnumber: $muser->idnumber, username: $muser->username.");

                // Get the search condition and value for logs.
                $conkeys = array_keys($condition);
                $convals = array_values($condition);
                $searchcon = array_shift($conkeys);
                $searchval = array_shift($convals);

                // I want to break free.
                break;
            }
        }

        // We don't have a nonexistent user or an existing user.
        if (!isset($muser->id) && !isset($muser->notreallyhere)) {
            self::dtrace("      We did not find a matching Moodle employee for email or username: $user->email or $username, idnumber: $student->school_id. let's create them.");
            $muser = self::create_moodle_user($user);
        // We have a perfect matching user who should exist.
        } else if (isset($muser->id) && !isset($muser->notreallyhere)
                   && $muser->idnumber == $user->idnumber
                   && $muser->email == $user->email
                   && ($muser->username == $user->username
                      || $muser->username == $username)
                   && $muser->lastname == $user->lastname) {
            self::dtrace("      We found a perfect match for " .
                                "$muser->firstname " .
                                "$muser->lastname with email " .
                                "$muser->email and idnumber " .
                                "$muser->idnumber and Moodle id " .
                                "$muser->id using " .
                                "$searchcon matching " .
                                "$searchval, moving on.");
        // We have a perfect matching user but they sould not exist (we should never be here).
        } else if (isset($muser->id) && isset($muser->notreallyhere)
                   && $muser->idnumber == $user->idnumber
                   && $muser->email == $user->email
                   && ($muser->username == $user->username
                      || $muser->username == $username)
                   && $muser->lastname == $user->lastname) {
            self::dtrace("      We found a perfect match for " .
                                "$muser->firstname " .
                                "$muser->lastname with email " .
                                "$muser->email and idnumber " .
                                "$muser->idnumber and Moodle id " .
                                "$muser->id using " .
                                "$searchcon matching " .
                                "$searchval, but they should not exist.");
        // We have a partial matching user who should exist.
        } else if (!isset($muser->notreallyhere)) {
            self::dtrace("      We found a partial match for first name " .
                                "$muser->firstname / $user->firstname and last name " .
                                "$muser->lastname / $user->lastname with email " .
                                "$muser->email / $user->email and/or idnumber " .
                                "$muser->idnumber / $user->idnumber and Moodle id " .
                                "$muser->id using " .
                                "$searchcon matching " .
                                "$searchval, updating them.");

            // Set the userid.
            $user->id = $muser->id;

            // Store the firstname in a temp location.
            $user->tempname = $user->firstname;

            // Unset the firstname
            unset($user->firstname);
            
            // Set the user table.
            $table = 'user';
            try {
                $update = $DB->update_record($table, $user, $bulk=false);
            }
            catch(Exception $e) {
                $update = false;
                $error = $e->getMessage();
            }

            // Reset the firstname.
            $user->firstname = $user->tempname;

            // Unset the tempname.
            unset($user->tempname);
            if ($update) {
                mtrace("      Updated $user->firstname $user->lastname's information");
                self::dtrace("      Beginning any enrollments for $user->firstname $user->lastname.");
                $muser = $DB->get_record($table, array("id"=>$user->id), $fields='*', $strictness=IGNORE_MISSING);
            } else {
                mtrace("      Updating $user->firstname $user->lastname failed with error $error.");
            }
        } else {
                mtrace("      This was a duplicate record. We skipped it.");
        }

        return $muser;
    }

    public static function create_moodle_user($user) {
        global $CFG, $DB;
        $table = 'user';
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->libdir . '/moodlelib.php');
        self::dtrace("      Creating username / email: $user->email, idnumber $user->idnumber, full name: $user->firstname $user->lastname.");
        $muserid = user_create_user($user, false, false);
        $muser = $DB->get_record($table, array("id"=>$muserid), $fields='*', $strictness=IGNORE_MISSING);
        mtrace("      Created userid: $muser->id username / email: $muser->email, idnumber $muser->idnumber, full name: $muser->firstname $muser->lastname.");
        return $muser;
    }

    public static function get_matching_employee($condition) {
        global $DB;
        $table = 'user';
        $cdata = json_encode($condition);
        $musers = $DB->get_records($table, $condition, $sort='', $fields='*', $limitfrom=0, $limitnum=0);
        $musercount = count($musers);
        if ($musercount > 1) {
            foreach ($musers as $muser) {
                mtrace("We found more than one matching Moodle user.
                        Stopping the update of $muser->firstname, $muser->lastname,
                        id: $muser->id,
                        email: $muser->email,
                        username: $muser->username,
                        idnumber: $muser->idnumber.");
            }
            mtrace("Search condition: $cdata");
            $muser->notreallyhere = 1;
        } else if (is_array($musers)) {
            $muser = reset($musers);
        } else {
            $muser = null;
        }
        return $muser;
    }

    /**
     * Contructs and sends error emails using Moodle functionality.
     *
     * @package   enrol_workdayhrm
     *
     * @param     @object $emaildata
     * @param     @object $s
     *
     * @return    @bool
     */
    public static function send_wdhrm_email($emaildata, $s) {
        global $CFG, $DB;

        // Get email subject from email log.
        $emailsubject = $emaildata->subject;

        // Get email content from email log.
        $emailcontent = $emaildata->body;

        // Grab the list of usernames from Moodle.
        $usernames = explode(",", $s->contacts);

        // Set up the users array.
        $users = array();

        // Loop through the usernames and add each user object to the user array.
        foreach ($usernames as $username) {

            // Make sure we have no spaces.
            $username = trim($username);

            // Add the user object to the array.
            $users[] = $DB->get_record('user', array('username' => $username));
        }

        // Send an email to each of the above users.
        foreach ($users as $user) {

            // Email the message.
            email_to_user($user,
                get_string("workdayhrm_emailname", "enrol_workdayhrm"),
                $emailsubject . " - " . $CFG->wwwroot,
                $emailcontent);
        }
    }
}

class enrol_workdayhrm extends enrol_plugin {

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
