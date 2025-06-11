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
 * @copyright 2024 onwards LSUOnline & Continuing Education
 * @copyright 2024 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 */
class workdaystudent {

    // Start the dtrace counter.
    private static $dtc = 0;

    // Reset this after each loop is done.
    public static function resetdtc() {
        self::$dtc = 0;
    }

    /**
     * Grabs the settings from config_plugins.
     *
     * @return @object $s
     */
    public static function get_settings() {
        $s = new stdClass();

        // Get the settings.
        $s = get_config('enrol_workdaystudent');

        return $s;
    }

    /**
     * Retrieves faculty preferences for a given user.
     *
     * If personal preferences are missing, return the global settings or fallbacks.
     *
     * @param int $userid The user ID.
     * @return stdClass An object containing the user's preferences.
     */
    public static function wds_get_faculty_preferences($mshell) {
        global $DB;

        // Set this for use later.
        $userid = $mshell->userid;

        // Validate user ID.
        if (!is_numeric($userid) || $userid <= 0) {
            var_dump($mshell);
            throw new invalid_parameter_exception('Invalid user ID provided.');
        }

        // Retrieve user preferences related to 'wdspref_'.
        $sql = "SELECT * FROM {user_preferences}
            WHERE name LIKE 'wdspref_%'
                AND userid = ?";

        // Get the data.
        $preferences = $DB->get_records_sql($sql, [$userid]);

        // Get global settings.
        $s = self::get_settings();

        // Define default values.
        $defaults = [
            'wdspref_createprior' => isset($s->createprior) ? (int) $s->createprior : 28,
            'wdspref_enrollprior' => isset($s->enrollprior) ? (int) $s->enrollprior : 14,
            'wdspref_courselimit' => isset($s->numberthreshold) ? (int) $s->numberthreshold : 8000,
            'wdspref_format' => 'topics'
        ];

        // Initialize user preferences with defaults.
        $userprefs = new stdClass();
        foreach ($defaults as $key => $value) {
            $shortkey = str_replace('wdspref_', '', $key);
            if ($shortkey == 'format') {
                $userprefs->$shortkey = $value;
            } else {
                $userprefs->$shortkey = (int) $value;
            }
        }

        // Override defaults with retrieved preferences.
        foreach ($preferences as $pref) {
            $shortkey = str_replace('wdspref_', '', $pref->name);
            if ($shortkey == 'format') {
                $userprefs->$shortkey = $pref->value;
            } else {
                $userprefs->$shortkey = (int) $pref->value;
            }
        }

        // Get any unwants we might have that are relvant to this shell.
        $unwants = self::wds_get_unwants($mshell);

        // Get the unwanted or sepcifivally wanted count.
        $uwcount = count($unwants);

        // Build out the arrays.
        $userprefs->unwants = [];
        $userprefs->wants = [];

        // Loop through the data.
        foreach($unwants as $unwant) {

            // If the sectionid is unwanted add it to the unwants array.
            if ($unwant->unwanted === "1") {
                $userprefs->unwants[] = $unwant->sectionid;
            }

            // If the sectionid is wanted add it to the wants array.
            if ($unwant->unwanted === "0") {
                $userprefs->wants[] = $unwant->sectionid;
            }
        }

        return $userprefs;
    }

    public static function wds_get_unwants($mshell) {
        global $DB;

        // Build the SQL.
        $usql = "SELECT *
            FROM {block_wdsprefs_unwants}
             WHERE userid = $mshell->userid
                 AND sectionid IN ($mshell->sectionids)";

        $unwants = $DB->get_records_sql($usql);

        return $unwants;
    }

    public static function get_students($s, $periodid, $studentid) {

        // Log what we're doing.
        mtrace("Fetching students from webservice endpoint.");

        // Set the start time.
        $starttime = microtime(true);

        // Set the endpoint.
        $endpoint = 'students';

        // Set some aprms up.
        $parms = [];

        // Set the required campus id.
        $parms['Institution!Academic_Unit_ID'] = $s->campus;

        // Set the required term.
        $parms['Academic_Period!Academic_Period_ID'] = $periodid;

        // Set the Student ID if we're looking up a single student.
        if ($studentid !== '') {
            $parms['Universal_Id'] = $studentid;
        }

        $parms['format'] = 'json';

        // Build out the settins based on settings, endpoint, and parms.
        $s = self::buildout_settings($s, $endpoint, $parms);

        // Get the data.
        $students = self::get_data($s);

        if (!is_array($students) || empty($students)) {
            self::dtrace("$periodid contains no students, this is probably a sub-semester.");
            return false;
        }

        // Get a count of students.
        $studentcount = count($students);

        // Populate some stuff for later.
        $students[0]->studentcount = $studentcount;

        // Set the endtime.
        $endtime = microtime(true);

        // Calculate the elapsed time.
        $elapsedtime = round($endtime - $starttime, 1);

        // Log what we did.
        mtrace("Retreived $studentcount students in $elapsedtime seconds.");

        // Return the data.
        return $students;
    }

    public static function get_guild($s) {

        // Set the endpoint.
        $endpoint = 'guild';

        // Set some parms up.
        $parms['format'] = 'json';

        // Build out the settins based on settings, endpoint, and parms.
        $bs = self::buildout_settings($s, $endpoint, $parms);

        // Get the units.
        $guilds = self::get_data($bs);

        return $guilds;
    }

    public static function get_uid_sfpr($guild) {
        $student = $guild->SFPR_Student;

        // Get the UID.
        preg_match('/(.+)\((\d+)\)/', $student, $matches);

        // If we found one, overwrite it appropriately.
        $guild->SFPR_StudentName = isset($matches[1]) ? trim($matches[1]) : $guild->SFPR_Student;
        $guild->SFPR_UID = isset($matches[2]) ? $matches[2] : $guild->SFPR_Student;

        // Return the object.
        return $guild;
    }

    public static function get_period_dates($s, $period) {

        // Set the endpoint.
        $endpoint = 'dates';

        // Set some parms up.
        $parms = [];
        $parms['Academic_Period!Academic_Period_ID'] = $period->Academic_Period_ID;
        $parms['format'] = 'json';

        // Build out the settins based on settings, endpoint, and parms.
        $bs = self::buildout_settings($s, $endpoint, $parms);

        // Get the units.
        $dates = self::get_data($bs);

        return $dates;
    }

    public static function get_pg_dates($s, $period) {

        // Set the endpoint.
        $endpoint = 'dates';

        // Set some parms up.
        $parms = [];
        $parms['Academic_Period!Academic_Period_ID'] = $period->Academic_Period_ID;
        $parms['format'] = 'json';

        // Build out the settins based on settings, endpoint, and parms.
        $bs = self::buildout_settings($s, $endpoint, $parms);

        // Get the units.
        $dates = self::get_data($bs);

        // Build the date object.
        $dateobj = new stdClass();
        $dateobjs = [];

        // Add the academic period id.
        $dateobj->academic_period_id = $period->Academic_Period_ID;

        foreach ($dates as $date) {

            // Fix the date and return an int in UTC.
            $realdate = self::format_pg_date($date->Date);

            $realdateobj = new DateTime($realdate);
            $timestamp = $realdateobj->getTimestamp();

            // Clean the academic level.
            $cleanal = str_replace(" ", "_", $date->Acad_Level);

            // Clean the date control.
            $cleanctrl = str_replace(' ', '_', $date->Date_Control);

            // Build the entry.
            $dateobj->{$cleanal . "_" . $cleanctrl} = $timestamp;

        //    $dateobjs[] = $dateobj;
        }

        return $dateobj;
    }

    // TODO: Possibly deprecated, please remove.
    public static function clean_honors_grade($grade) {

        // First get universal ID.
        preg_match('/^(.+)\s+\((HNR)\)/', $grade, $matches);

        // Return the matches.
        return $matches[1];
    }

    public static function clean_sfpr_student($student) {

        // First get universal ID.
        preg_match('/^(.+)\s+\((\d+)\)/', $student, $matches);

        // Remove the 1st element.
        array_shift($matches);

        // Return the matches.
        return $matches;
    }

    public static function id_fake_courses($course) {

        // Identify courses with *s or all 0 as their course number.
        preg_match('/\*|0000/', $course->Course_Number, $match);

        // Return the matches.
        return $match;
    }

    public static function check_period($period) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_periods';

        // Set the parameters.
        $parms = ['academic_period_id' => $period->Academic_Period_ID];

        // Get the academic unit record.
        $ap = $DB->get_record($table, $parms);

        return $ap;
    }

    public static function check_period_date($date) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_pgc_dates';

        // Set the parameters.
        $parms = ['academic_period_id' => $date->academic_period_id,
            'academic_level' => $date->Acad_Level,
            'date_type' => $date->Date_Control];

        // Get the academic unit record.
        $ap = $DB->get_record($table, $parms);

        return $ap;
    }

    public static function check_unit($unit) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_units';

        // Set the parameters.
        $parms = ['academic_unit_id' => $unit->Academic_Unit_ID];

        // Get the academic unit record.
        $au = $DB->get_record($table, $parms);

        return $au;
    }

    public static function get_academic_year($period) {

        // Set this to avoid issues.8
        $ayear = [];

        if (!isset($period->Academic_Year)) {
            var_dump($period);
            return false;
        }

        // Find the year.
        preg_match('/(\d\d\d\d-\d\d\d\d).*/', $period->Academic_Year, $ayear);

        // Make sure we found the year.
        $academicyear = isset($ayear[1]) ? $ayear[1] : 0;

        //Return the year.
        return $academicyear;
    }

    public static function get_period_year($period) {
        if (isset($period->academic_period_id) && !isset($period->Academic_Period_ID)) {
            $period->Academic_Period_ID = $period->academic_period_id;
        } elseif (isset($period->Academic_Period_ID) && !isset($period->academic_period_id)) {
            $period->academic_period_id = $period->Academic_Period_ID;
        } else {
            mtrace("Error! No academic period in the period, look into this");
            var_dump($period);
            return false;
        }

        // Find the year.
        preg_match('/\d{4}/', $period->Academic_Period, $pyear);

        // Make sure we found the year.
        $periodyear = isset($pyear[0]) ? $pyear[0] : 0;

        //Return the year.
        return $periodyear;
    }

    public static function update_period($period, $ap) {
        global $DB;

        // Build the cloned object.
        $ap2 = unserialize(serialize($ap));

        // Set start dates.
        $startdate = strtotime($period->Start_Date);
        $enddate = strtotime($period->End_Date);

        // Get the period year.
        $periodyear = workdaystudent::get_period_year($period);

        // Get the academic year.
        $academicyear = workdaystudent::get_academic_year($period);

        // Keep the ids from $ap and populate the rest from $period.
        $ap2->academic_period_id = $period->Academic_Period_ID;
        $ap2->academic_period = $period->Academic_Period;
        $ap2->period_type = $period->Period_Type;
        $ap2->period_year = $periodyear;
        $ap2->academic_calendar = $period->Academic_Calendar;
        $ap2->academic_year = $academicyear;
        $ap2->start_date = (string) $startdate;
        $ap2->end_date = (string) $enddate;
        $ap2->enabled = $ap->enabled;

        // Compare the objects.
        if (get_object_vars($ap) === get_object_vars($ap2)) {
            self::dtrace("   - Academic period matched, skipping.");
            return $ap;
        } else {

            // Set the table.
            $table = 'enrol_wds_periods';

            // Update the record.
            $success = $DB->update_record($table, $ap2, false);

            if ($success) {
                self::dtrace("   - Academic period $ap->academic_period_id has been updated from the endpoint.");

                // Return the updated object.
                return $ap2;
            } else {
                mtrace("   - Error! Updating $ap->academic_period_id failed and has not been updated.");

                // Return the original object.
                return $ap;
            }
        }
    }

    public static function insert_period($period) {
        global $DB;

        // Only deal with actual periods with years and dates associated with them.
        if (isset($period->Academic_Year) && (isset($period->academic_period_id) || isset($period->Academic_Period_ID))) {

            // Set the table.
            $table = 'enrol_wds_periods';

            // Create the object.
            $tap = new stdClass();

            // Set start dates.
            $startdate = strtotime($period->Start_Date);
            $enddate = strtotime($period->End_Date);

            // Get the period year.
            $periodyear = workdaystudent::get_period_year($period);

            // Get the academic year.
            $academicyear = workdaystudent::get_academic_year($period);

            // Populate the temporary period table.
            $tap->academic_period_id = isset($period->Academic_Period_ID)
                ? $period->Academic_Period_ID
                : $period->academic_period_id;
            $tap->academic_period = $period->Academic_Period;
            $tap->period_type = $period->Period_Type;
            $tap->period_year = $periodyear;
            $tap->academic_calendar = $period->Academic_Calendar;
            $tap->academic_year = $academicyear;
            $tap->start_date = $startdate;
            $tap->end_date = $enddate;

            $ap = $DB->insert_record($table, $tap);
            self::dtrace("   - Inserted academic_period_id: $tap->academic_period_id.");

            return $tap;
        } else {
            var_dump($period);
            return false;
        }
    }

    public static function insert_period_date($pdate) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_pgc_dates';

        // Create the object.
        $pd = new stdClass();

        // Populate the temporary period date table.
        $pd->academic_period_id = $pdate->academic_period_id;
        $pd->academic_level = $pdate->Acad_Level;
        $pd->date_type = $pdate->Date_Control;
        $pd->date = strtotime($pdate->Date);

        $ap = $DB->insert_record($table, $pd);
        self::dtrace("    - Inserted $pd->academic_level $pd->date_type for $pd->academic_period_id.");

        return $ap;
    }

    public static function update_period_date($pdate, $pd) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_pgc_dates';

        // Create the object.
        $pd2 = new stdClass();

        // Populate the temporary period date table.
        $pd2->id = $pd->id;
        $pd2->academic_period_id = $pdate->academic_period_id;
        $pd2->academic_level = $pdate->Acad_Level;
        $pd2->date_type = $pdate->Date_Control;
        $pd2->date = (string) strtotime($pdate->Date);

        if (get_object_vars($pd) !== get_object_vars($pd2)) {
            $ap = $DB->update_record($table, $pd2, false);
            self::dtrace("    - Updated $pd2->academic_level $pd2->date_type for $pd2->academic_period_id.");
            return $pd2;
        } else {
            self::dtrace("    - $pd2->academic_level $pd2->date_type records matched perfectly, skipping.");
            return $pd2;
        }
    }

    public static function check_section($section) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_sections';

        // Set the parameters.
        $parms = ['section_listing_id' => $section->Section_Listing_ID];

        // Get the academic unit record.
        $as = $DB->get_record($table, $parms);

        return $as;
    }

    public static function check_course($course) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_courses';

        // Set the parameters.
        $parms = ['course_listing_id' => $course->Course_Listing_ID];

        // Get the academic unit record.
        $ac = $DB->get_record($table, $parms);

        return $ac;
    }

    public static function update_section($section, $as) {
        global $DB;

        // Build the cloned object.
        $as2 = unserialize(serialize($as));

        // Keep id, section_listing_id, idnumber, and status from $as and populate the rest from $section.
        $as2->course_section_definition_id = $section->Course_Section_Definition_ID;
        $as2->section_number = $section->Section_Number;
        $as2->course_definition_id = $section->Course_Definition_ID;
        $as2->course_listing_id = $section->Course_Listing_ID;
        $as2->course_subject_abbreviation = $section->Course_Subject_Abbreviation;
        $as2->academic_unit_id = $section->Academic_Unit_ID;
        $as2->academic_period_id = $section->Academic_Period_ID;
        $as2->course_section_title = $section->Course_Section_Title;
        $as2->course_section_abbrev_title = $section->Course_Section_Abbreviated_Title;
        $as2->delivery_mode = $section->Delivery_Mode;
        $as2->class_type = $section->Class_Type;
        $as2->controls_grading = $section->Controls_Grading;
        $as2->wd_status = isset($section->Course_Section_Status) ? $section->Course_Section_Status : 'pending';

        // Compare the objects.
        if (get_object_vars($as) === get_object_vars($as2)) {
            self::dtrace("Section $section->Section_Listing_ID matched stored value, skipping.");

            return $as;
        } else {

            // Set the table.
            $table = 'enrol_wds_sections';

            // Update the record.
            $success = $DB->update_record($table, $as2, false);

            if ($success) {
                self::dtrace("Section $as->section_listing_id has been updated from the endpoint.");

                // Return the updated object.
                return $as2;
            } else {
                mtrace("Error! Updating $as->section_listing_id failed and has not been updated.");

                // Return the original object.
                return $as;
            }
        }
    }

    public static function insert_section($section) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_sections';

        // Create the object.
        $tas = new stdClass();

        // Build the object from $section.
        $tas->section_listing_id = $section->Section_Listing_ID;
        $tas->course_section_definition_id = $section->Course_Section_Definition_ID;
        $tas->section_number = $section->Section_Number;
        $tas->course_definition_id = $section->Course_Definition_ID;
        $tas->course_listing_id = $section->Course_Listing_ID;
        $tas->course_subject_abbreviation = $section->Course_Subject_Abbreviation;
        $tas->academic_unit_id = $section->Academic_Unit_ID;
        $tas->academic_period_id = $section->Academic_Period_ID;
        $tas->course_section_title = $section->Course_Section_Title;
        $tas->course_section_abbrev_title = $section->Course_Section_Abbreviated_Title;
        $tas->delivery_mode = $section->Delivery_Mode;
        $tas->class_type = $section->Class_Type;
        $tas->idnumber = null;
        $tas->controls_grading = $section->Controls_Grading;
        $tas->wd_status = isset($section->Course_Section_Status) ? $section->Course_Section_Status : 'pending';

        $as = $DB->insert_record($table, $tas);
        self::dtrace("Inserted section_listing_id: $tas->section_listing_id.");

        return $as;
    }

    public static function update_course($course, $ac) {
        global $DB;

        // Build the cloned object.
        $ac2 = unserialize(serialize($ac));

        // Keep the id and course_listing_id from $ac and populate the rest from $course.
        $ac2->academic_unit_id = $course->Academic_Unit_ID;
        $ac2->course_definition_id = $course->Course_Definition_ID;
        $ac2->course_number = $course->Course_Number;
        $ac2->course_subject_abbreviation = $course->Course_Subject_Abbreviation;
        $ac2->course_subject = $course->Course_Subject;
        $ac2->subject_code = $course->Subject_Code;
        $ac2->course_abbreviated_title = $course->Course_Abbreviated_Title;
        $ac2->academic_level = $course->Academic_Level;

        // Compare the objects.
        if (get_object_vars($ac) === get_object_vars($ac2)) {
            self::dtrace("    Course $ac->course_listing_id matched $course->Course_Listing_ID, skipping.");
            return $ac;
        } else {

            // Set the table.
            $table = 'enrol_wds_courses';

            // Update the record.
            $success = $DB->update_record($table, $ac2, false);

            if ($success) {
                self::dtrace("    Course $ac->course_listing_id has been updated from the endpoint.");

                // Return the updated object.
                return $ac2;
            } else {
                mtrace("    Error! Updating $ac->course_listing_id failed and has not been updated.");

                // Return the original object.
                return $ac;
            }
        }
    }

    public static function get_prevdays_date($xdays) {

        // Build the date.
        $date = new DateTime();

        // Set the timezone.
        $date->setTimezone(new DateTimeZone('America/Chicago'));

        // Modify the date.
        $date->modify('-' . $xdays . ' days');

        // Set the time to midnight.
        $date->setTime(0, 0);

        // Set the fdate variable for use.
        $fdate = $date->format('Y-m-d\TH:i:s');

        return $fdate;
    }

    public static function insert_course($course) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_courses';

        // Create the object.
        $tac = new stdClass();

        // Build the object from $unit.
        $tac->course_listing_id = $course->Course_Listing_ID;
        $tac->academic_unit_id = $course->Academic_Unit_ID;
        $tac->course_definition_id = $course->Course_Definition_ID;
        $tac->course_number = $course->Course_Number;
        $tac->course_subject_abbreviation = $course->Course_Subject_Abbreviation;
        $tac->course_subject = $course->Course_Subject;
        $tac->subject_code = $course->Subject_Code;
        $tac->course_abbreviated_title = $course->Course_Abbreviated_Title;
        $tac->academic_level = $course->Academic_Level;

        $ac = $DB->insert_record($table, $tac);
        self::dtrace("      Inserted course_listing_id: $tac->course_listing_id.");

        return $ac;
    }

    public static function insert_update_student_enrollment($s, $enrollment, $unenrolls, $enrolls, $donothings) {

        // Enrollment is missing universal ID. Log and move on.
        if (!isset($enrollment->Universal_Id)) {
            $fullname = isset($enrollment->Full_Legal_Name) ? $enrollment->Full_Legal_Name : 'Someone';
            $email = isset($enrollment->LSUAM_Institutional_Email) ? $enrollment->LSUAM_Institutional_Email : $fullname;
            mtrace("Error! $enrollment->Section_Listing_ID missing universal ID for $email.");
            return false;
        }

        // Check to see if the enrollment record exists.
        $as = self::check_student_enrollment($enrollment);

        $grading_basis = isset($enrollment->Grading_Basis) ? $enrollment->Grading_Basis : 'Graded';

        $gsid = isset($enrollment->Student_Grading_Scheme_ID)
            ? $enrollment->Student_Grading_Scheme_ID
            : $s->campusname . ' Standard Grading Scheme';

        // It exists and does not match registration status, update it.
        if (isset($as->id) &&
            isset($enrollment->Registered_Date) &&
            ($as->grading_scheme != $gsid ||
            $as->grading_basis != $grading_basis ||
            $as->credit_hrs != $enrollment->Units ||
            $as->registered_date != $enrollment->Registered_Date ||
            $as->registration_status != $enrollment->Registration_Status)
        ) {
            self::dtrace("Found interstitial enrollment record that requires an update with id: $as->id.");
            $as = self::update_student_enrollment($s, $enrollment, $unenrolls, $enrolls, $donothings, $as);

        } else if (isset($as->id) && !isset($enrollment->Registered_Date) && (
            $as->grading_scheme != $gsid ||
            $as->grading_basis != $grading_basis ||
            $as->credit_hrs != $enrollment->Units ||
            $as->registration_status != $enrollment->Registration_Status
        )) {
            self::dtrace("Found interstitial enrollment record that requires an update with id: $as->id.");
            $as = self::update_student_enrollment($s, $enrollment, $unenrolls, $enrolls, $donothings, $as);

        // It does not exist, create it.
        } else if (!isset($as->id)) {
            $as = self::insert_student_enrollment($s, $enrollment, $unenrolls, $enrolls, $donothings);
            self::dtrace("No enrollment record for: $enrollment->Universal_Id in $enrollment->Section_Listing_ID. Created it with id $as.");

        // It exists and matches, log it.
        } else {
            self::dtrace("Found interstitial enrollment record with id: $as->id. No update required.");
        }

        return $as;
    }

    public static function dateconv($datestr) {

        // Build the string into a date.
        $date = new DateTime($datestr);

        // Convert the date into a timestamp.
        $timestamp = $date->getTimestamp();

        // Return it.
        return (string) $timestamp;
    }

    public static function update_student_enrollment($s, $enrollment, $unenrolls, $enrolls, $donothings, $as) {
        global $DB;

        // Figure out some dates in unix_timestamps.
        $wdate = isset($enrollment->Withdraw_Date)
                    ? self::dateconv($enrollment->Withdraw_Date)
                    : 0;
        $regdate = isset($enrollment->Registered_Date)
                   ? self::dateconv($enrollment->Registered_Date)
                   : 0;
        $dropdate = isset($enrollment->Drop_Date)
                    ? self::dateconv($enrollment->Drop_Date)
                    : $wdate;
        $lastupdate = isset($enrollment->Last_Functionally_Updated)
                      ? self::dateconv($enrollment->Last_Functionally_Updated)
                      : time();

        // Build the cloned object.
        $as2 = unserialize(serialize($as));

        if (!isset($enrollment->Grading_Basis)) {
            mtrace("*** Grading basis not set for course: $enrollment->Section_Listing_ID and student: $enrollment->Universal_Id.");
        }

        // Keep the id, section_listing_id, and $universal_id from $as and populate the rest from aenrollment.
        $as2->credit_hrs = $enrollment->Units;
        $as2->grading_scheme = isset($enrollment->Student_Grading_Scheme_ID)
                               ? $enrollment->Student_Grading_Scheme_ID
                               :  $s->campusname . ' Standard Grading Scheme';
        $as2->grading_basis = isset($enrollment->Grading_Basis) ? $enrollment->Grading_Basis : 'Graded';
        $as2->registration_status = $enrollment->Registration_Status;
        $as2->registered_date = $regdate;
        $as2->drop_date = $dropdate;
        $as2->lastupdate = $lastupdate;

        // Status gets complicated and is based on Registration Status.
        if (in_array($enrollment->Registration_Status, $unenrolls) && $as->status != 'unenrolled') {
            $as2->status = 'unenroll';

        } else if (in_array($enrollment->Registration_Status, $unenrolls) && $as->status == 'unenrolled') {
            $as2->status = 'unenrolled';

        } else if (in_array($enrollment->Registration_Status, $enrolls) && $as->status != 'enrolled') {
            $as2->status = 'enroll';

        } else if (in_array($enrollment->Registration_Status, $enrolls) && $as->status == 'enrolled') {
            $as2->status = 'enrolled';

        } else {
            $as2->status = mb_strtolower($enrollment->Registration_Status, 'UTF-8');
        }

        $as2->prevstatus = $as->status;

        // Compare the objects.
        if (get_object_vars($as) === get_object_vars($as2)) {
            return $as;
        } else {

            // Set the table.
            $table = 'enrol_wds_student_enroll';

            if ($as2->lastupdate >= $as->lastupdate) {

                // Update the record.
                $success = $DB->update_record($table, $as2, true);
            } else {
                self::dtrace("Enrollment record for $as->section_listing_id - $as->universal_id is older than the existing record, skipping.");
            }

            if (isset($success) && $success == true) {
                self::dtrace("Enrollment for $as2->universal_id in $as2->section_listing_id has been updated from the endpoint.");

                // Return the updated object.
                return $as2;
            } else if (isset($success) && $success == false) {
                self::dtrace("Enrollment for $as2->universal_id in $as2->section_listing_id failed updating status to: $as2->registration_status from the endpoint.");

                // Return the original object.
                return $as;
            } else {
                return $as;
            }
        }
    }

    public static function check_student_enrollment($enrollment) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_student_enroll';

        // Set the parameters.
        $parms = ['section_listing_id' => $enrollment->Section_Listing_ID, 'universal_id' => $enrollment->Universal_Id];

        // Get the enrollment record.
        $as = $DB->get_record($table, $parms);

        return $as;
    }

    public static function insert_student_enrollment($s, $enrollment, $unenrolls, $enrolls, $donothings) {
        global $DB;

        // Figure out some dates in unix_timestamps.
        $wdate = isset($enrollment->Withdraw_Date)
                    ? self::dateconv($enrollment->Withdraw_Date)
                    : 0;
        $regdate = isset($enrollment->Registered_Date)
                   ? self::dateconv($enrollment->Registered_Date)
                   : 0;
        $dropdate = isset($enrollment->Drop_Date)
                    ? self::dateconv($enrollment->Drop_Date)
                    : $wdate;
        $lastupdate = isset($enrollment->Last_Functionally_Updated)
                      ? self::dateconv($enrollment->Last_Functionally_Updated)
                      : time();

        // Set the table.
        $table = 'enrol_wds_student_enroll';

        // Create the object.
        $tas = new stdClass();

        // Build the object from $enrollment.
        $tas->section_listing_id = $enrollment->Section_Listing_ID;
        $tas->universal_id = $enrollment->Universal_Id;
        $tas->credit_hrs = $enrollment->Units;
        $tas->grading_scheme = isset($enrollment->Student_Grading_Scheme_ID)
                               ? $enrollment->Student_Grading_Scheme_ID
                               :  $s->campusname . ' Standard Grading Scheme';
        $tas->grading_basis = isset($enrollment->Grading_Basis) ? $enrollment->Grading_Basis : 'Graded';
        $tas->registration_status = $enrollment->Registration_Status;
        $tas->registered_date = $regdate;
        $tas->drop_date = $dropdate;
        $tas->lastupdate = $lastupdate;

        // Status gets complicated and is based on Registration Status.
        if (in_array($enrollment->Registration_Status, $unenrolls)) {
            $tas->status = 'unenrolled';
        } else if (in_array($enrollment->Registration_Status, $enrolls)) {
            $tas->status = 'enroll';
        } else {
            $tas->status = $enrollment->Registration_Status;
        }

        // This is the first status and should be null.
        $tas->prevstatus = null;

        // Insert the record.
        $as = $DB->insert_record($table, $tas);

        return $as;
    }

    public static function insert_update_course($s, $course) {

        // Check to see if we have a matching course.
        $ac = self::check_course($course);

        // We do! We do have a matching course.
        if (isset($ac->id)) {

            // Update it.
            $ac = self::update_course($course, $ac);
        } else {

            // Insert it.
            $ac = self::insert_course($course);
        }
        return $ac;
    }

    public static function check_unwant($section, $as) {
        global $DB;

        // Short curcuit this in case we have no PMI.
        if (!isset($section->PMI_Universal_ID)) {
            return false;
        }

        $parms = [
            'section' => $as->id,
            'pmi' => $section->PMI_Universal_ID
        ];

        // Build the SQL.
        $usql = "SELECT
            tea.universal_id,
            uw.unwanted
            FROM {block_wdsprefs_unwants} uw
            INNER JOIN {enrol_wds_teachers} tea
                ON tea.userid = uw.userid
            WHERE uw.sectionid = :section
                AND tea.universal_id = :pmi";

        // Get the record.
        $unwanted = $DB->get_record_sql($usql, $parms);

        return $unwanted;
    }

    public static function insert_update_section($section) {
        global $DB;

        // Check to see if we have a matching section.
        $as = self::check_section($section);

        // We do! We do have a matching section.
        if (isset($as->id)) {

        // Check if there's a PMI change.
        if (isset($section->PMI_Universal_ID)) {

            // Get all primary instructors for this section.
            $ppmisql = "SELECT tenr.universal_id, tenr.id AS enrollment_id
                FROM {enrol_wds_teacher_enroll} tenr
                WHERE tenr.section_listing_id = :sectionid
                AND tenr.role = 'primary'";

            $ppmiparms = ['sectionid' => $section->Section_Listing_ID];
            $primaryinstructors = $DB->get_records_sql($ppmisql, $ppmiparms);

            // Process for instructor change if we have any primaries.
            if (!empty($primaryinstructors)) {

                // Flag to track if the current PMI exists among previous primaries.
                $pmichanged = true;

                // Handle multiple primary instructors (fix previous errors).
                if (count($primaryinstructors) > 1) {
                    workdaystudent::dtrace("Found multiple primary instructors for section {$section->Section_Listing_ID}. Fixing...");

                    // Loop through all primaries and handle each one.
                    foreach ($primaryinstructors as $primaryinstructor) {

                        // Check if this is the current/correct PMI.
                        if ($primaryinstructor->universal_id === $section->PMI_Universal_ID) {
                            $pmichanged = false;
                            continue;
                        }

                        // This is an incorrect primary, create a temporary object for the handler.
                        $oldinstructor = new stdClass();
                        $oldinstructor->universal_id = $primaryinstructor->universal_id;
                        $oldinstructor->enrollment_id = $primaryinstructor->enrollment_id;

                        // Call the handler to unenroll this instructor.
                        workdaystudent::dtrace("Unenrolling extra primary instructor: {$oldinstructor->universal_id}");
                        workdaystudent::handle_instructor_change($section, $as, $oldinstructor);
                    }
                }

                // Single primary instructor case - check if it's changed.
                else {
                    $primaryinstructor = reset($primaryinstructors);
                    if ($primaryinstructor->universal_id === $section->PMI_Universal_ID) {
                        $pmichanged = false;
                    }
                }

                // If the PMI has changed, handle the change.
                if ($pmichanged) {
                    workdaystudent::dtrace("Primary instructor changed for section {$section->Section_Listing_ID}");
                    workdaystudent::handle_instructor_change($section, $as);
                }
            } else {

                // No existing primary instructors found, nothing to change.
                workdaystudent::dtrace("No existing primary instructors found for section {$section->Section_Listing_ID}");
            }
        }

            // Check if this section / teacher combo is unwanted.
            $unwanted = self::check_unwant($section, $as);

            // We do not have a record of this section being unwanted.
            if (!isset($unwanted->universal_id)) {

                // Existing not-specifically-unwanted section, update it.
                $as = self::update_section($section, $as);

            // This section is specifically wanted by the Primary instructor.
            } else if ($section->PMI_Universal_ID == $unwanted->universal_id &&
                $unwanted->unwanted == 0) {

                // Existing specifically-wanted section, update it.
                $as = self::update_section($section, $as);

            // The section is unwanted, do not updated it, just return it so we can move on.
            } else {
                mtrace("\n$as->section_listing_id is unwanted by $unwanted->universal_id.");

                return $as;
            }

        // We have no record of this section, add it.
        } else {

            // Insert it.
            $as = self::insert_section($section);
        }

        return $as;
    }

    public static function process_section_schedule(object $section,
        string $schedule,
        string $timezone = 'America/Chicago'): array {

        // Split the string into days and time.
        [$dayspart, $timepart] = explode('|', $schedule);

        // Trim the whitespace.
        $dayspart = trim($dayspart);
        $timepart = trim($timepart);

        // Split the days into an array.
        $days = explode(' ', $dayspart);

        // Check if we have both start and end time in the time part.
        $times = explode('-', $timepart);

        // Trim the times.
        $times = array_map('trim', $times);

        // Ensure we have exactly 2 times (start and end).
        if (count($times) < 2) {

            // If we don't have both start and end times, log the issue and exit.
            self::dtrace("Invalid time format: $timepart");
            var_dump($times);
            return [];
        }

        // Split the time range into start and end times.
        [$starttime, $endtime] = $times;

        // Map full day names to short names as per my whimsy.
        $dayshortnames = [
            'Monday' => 'M', 'Tuesday' => 'Tu', 'Wednesday' => 'W', 'Thursday' => 'Th',
            'Friday' => 'F', 'Saturday' => 'Sa', 'Sunday' => 'Su'
        ];

        // Utilize an anonymous function to map the day to its shortened counterpart.
        $shortdays = array_map(fn($day) => $dayshortnames[$day] ?? $day, $days);

        // Set the timezone.
        $tz = new DateTimeZone($timezone);

        // Build an array to hold this stuff.
        $scheduleitems = [];

        // Loop through the days and times, ensuring each day has a corresponding time.
        foreach ($days as $index => $day) {

            // If there are fewer times than days, we should repeat the times or handle the mismatch.
            if (empty($starttime) || empty($endtime)) {

                // Skip days with invalid time or log an error.
                mtrace("Error! Invalid time for day: $day");
                continue;
            }

            // Convert to DateTime objects with the timezone.
            $startdatetime = DateTime::createFromFormat('g:i A', $starttime, $tz);
            $enddatetime = DateTime::createFromFormat('g:i A', $endtime, $tz);

            // Check if the DateTime creation was successful.
            if ($startdatetime === false || $enddatetime === false) {

                // Handle the error (skip and log it).
                mtrace("Error! Failed to parse time: Start time - $starttime, End time - $endtime");
                continue;
            }

            // Create an object for each day with start or end times.
            $scheduleitems[] = (object)[
                'section_listing_id' => $section->Section_Listing_ID,
                'day' => $day,
                'short_day' => $shortdays[$index] ?? null,
                'start_time' => $startdatetime->setTimezone($tz)->format('g:i A T'),
                'end_time' => $enddatetime->setTimezone($tz)->format('g:i A T')
            ];
        }

        // Return the array of schedule items.
        return $scheduleitems;
    }

    /**
     * Store the schedule (add, update, or delete records) based on the provided data.
     *
     * @param array $schedule An array of stdClass objects containing the schedule data.
     * @return void
     */
    public static function wds_store_schedules($section, $schedules) {
        global $DB;

        // Check if the schedule is valid.
        if (empty($schedules)) {
            return;
        }

        // Set the table.
        $table = 'enrol_wds_section_meta';

        // Build out the query parms.
        $parms = ['section_listing_id' => $section->Section_Listing_ID];

        // Retrieve existing records for the given section_listing_id.
        $existingrecords = $DB->get_records($table, $parms);

        // Build an array for future use.
        $existingmap = [];

        // Convert to an associative array by day for easy access.
        foreach ($existingrecords as $record) {
            $existingmap[$record->day] = $record;
        }

        // Process each schedule entry.
        foreach ($schedules as $scheduleitem) {

            // Validate the required fields.
            if (empty($scheduleitem->section_listing_id) || empty($scheduleitem->day)) {
                mtrace("Schedule is borked for $section->section_listing_id.");
                continue;
            }

            // Check if this day already exists in the database.
            if (isset($existingmap[$scheduleitem->day])) {

                // The day already exists, check if the times are different.
                $existingrecord = $existingmap[$scheduleitem->day];

                // The record exists but it does not match the stored value.
                if ($existingrecord->start_time !== $scheduleitem->start_time ||
                    $existingrecord->end_time !== $scheduleitem->end_time) {

                    // Times differ, set them accordingly and update the record.
                    $existingrecord->start_time = $scheduleitem->start_time;
                    $existingrecord->end_time = $scheduleitem->end_time;
                    $DB->update_record($table, $existingrecord);
                }

                // Remove handled items from the existingmap.
                unset($existingmap[$scheduleitem->day]);
            } else {

                // No record for this day. Insert one.
                $DB->insert_record($table, $scheduleitem);

                // Remove handled items from the existingmap.
                unset($existingmap[$scheduleitem->day]);
            }
        }

        if (!empty($existingmap)) {

            // After processing the schedules, remove any days not in the new schedules.
            foreach ($existingmap as $day => $recordtoremove) {
                $DB->delete_records($table, ['id' => $recordtoremove->id]);
                mtrace("Removed $recordtoremove->day section schedule from " .
                    "$recordtoremove->section_listing_id.");
            }

        }
    }

    public static function grab_section_schedule($universalid = null) {
        global $DB;

        // We're looking for a single student's schedule for the current terms.
        if (!is_null($universalid)) {
            $user = "INNER JOIN {enrol_wds_student_enroll} ste
                         ON sec.section_listing_id = ste.section_listing_id
                     WHERE ste.universal_id = '$universalid'
                         AND ste.registration_status = 'Registered'
                         AND ap.start_date < UNIX_TIMESTAMP()
                         AND ap.end_date > UNIX_TIMESTAMP()";

        // We're looking for all course schedules.
        } else {
            $user = "WHERE ap.start_date < UNIX_TIMESTAMP()
                         AND ap.end_date > UNIX_TIMESTAMP()";
        }

        // Build out the sql to grab the data for everyone.
        $sql = 'SELECT
            CONCAT(
                ap.period_year,
                " ",
                ap.period_type,
                " ",
                cou.course_subject_abbreviation,
                " ",
                cou.course_number,
                " - ",
                sec.section_number
            ) AS course,
            GROUP_CONCAT(
                sm.short_day ORDER BY FIELD(
                    sm.short_day,
                    \'M\',
                    \'Tu\',
                    \'W\',
                    \'Th\',
                    \'F\'
                ) SEPARATOR \', \'
            ) AS day,
            sm.start_time,
            sm.end_time
            FROM {enrol_wds_courses} cou
                INNER JOIN {enrol_wds_sections} sec
                    ON cou.course_listing_id = sec.course_listing_id
                INNER JOIN {enrol_wds_section_meta} sm
                    ON sm.section_listing_id = sec.section_listing_id
                INNER JOIN {enrol_wds_periods} ap
                    ON ap.academic_period_id = sec.academic_period_id
                ' . $user . '
            GROUP BY sec.section_listing_id
            ORDER BY sm.id';

        // Grab the data.
        $schedule = $DB->get_records_sql($sql);

        return $schedule;
    }

    public static function check_shell($shell) {
        global $DB;
    }

    public static function create_update_shell($shell) {

        // Check to see if we have a matching course shell.
        $cs = self::check_shell($shell);

        // We do! We do have a matching course shell.
        if (isset($cs->id)) {

            // Update it.
            $cs = self::update_shell($shell, $cs);
        } else {

            // Insert it.
            $cs = self::create_shell($shell);
        }
        return $cs;
    }

    public static function insert_update_period($s, $period) {
        $ap = self::check_period($period);
        if (isset($ap->id)) {
            $ap = self::update_period($period, $ap);
        } else {
            $ap = self::insert_period($period);
        }
        return $ap;
    }

    public static function insert_update_period_date($s, $pdate) {
        $pd = self::check_period_date($pdate);

        if (isset($pd->id)) {
            $pd = self::update_period_date($pdate, $pd);
        } else {
            $pd = self::insert_period_date($pdate);
        }
        return $pd;
    }

    public static function update_unit($unit, $au) {
        global $DB;

        // Build the cloned object.
        $au2 = unserialize(serialize($au));

        // Keep the ids from $au and populate the rest from $unit.
        $au2->academic_unit_subtype = $unit->Academic_Unit_Subtype;
        $au2->academic_unit_code = $unit->Academic_Unit_Code;
        $au2->academic_unit = $unit->Academic_Unit;
        $au2->superior_unit_id = $unit->Superior_ID;

        // Compare the objects.
        if (get_object_vars($au) === get_object_vars($au2)) {
            self::dtrace(" - Academic unit $au->academic_unit_id matched $unit->Academic_Unit_ID, skipping.");
            return $au;
        } else {

            // Set the table.
            $table = 'enrol_wds_units';

            // Update the record.
            $success = $DB->update_record($table, $au2, false);

            if ($success) {
                self::dtrace(" - Academic unit $au->academic_unit_id has been updated from the endpoint.");

                // Return the updated object.
                return $au2;
            } else {
                mtrace(" - Error! Updating $au->academic_unit_id failed and has not been updated.");

                // Return the original object.
                return $au;
            }
        }
    }

    public static function cleanxml($xmlstring) {

        // Use a regex to remove `{+1}` entirely.
        $xmlstring = preg_replace('/\{[^}]*\}/', '', $xmlstring);

        // Ensure that the XML is well-formed using DOMDocument.
        $dom = new DOMDocument('1.0', 'UTF-8');

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

    public static function buildgradestopost($grades, $gradetype) {
        $today = date('Y-m-d');

        $studentgrades = '';
        foreach ($grades as $grade) {

            // Student Registration Data.
            $sectionlistingid = $grade->section_listing_id;
            $universalid = $grade->universal_id;

            // Grade for the registration in question.
            $gradeid = $grade->grade_id;

            // Check to see if we're in finals or this is an interim grade.
            if ($gradetype == "finals") {

                // Posting final grades.
                $sdtype = "Student_Grades_Data";

                // If we have a last date of attendance set, send it.
                if (isset($grade->requires_last_attendance)) {
                    $ld = date('Y-m-d', $grade->last_attendance_date);
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
            $returnerror = new stdClass();

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

    public static function buildsoapxml($s, $grades, $gradetype, $sectionlistingid) {

        // Build out if it's interim or final grades.
        if ($gradetype == "finals") {
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
        $username = $s->username . "@lsu14";
        $password = $s->password;
        $version = "v" . $s->apiversion;

        // Build out the student grades portion of the xml.
        $gradesxml = self::buildgradestopost($grades, $gradetype);

        // Create SOAP Envelope.
        $xml = new SimpleXMLElement('<env:Envelope
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

    public static function post_grade($s, $grades, $gradetype, $sectionlistingid) {

        // Build out the xml.
        $xml = self::buildsoapxml($s, $grades, $gradetype, $sectionlistingid);

        // Workday API credentials.
        $username = $s->username . "@lsu14";
        $password = $s->password;

        $version = "v" . $s->apiversion;

        // Workday API endpoint for the Submit_Grades_for_Registrations SOAP operation.
        $workdayurl = "https://wd2-impl-services1.workday.com/ccx/service/lsu14/Student_Records/$version";

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

        // Check if the cURL request was successful.
        if(curl_errno($ch)) {
            $curlerror = curl_error($ch);

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
                $xmlobj = new stdClass();

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

    public static function insert_unit($unit) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_units';

        // Create the object.
        $tau = new stdClass();

        // Build the object from $unit.
        $tau->academic_unit_id = $unit->Academic_Unit_ID;
        $tau->academic_unit_subtype = $unit->Academic_Unit_Subtype;
        $tau->academic_unit_code = $unit->Academic_Unit_Code;
        $tau->academic_unit = $unit->Academic_Unit;
        $tau->superior_unit_id = isset($unit->Superior_ID) ? $unit->Superior_ID : '';

        $au = $DB->insert_record($table, $tau);
        self::dtrace("Inserted academic_unit_id: $tau->academic_unit_id.");

        return $au;
    }

    public static function insert_update_unit($s, $unit) {
        $au = self::check_unit($unit);
        if (isset($au->id)) {
            $au = self::update_unit($unit, $au);
        } else {
            $au = self::insert_unit($unit);
        }
        return $au;
    }

    public static function get_current_departments($s) {
        global $DB;

        $sql = 'SELECT CONCAT(p.id, "_",  c.id, "_", s.id) AS uniqueid,
                c.course_subject_abbreviation,
                p.academic_period_id
            FROM {enrol_wds_periods} p
                INNER JOIN {enrol_wds_sections} s ON p.academic_period_id = s.academic_period_id
                INNER JOIN {enrol_wds_courses} c ON s.course_listing_id = c.course_listing_id
            WHERE p.start_date < UNIX_TIMESTAMP(NOW())
                AND p.end_date > UNIX_TIMESTAMP(NOW())
                AND p.enabled = 1
                GROUP BY c.course_subject_abbreviation, p.academic_period_id';

        // Get the data using the sql above.
        $departments = $DB->get_records_sql($sql);

        return $departments;
    }

    public static function get_specified_period(string $courseid): array {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_sections';

        // Check if the $courseid consists of only digits.
        if (ctype_digit((string) $courseid)) {

            // It's a plain integer or numeric string.
            $parms = ['moodle_status' => $courseid];
        } else {

            // It's a structured course section definition.
            $parms = ['course_section_definition_id' => $courseid];
        }

        // Get the actual data.
        $periods = $DB->get_records($table, $parms);

        $period = [reset($periods)];

        return $period;
    }

    public static function get_current_periods($s) {
        global $DB;

        $allcurrentperiods = isset($s->allperiods) && $s->allperiods ? '' : 'AND p.enabled = 1';

        // Set the semester range for getting future and recent semesters.
        $fsemrange = isset($s->brange) ? ($s->brange * 86400) : 0;
        $psemrange = isset($s->erange) ? ($s->erange * 86400) : 0;

        // Build the SQL.
        $sql = "SELECT p.academic_period_id
                  FROM {enrol_wds_periods} p
                WHERE p.start_date < UNIX_TIMESTAMP() + $fsemrange
                  AND p.end_date > UNIX_TIMESTAMP() - $psemrange
                  $allcurrentperiods
                  ORDER BY p.start_date ASC, p.period_type ASC";

        // Get the actual data.
        $periods = $DB->get_records_sql($sql);

        return $periods;
    }

    public static function get_current_sections($s) {
        global $DB;

        $sql = 'SELECT s.*
            FROM {enrol_wds_periods} p
                INNER JOIN {enrol_wds_sections} s ON p.academic_period_id = s.academic_period_id
            WHERE p.start_date < UNIX_TIMESTAMP(NOW())
                AND p.end_date > UNIX_TIMESTAMP(NOW())
                AND p.enabled = 1';

        $sections = $DB->get_records_sql($sql);
        return $sections;
    }

    public static function get_period_enrollments($s, $period, $fdate = null) {

        // Set the endpoint.
        $endpoint = 'registrations';

        // Set up the paramaters array.
        $parms = [];

        if (isset($period->courseid)) {

            // Check if the $period->courseid consists of only digits.
            if (!ctype_digit((string) $period->courseid)) {

                // It's a structured course section definition.
                $parms['Course_Section_Definition_ID'] = $period->courseid;
                $parms['Academic_Period!Academic_Period_ID'] = $period->academic_period_id;
            }
        }

        // Set some more parms up.
        if (!is_null($fdate)) {
            $parms['Last_Updated'] = $fdate;
        }

        if (isset($period->course_subject_abbreviation)) {
            $parms['Subject_Code'] = $period->course_subject_abbreviation;
            $parms['Academic_Period!Academic_Period_ID'] = $period->academic_period_id;
        } else {
            $parms['Academic_Period!Academic_Period_ID'] = $period->academic_period_id;
        }

        $parms['format'] = 'json';

        // Build out the settins based on settings, endpoint, and parms.
        $sep = self::buildout_settings($s, $endpoint, $parms);

        // Get the sections.
        $enrollments = self::get_data($sep);

        return $enrollments;
    }

    public static function get_programs($s) {

        // Set the endpoint.
        $endpoint = 'programs';

        // Set some more parms up.

        // TODO: Add me back! $parms['Institution!Academic_Unit_ID'] = $s->campus;
        $parms['format'] = 'json';

        // Build out the settins based on settings, endpoint, and parms.
        $s = self::buildout_settings($s, $endpoint, $parms);

        // Get the sections.
        $programs = self::get_data($s);

        return $programs;
    }

    public static function clear_insert_programs($programs) {
        global $DB;

        // Build some sql to truncate the table.
        $sql = 'TRUNCATE {enrol_wds_programs}';
        self::dtrace("  Truncating enrol_wds_programs.");

        // Actually do it and store if we're successful or not.
        $success = $DB->execute($sql);

        // Build the $pgms array for future use.
        $pgms = [];

        // If we successfully truncated, insert data.
        if ($success) {
            self::dtrace("  Successfully truncated enrol_wds_programs.");

            // Get the program data.
            foreach ($programs as $program) {
                $pgms[] = self::insert_program($program);
            }

        } else {
            mtrace("  Error! Failed to truncate enrol_wds_programs.");
            return $success;
        }

        return $pgms;
    }

    public static function insert_update_programs($programs) {

        // Build the $pgms array for future use.
        $pgms = [];

        // Get the program data.
        foreach ($programs as $program) {

            // Check to see if a program already exists.
            $pgm = self::fetch_program($program);

            // If we have an existing program, update it, if not create one.
            if (isset($pgm->id)) {
                $pgms[] = self::update_program($program, $pgm);
            } else {
                $pgms[] = self::insert_program($program);
            }
        }

        return $pgms;
    }

    public static function update_program($program, $pgm) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_programs';

        // Build the two objects to compare.
        $pgm1 = unserialize(serialize($pgm));

        // Populate the cloned object.
        $pgm1->academic_unit_id = $program->Academic_Unit_ID;
        $pgm1->program_of_study_code = $program->Program_of_Study_Code;
        $pgm1->program_of_study = $program->Program_of_Study;

        // If the objects match.
        if (get_object_vars($pgm) === get_object_vars($pgm1)) {
            self::dtrace("  - Program object match, no update necessary.");

            // Return the original program.
            return $pgm;
        } else {
            self::dtrace("  - $pgm1->program_of_study_code mismatch, update necessary.");

            // Update the record.
            $success = $DB->update_record($table, $pgm1, false);

            // TODO: RETURN ERRORS.

            // Return the new record.
            return $pgm1;
        }
    }

    public static function fetch_program($program) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_programs';

        // Set the parameters.
        $parms = ['academic_unit_id' => $program->Academic_Unit_ID,
                       'program_of_study_code' => $program->Program_of_Study_Code];

        // Get the program record.
        $pgm = $DB->get_record($table, $parms);

        return $pgm;
    }

    public static function insert_program($program) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_programs';

        // Set the singular data object.
        $dataobj = [
            'academic_unit_id' => $program->Academic_Unit_ID,
            'program_of_study_code' => $program->Program_of_Study_Code,
            'program_of_study' => $program->Program_of_Study
        ];

        // Insert the data.
        $gsid = $DB->insert_record($table, $dataobj, false);

        // We may not need to fetch/send this. Revisit.
        $gs = $DB->get_record($table, ['id' => $gsid]);

        // TODO: RETURN ERRORS.

        return $gs;
    }

    public static function get_grading_schemes($s) {

        // Set the endpoint.
        $endpoint = 'grading_schemes';

        // Set some more parms up.
        $parms['Institution!Academic_Unit_ID'] = $s->campus;
        $parms['format'] = 'json';

        // Build out the settins based on settings, endpoint, and parms.
        $s = self::buildout_settings($s, $endpoint, $parms);

        // Get the sections.
        $gradingschemes = self::get_data($s);

        return $gradingschemes;
    }

    public static function clear_insert_grading_schemes($gradingschemes) {
        global $DB;

        $s = self::get_settings();

        // Build some sql to truncate the table.
        $sql = 'TRUNCATE {enrol_wds_grade_schemes}';
        self::dtrace("  Truncating enrol_wds_grade_schemes.");

        // Actually do it and store if we're successful or not.
        $success = $DB->execute($sql);

        // Build the $gs array for future use.
        $gs = [];

        // Set the counter.
        $counter = 0;

        // If we successfully truncated, insert data.
        if ($success) {
            self::dtrace("  Successfully truncated enrol_wds_grade_schemes.");

            // Get the grading schemas.
            foreach ($gradingschemes as $gradingschema) {
                self::dtrace("    Processing $gradingschema->Student_Grading_Scheme_ID.");

                // Get the grading schemes from each Grades_group.
                foreach ($gradingschema->Grades_group as $gradingscheme) {

                    // If we have a 2 digit display grade, please use it.
                    if (isset($gradingscheme->Student_Grade_Display_2dig)) {

                          // Set Student_Grade_Display to the max 2 digit value as provided by WorkDay.
                          $gradingscheme->Student_Grade_Display = $gradingscheme->Student_Grade_Display_2dig;
                    }

                    $gsid = isset($gradingschema->Student_Grading_Scheme_ID)
                        ? $gradingschema->Student_Grading_Scheme_ID
                        : $s->campusname . ' Standard Grading Scheme';

                    // Add the grading scheme id into the child array.
                    $gradingscheme->Student_Grading_Scheme_ID = $gsid;

                    // Increment the counter.
                    $counter++;
                    self::dtrace("      -($counter) Processing $gradingscheme->Grading_Basis - $gradingscheme->Student_Grade_Display.");

                    // Insert each grading scheme and add it to the $gs array.
                    $gs = array_merge($gs, self::insert_grading_scheme($gradingscheme));
                }
            }

        } else {
            mtrace("  Error! Failed to truncate enrol_wds_grade_schemes.");
            return $success;
        }

        return $gs;
    }

    public static function insert_grading_scheme($gradingscheme) {
        global $DB;

        $s = self::get_settings();
        $gsids = isset($gradingscheme->Student_Grading_Scheme_ID)
            ? $gradingscheme->Student_Grading_Scheme_ID
            : $s->campusname . ' Standard Grading Scheme';

        // Set the table.
        $table = 'enrol_wds_grade_schemes';

        // Set the singular data object.
        $dataobj = [
            'grading_scheme_id' => $gsids,
            'grade_id' => $gradingscheme->Student_Grade_ID,
            'grade_display' => $gradingscheme->Student_Grade_Display,
            'requires_last_attendance' => $gradingscheme->Requires_Last_Attendance,
            'grade_note_required' => $gradingscheme->Grade_Note_Required
        ];

        $gs = [];
        $gsa = [];

        // We do not have multiple grading basis, insert the singular item.
        if (!strpos($gradingscheme->Grading_Basis, ';')) {
            $dataobj['grading_basis'] = $gradingscheme->Grading_Basis;

            // Insert the data.
            $gsid = $DB->insert_record($table, $dataobj, true);

            // We may not need to fetch/send this. Revisit.
            $gs[] = $DB->get_record($table, ['id' => $gsid]);

        // We have multiple grading basis', go nuts.
        } else {

            // Get the multiple grading basis' from the ; separated list.
            $mgb = array_map('trim', explode(';', $gradingscheme->Grading_Basis));

            // Loop through our grading basis'.
            foreach ($mgb as $gb) {

                // Set the data object grading basis accordingly.
                $dataobj['grading_basis'] = $gb;

                // Insert the data.
                $gsid = $DB->insert_record($table, $dataobj, true);

                // We may not need to fetch/send this. Revisit.
                $gsa[] = $DB->get_record($table, ['id' => $gsid]);
                $gs = array_merge($gs, $gsa);
            }
        }

        return $gs;
    }

    public static function get_potential_new_basic_shells($period) {
        global $DB;

        $params = [
            'periodid' => $period->academic_period_id
        ];

        $sql = 'SELECT sec.id,
                    p.period_year,
                    p.period_type,
                    cou.course_subject_abbreviation,
                    cou.course_number,
                    sec.section_number,
                    te.universal_id,
                    COALESCE(t.preferred_firstname, t.firstname) AS firstname,
                    COALESCE(t.preferred_lastname, t.lastname) AS lastname
                FROM {enrol_wds_sections} sec
                    INNER JOIN {enrol_wds_teacher_enroll} te ON
                        sec.section_listing_id = te.section_listing_id
                    INNER JOIN {enrol_wds_courses} cou ON
                        cou.course_listing_id = sec.course_listing_id
                    INNER JOIN {enrol_wds_periods} p ON
                        p.academic_period_id = sec.academic_period_id
                   INNER JOIN {enrol_wds_teachers} t ON
                        t.universal_id = te.universal_id
                WHERE sec.idnumber IS NULL
                    AND sec.academic_period_id = :periodid
                GROUP BY cou.course_listing_id, te.universal_id
                ORDER BY te.universal_id ASC,
                    cou.course_subject_abbreviation ASC,
                    cou.course_number ASC,
                    sec.section_number ASC';

        // Run the SQL for this period.
        $pns = $DB->get_records_sql($sql, $params);

        // Return the data.
        return $pns;
    }

    public static function get_sections($s, $parms) {

        // Set the endpoint.
        $endpoint = 'sections';

        // Set some more parms up.
        if (isset($s->campus)) {
            $parms['Institution!Academic_Unit_ID'] = $s->campus;
        }
        $parms['format'] = 'json';

        // Build out the settins based on settings, endpoint, and parms.
        $s = self::buildout_settings($s, $endpoint, $parms);

        // Get the sections.
        $sections = self::get_data($s);

        return $sections;
    }

    public static function get_wd_courses($s) {

    // Set the endpoint.
    $endpoint = 'courses';

        // Set some aprms up.
        $parms = [];
        if (isset($s->campus)) {
            $parms['Institution!Academic_Unit_ID'] = $s->campus;
        }
        $parms['format'] = 'json';

        // Build out the settins based on settings, endpoint, and parms.
        $s = self::buildout_settings($s, $endpoint, $parms);

        // Get the units.
        $courses = self::get_data($s);

        return $courses;
    }

    public static function sort_courses($courses) {
        usort($courses, function($a, $b) {

            // First comparison based on subject.
            $coursecomparison = strcmp($a->Course_Subject_Abbreviation, $b->Course_Subject_Abbreviation);

            // If 'name' values are equal, compare based on 'count'.
            if ($coursecomparison == 0) {
                return strcmp($a->Course_Number, $b->Course_Number);
            }

            return $coursecomparison;
        });
        return $courses;
    }

    public static function format_date($longago) {
        $date = date("Y-m-d\TH:i:s", strtotime("$longago"));
        return $date;
    }

    public static function format_pg_date($pgdate) {

        // Parse the date string as a DateTime object.
        $datewithprovidedoffset = new DateTime($pgdate);

        // Specify the expected (local) timezone.
        $expectedtimezone = new DateTimeZone(date_default_timezone_get());

        // Get the offset from the provided date in hours.
        $providedoffset = $datewithprovidedoffset->getOffset() / 3600;

        // Create a DateTime object for the same date in the expected timezone.
        $datewithexpectedtimezone = new DateTime($pgdate);
        $datewithexpectedtimezone->setTimezone($expectedtimezone);

        // Get the offset from the expected date in hours.
        $expectedoffset = $expectedtimezone->getOffset($datewithexpectedtimezone) / 3600;

        // Check if the offsets match.
        if ($providedoffset == $expectedoffset) {

            // Yay! The timzone in WD matches the expected value.
        } else {

            // Find the difference between expected and provided timzones in hours.
            $hourdiff = $expectedoffset - $providedoffset;

            // Update the provided date to migrate from the provided to observed timezone.
            $datewithprovidedoffset->modify(-$hourdiff . ' hours');

            // Reset the DateTime object to use the expected timezone.
            $datewithprovidedoffset->setTimezone($expectedtimezone);
        }

        $formatteddate = $datewithprovidedoffset->format("Y-m-d\TH:i:sP");

//        $utcintdate = $datewithprovidedoffset->getTimestamp();
        $pgdate = $formatteddate;

        // Output the final adjusted date in the correct timezone.
        return $pgdate;
    }

    public static function get_local_units($s) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_units';

        // Set up the conditions.
        $conditions = ['academic_unit_subtype'=>'Institution'];

        // Fetch the units.
        $units = $DB->get_records($table, $conditions, $sort = '', $fields = '*');

        return $units;
    }

    public static function get_units($s, $date = null) {

        // Set the endpoint.
        $endpoint = 'units';

        // Set some aprms up.
        $parms = [];

        // Check the campus.
        if (isset($s->campus)) {
            $parms['Superior_Unit!Academic_Unit_ID'] = $s->campus;
            $parms['Institution_ID!Academic_Unit_ID'] = $s->campus;
        }

        // Check the date.
        if (isset($date)) {
            $parms['Last_Updated'] = $date;
        }

        // Make sure we have the parm for our format.
        $parms['format'] = 'json';

        // Build out the settins based on settings, endpoint, and parms.
        $s = self::buildout_settings($s, $endpoint, $parms);

        // Get the units.
        $units = self::get_data($s);

        return $units;
    }

    public static function buildout_settings($s, $endpoint, $parms) {

        // Build the urlencoded params.
        $params = http_build_query($parms, '', '%26');

        // Build the entire urlencoded URL.
        $s->url = urlencode($s->wsurl . '/' . $s->$endpoint . '?') . $params;

        return $s;
    }

    public static function get_dates() {

        // Get the current year.
        $currentyear = date('Y');

        // Get the integer value of the current month.
        $currentmonth = intval(date('m'));

        // Set the start date to the 1st of the year.
        $startdate = $currentyear . '-01-01';

        // Set the end date to the last of either this or next year, depending on month.
        if ($currentmonth < 9) {
            $enddate = ($currentyear) . '-12-31';
        } else {
            $enddate = ($currentyear + 1) . '-12-31';
        }

        // Build the return array.
        $dates = [
            'Start_Date' => $startdate,
            'End_Date' => $enddate
        ];

        return $dates;
    }

    public static function delete_studentmeta($stu) {
        global $DB;
        $starttime = microtime(true);

        // Set the deleted table.
        $dtable = 'enrol_wds_students_meta';

        // Set the deleted parms.
        $dparms = ['studentid' => $stu->id];

        // Delete the records for this student.
        $deleted = $DB->delete_records($dtable, $dparms);

        $endtime = microtime(true);
        $elapsedtime = round($endtime - $starttime, 4);
        self::dtrace("  - Cleaning metadata for $stu->universal_id took $elapsedtime seconds.");

        // Return the bool.
        return $deleted;
    }

    public static function insert_studentmeta($s, $stu, $metafield, $metadata, $period) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_students_meta';

        // Build the $data object.
        $data = new stdClass();

        // Fill out the object.
        $data->studentid = $stu->id;
        $data->datatype = $metafield;
        $data->academic_period_id = $period->academic_period_id;
        $data->data = $metadata;

        // Insert the record.
        $inserted = $DB->insert_record($table, $data, false);

        return $inserted;
    }

    public static function insert_all_studentmeta($s, $stu, $student, $period) {

        // Determine what data we're talking about.
        $metafields = explode(',', $s->metafields);
        $sportfield = $s->sportfield;
        $athletecounter = 0;

        self::dtrace("Beginning to process student metadata for $stu->universal_id.");
        foreach ($metafields as $metafield) {
            $metafield = trim($metafield);
            if (isset($student->$metafield)) {
                $metadata = $student->$metafield;
                $updated = self::insert_studentmeta($s, $stu, $metafield, $metadata, $period);
                if ($updated) {
                    self::dtrace("    $student->Universal_Id - $metafield: $metadata updated.");
                } else {
                    self::dtrace("    ERROR: $student->Universal_Id - $metafield: $metadata failed to populate.");
                }
            }
        }

        foreach ($metafields as $metafield) {
            if (isset($student->$sportfield)) {
                $athletecounter++;
                foreach ($student->$sportfield as $team) {

                    // Update and insert sports and codes as needed.
                    $sport = self::create_update_sportcodes($s, $team);
                    $sports[] = $sport->code;
                    $supdated = self::insert_studentmeta($s, $stu, 'Athletic_Team_ID', $sport->code, $period);
                    if ($supdated) {
                        self::dtrace("    $student->Universal_Id - $student->First_Name $student->Last_Name is on team $sport->code: $sport->name.");
                    } else {
                        self::dtrace("    ERROR: $student->Universal_Id - $student->First_Name $student->Last_Name - $sport->code: $sport->name failed to populate.");
                    }
                }
                break;
            }
        }

        self::dtrace("Finished processing of student metadata for $stu->universal_id.");
        return $athletecounter;
    }

    public static function truncate_studentmeta() {
        global $DB;
        $sql = "TRUNCATE {enrol_wds_students_meta}";
        $truncated = $DB->execute($sql);
        return $truncated;
    }

    public static function delete_insert_studentmeta($s, $stu, $student) {

        // Determine what data we're talking about.
        $metafields = explode(',', $s->metafields);
        $sportfield = $s->sportfield;
        $athletecounter = 0;
        $deleted = self::delete_studentmeta($stu);

        if ($deleted) {
            self::dtrace("  - Beginning processing of student metadata for $stu->universal_id.");
            foreach ($metafields as $metafield) {
                $metafield = trim($metafield);
                if (isset($student->$metafield)) {
                    $metadata = $student->$metafield;
                    self::dtrace("    $student->Universal_Id - $metafield: $metadata");
                    $updated = self::insert_studentmeta($s, $stu, $metafield, $metadata);
                }
            }

            foreach ($metafields as $metafield) {
                if (isset($student->$sportfield)) {
                    $athletecounter++;
                    foreach ($student->$sportfield as $team) {

                        // Update and insert sports and codes as needed.
                        $sport = self::create_update_sportcodes($s, $team);

                        $sports[] = $sport->code;
                        self::dtrace("    $student->Universal_Id - $student->First_Name $student->Last_Name is on team $sport->code: $sport->name.");
                        $supdated= self::insert_studentmeta($s, $stu, 'Athletic_Team_ID', $sport->code);
                    }
                    break;
                }
            }
            self::dtrace("Finished processing of student metadata for $stu->universal_id.");
        } else {
            mtrace("Error! Could not update student metadata for $student->Universal_ID.");
        }
    }

    public static function check_sportcodes($team) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_sport';

        // Set the parms.
        $parms = ['code' => $team->Athletic_Team_ID];

        // Get tthe data.
        $sport = $DB->get_record($table, $parms);

        return $sport;
    }

    public static function create_sportcode($s, $team) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_sport';

        // Build the new obj.
        $sport = new stdClass();

        // Populate the obj.
        $sport->code = $team->Athletic_Team_ID;
        $sport->name = $team->Athletic_Team;

        // Insert the data.
        $created = $DB->insert_record($table, $sport, $returnid = true);

        if ($created) {
            self::dtrace("  - Inserted sport code: $sport->code with name: $sport->name at id: $created.");
            $sport->id = $created;
            return $sport;
        } else {
            self::dtrace("  - Failed to insert sport code: $sport->code with name: $sport->name.");
            return false;
        }
    }

    public static function update_sportcode($s, $sport, $team) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_sport';

        // Make sure we're only updating mismatches.
        if ($sport->code == $team->Athletic_Team_ID
            && $sport->name == $team->Athletic_Team) {

            // Log we did not update anything.
            self::dtrace("  - Sport code matches team name, skipping.");

            return $sport;
        } else {

            // Set the new sport name.
            $sport->name = $team->Athletic_Team;

            // Update the table.
            $updated = $DB->update_record($table, $sport, false);

            if ($updated) {
                self::dtrace("  - Updated $sport->code name to $sport->name.");
            } else {
                self::dtrace("  - Failed to update $sport->code name to $sport->name.");
            }

            return $sport;
        }
    }

    public static function create_update_sportcodes($s, $team) {

        // Check if the sport code exists.
        $sport = self::check_sportcodes($team);

        if (isset($sport->id)) {

            // Update the sport.
            $updated = self::update_sportcode($s, $sport, $team);
            return $updated;
        } else {

            // Create the student.
            $created = self::create_sportcode($s, $team);
            return $created;
        }
    }

    /**
     * Gets the data from the webservice endpoint.
     *
     * @param  @object $s
     *
     * @return @array of @objects
     */
    public static function get_data($s) {
        global $CFG;

        // Deal with some unfortunate stuff.
        $s->url = urldecode(urldecode($s->url));

        // Initiate the curl.
        $ch = curl_init($s->url);

        // Set the curl options.
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$s->username:$s->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1800);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        // Debug this connection.
        if ($CFG->debugdisplay == 1) {

// TODO: readd            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        // Get the data.
        $json_response = curl_exec($ch);

        // Get the http code for later.
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check if the cURL request was successful.
        if(curl_errno($ch)) {
            $curlerror = curl_error($ch);

            // Return the error.
            mtrace("cURL ERROR: $curlerror. Aborting.");
            return "error";

        // Check to see that we have a proper response.
        } else if ($httpcode != "200") {

            // Return the HTTP status code.
            mtrace("SERVER ERROR - HTTP Status Code: $httpcode. Aborting.");
            return "error";
        }

        // Close the curl.
        curl_close($ch);

        // Decode the json.
        $dataobj = json_decode($json_response);

        // Get the data we need.
        if (isset($dataobj->Report_Entry)) {
            $datas = $dataobj->Report_Entry;
        } else {
            $datas = null;
        }

        // Return the data.
        return $datas;
    }

    public static function get_data_raw($s) {

        // Deal with some unfortunate stuff.
        $s->url = urldecode(urldecode($s->url));

        // Initiate the curl.
        $ch = curl_init($s->url);

        // Set the curl options.
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$s->username:$s->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // Get the data.
        $json_response = curl_exec($ch);

        // Get the http code for later.
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check if the cURL request was successful.
        if(curl_errno($ch)) {
            $curlerror = curl_error($ch);

            // Return the error.
            mtrace("cURL ERROR: $curlerror. Aborting.");
            return "error";

        // Check to see that we have a proper response.
        } else if ($httpcode != "200") {

            // Return the HTTP status code.
            mtrace("SERVER ERROR - HTTP Status Code: $httpcode. Aborting.");
            return "error";
        }

        // Close the curl.
        curl_close($ch);

        // Decode the json.
        $dataobj = json_decode($json_response);

        // Return the data.
        return $dataobj;
    }

    public static function get_suffix_from_institution($student) {

        // Check if the 'Institution' key exists in the object.
        if (isset($student->Institution)) {

            // Use regular expression to match the first sequence of non-space characters.
            if (preg_match('/^\S+/', $student->Institution, $matches)) {

                // Return the data I am looking for.
                return $matches[0];
            }
        }

        // Otherwise return null.
        return null;
    }

    public static function get_email_or_idnumber($s, $student, $type) {

        // Build out the required data.
        $suffix = $s->campusname . '_' . $type;
        if (isset($student->$suffix)) {
            $data = isset($student->$suffix) ? $student->$suffix : null;
        } else {
            self::dtrace("We found a non-default user, deal with it.");
            $suffix = self::get_suffix_from_institution($student) . '_' . $type;
            $data = isset($student->$suffix) ? $student->$suffix : null;
        }
        return $data;
    }

    public static function check_istudent($s, $student) {
        global $DB;

        // Get the student email from the object.
        $email = self::get_email_or_idnumber($s, $student, 'Email');

        // Get the student legacy ID from the object.
        $lid = self::get_email_or_idnumber($s, $student, 'Legacy_ID');

        // Set up the SQL to look for the student in the LMS.
        $sql = 'SELECT *
                FROM {enrol_wds_students} stu
                WHERE stu.universal_id = "' . $student->Universal_Id . '"
                OR stu.username = "' . \core_text::strtolower($email) . '"
                OR stu.email = "' . \core_text::strtolower($email) . '"';

        $stus = $DB->get_records_sql($sql);
        if (count($stus) > 1) {
            foreach ($stus as $stu) {
                $schoolid = !is_null($stu->school_id) ?
                    ' ' . $stu->school_id . ',' :
                    "";
                mtrace('Error! IDB student ID: ' . $stu->id . ', ' .
                    $stu->universal_id . ', ' .
                    $stu->email . ',' .
                    $stu->school_id . ' - ' .
                    $stu->firstname . ' ' .
                    $stu->lastname . ' is a dupe of remote user ' .
                    $student->Universal_Id . ', ' .
                    $email . ', ' .
                    $lid . ', ' .
                    $student->First_Name . ' ' .
                    $student->Last_Name . '.');
            }

            // Do not process this student.
            $stu = null;

        } else if (count($stus) == 1) {

            // Grab the student from the array.
            self::dtrace("  Student found with universal_id: " .
                "$student->Universal_Id, email: $email, school_id: $lid.");
            $stu = reset($stus);

        } else {
            self::dtrace("  No student with universal_id: " .
                "$student->Universal_Id, email: $email, school_id: $lid.");
            $stu = null;
        }

        return $stu;
    }

    public static function update_istudent($s, $stu, $student) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_students';

        $esuffix = $s->campusname . '_Email';
        if (isset($student->$esuffix)) {
            $email = $student->$esuffix;
        } else {
            self::dtrace("\nWe found a non-default user, deal with it.");
            $esuffix = self::get_suffix_from_institution($student) . '_Email';
            $email = $student->$esuffix;
        }

        // Build out the legacy idnumber query.
        $lidsuffix = $s->campusname . '_Legacy_ID';
        if (isset($student->$lidsuffix)) {
            $lid = $student->$lidsuffix;
        } else {
            $lidsuffix = self::get_suffix_from_institution($student) . '_Legacy_ID';
            $lid = isset($student->$lidsuffix) ? $student->$lidsuffix : null;
        }

        // Build the two objects to compare.
        $stu1 = unserialize(serialize($stu));
        $stu2 = new stdClass();

        // Un/populate the two objects.
        unset($stu1->id);
        unset($stu1->username);
        unset($stu1->userid);
        unset($stu1->lastupdate);
        $stu2->universal_id = $student->Universal_Id;
        $stu2->email = $email;
        $stu2->school_id = $lid;
        $stu2->firstname = $student->First_Name;
        $stu2->preferred_firstname = isset($student->Preferred_First_Name) ?
            $student->Preferred_First_Name : null;
        $stu2->lastname = $student->Last_Name;
        $stu2->preferred_lastname = isset($student->Preferred_Last_Name) ?
            $student->Preferred_Last_Name : null;
        $stu2->middlename = isset($student->Middle_Name) ?
            $student->Middle_Name : null;

        // If the objects match.
        if (get_object_vars($stu1) === get_object_vars($stu2)) {
            self::dtrace("  - Student objects match, no update necessary.");

            // Return the original student.
            return $stu;
        } else {

            // Set the id.
            $stu2->id = $stu->id;

            // Set the datestamp.
            $stu2->lastupdate = time();

            // Update the record.
            $istudent = $DB->update_record($table, $stu2, false);

            self::dtrace("  - Updated student with universal_id: " .
                "$stu->universal_id - $student->Universal_Id, email: " .
                "$stu->email - $email, school_id: $stu->school_id - $lid.");

            return $istudent;
        }
    }

    public static function create_istudent($s, $student) {
        global $DB;

        // Build out the email query.
        $esuffix = $s->campusname . '_Email';
        $email = $student->$esuffix;

        // Build out the legacy idnumber query.
        $lidsuffix = $s->campusname . '_Legacy_ID';
        $lid = isset($student->$lidsuffix) ? $student->$lidsuffix : null;

        // Set the table.
        $table = 'enrol_wds_students';

        // Build the object.
        $data = new stdClass();
        $data->universal_id = $student->Universal_Id;
        $data->email = $email;
        $data->username = $email;
        $data->school_id = $lid;
        $data->userid = null;
        $data->firstname = $student->First_Name;
        $data->preferred_firstname = isset($student->Preferred_First_Name) ?
            $student->Preferred_First_Name : null;
        $data->lastname = $student->Last_Name;
        $data->preferred_lastname = isset($student->Preferred_Last_Name) ?
            $student->Preferred_Last_Name : null;
        $data->middlename = isset($student->Middle_Name) ?
            $student->Middle_Name : null;
        $data->lastupdate = time();

        $success = $DB->insert_record($table, $data, true);

        if (is_int($success)) {
            $stu = $DB->get_record($table, ['id' => $success]);
            self::dtrace("  - Created student with universal_id: " .
                "$stu->universal_id, email: $stu->email, school_id: $stu->school_id.");
            return $stu;
        } else {
            mtrace("  - Error! Failed to create interstitial student.");
            var_dump($student);
            return false;
        }
    }

    public static function create_update_istudent($s, $student) {
        $starttime = microtime(true);

        // Check if the student exists.
        $stu = self::check_istudent($s, $student);

        if (isset($stu->id)) {

            // Update the student.
            $updated = self::update_istudent($s, $stu, $student);

            $endtime = microtime(true);
            $elapsedtime = round($endtime - $starttime, 4);
            self::dtrace("  Student: $stu->universal_id took " .
                "$elapsedtime seconds to process.");

            return $stu;

        } else {

            // Create the student.
            $stu = self::create_istudent($s, $student);

            $endtime = microtime(true);
            $elapsedtime = round($endtime - $starttime, 4);
            self::dtrace("  Student: $stu->universal_id took " .
                "$elapsedtime seconds to process.");

            return $stu;
        }
    }

    public static function create_update_iteacher($s, $teacher) {
        $starttime = microtime(true);

        // Check if the teacher exists.
        $tea = self::check_iteacher($s, $teacher);

        if (isset($tea->id)) {

            // Update the teacher.
            $updated = self::update_iteacher($s, $tea, $teacher);

            $endtime = microtime(true);
            $elapsedtime = round($endtime - $starttime, 4);
            self::dtrace(" User: $tea->universal_id took " .
                "$elapsedtime seconds to process.");

            return $tea;

        } else {

            // Create the teacher.
            $tea = self::create_iteacher($s, $teacher);

            if (!$tea) {
                return false;
            }

            $endtime = microtime(true);
            $elapsedtime = round($endtime - $starttime, 4);
            self::dtrace(" User: $tea->universal_id took " .
                "$elapsedtime seconds to process.");

            return $tea;
        }
    }

    public static function check_iteacher($s, $teacher) {
        global $DB;

        if (!isset($teacher->Instructor_Email) || !isset($teacher->Instructor_ID)) {
            self::dtrace("Instructor Email / ID not found, skipping.");
            var_dump($teacher);
            return false;
        }

        $sql = 'SELECT *
                FROM {enrol_wds_teachers} tea
                WHERE tea.universal_id = "' . $teacher->Instructor_ID . '"
                OR tea.email = "' . $teacher->Instructor_Email . '"';

        $teas = $DB->get_records_sql($sql);
        if (count($teas) > 1) {
            foreach ($teas as $tea) {
                mtrace('Error! IDB teacher ID: ' . $tea->id . ', ' .
                    $tea->universal_id . ', ' .
                    $tea->email . ',' .
                    $tea->firstname . ' ' .
                    $tea->lastname . ' is a dupe of remote user ' .
                    $teacher->Instructor_ID . ', ' .
                    $teacher->Instructor_Email . ', ' .
                    $teacher->Instructor_First_Name . ' ' .
                    $teacher->Instructor_Last_Name . '.');
            }

            // Do not process this teacher.
            $tea = null;

        } else if (count($teas) == 1) {

            // Grab the teacher from the array.
            self::dtrace(" User found with universal_id: " .
                "$teacher->Instructor_ID, email: " .
                "$teacher->Instructor_Email.");
            $tea = reset($teas);

        } else {
            self::dtrace(" No user with universal_id: " .
                "$teacher->Instructor_ID, email: " .
                "$teacher->Instructor_Email.");
            $tea = null;
        }

        return $tea;
    }

    public static function update_iteacher($s, $tea, $teacher) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_teachers';

        // Build out the email.
        $email = $teacher->Instructor_Email;

        // Build the two objects to compare.
        $tea1 = unserialize(serialize($tea));
        $tea2 = new stdClass();

        // Un/populate the two objects.
        unset($tea1->id);
        unset($tea1->username);
        unset($tea1->userid);
        unset($tea1->school_id);
        unset($tea1->preferred_firstname);
        unset($tea1->preferred_lastname);
        unset($tea1->middlename);
        unset($tea1->lastupdate);
        $tea2->universal_id = $teacher->Instructor_ID;
        $tea2->email = $email;
        $tea2->firstname = $teacher->Instructor_First_Name;
        $tea2->lastname = $teacher->Instructor_Last_Name;

        // If the objects match.
        if (get_object_vars($tea1) === get_object_vars($tea2)) {
            self::dtrace(" User objects match, no update necessary.");

            // Return the original teacher.
            return false;
        } else {

            // Set the id.
            $tea2->id = $tea->id;

            // Set the datestamp.
            $tea2->lastupdate = time();

            // Update the record.
            $iteacher = $DB->update_record($table, $tea2, false);

            self::dtrace(" Updated user with universal_id: " .
                "$tea->universal_id - $teacher->Instructor_ID, email: " .
                "$tea->email - $email.");

            return $iteacher;
        }
    }

    public static function create_iteacher($s, $teacher) {
        global $DB;

        // Build out the email query.
        if (isset($teacher->Instructor_Email)) {
            $email = $teacher->Instructor_Email;
        } else {
            mtrace(" Error! Failed to create interstitial user. No email provided.");
            mtrace($teacher->Instructor_ID);
            return false;
        }

        // Set the table.
        $table = 'enrol_wds_teachers';

        // Build the object.
        $data = new stdClass();
        $data->universal_id = $teacher->Instructor_ID;
        $data->email = $email;
        $data->username = $email;
        $data->userid = null;
        $data->firstname = $teacher->Instructor_First_Name;
        $data->lastname = $teacher->Instructor_Last_Name;
        $data->lastupdate = time();

        $success = $DB->insert_record($table, $data, true);

        if (is_int($success)) {
            $tea = $DB->get_record($table, ['id' => $success]);
            self::dtrace(" Created user with universal_id: " .
                "$tea->universal_id, email: $tea->email.");
            return $tea;
        } else {
            mtrace(" Error! Failed to create interstitial user.");
            var_dump($teacher);
            return false;
        }
    }

    public static function insert_update_teacher_enrollment($sectionid, $universalid, $role, $status) {
        global $DB;

        // Set the table.
        $table = 'enrol_wds_teacher_enroll';

        // We do not have an instructor or a role.
        if (is_null($universalid) && is_null($role)) {

            // Build the SQL to grab existing instructors.
            $usql = 'SELECT * FROM {enrol_wds_teacher_enroll} e
                    WHERE e.section_listing_id = "' . $sectionid . '"
                        AND (e.status = "enroll" OR e.status = "enrolled")
                        AND (e.role = "teacher" OR e.role = "primary")';

            // Fetch the existing instructors.
            $uenrs = $DB->get_records_sql($usql);

            // Build an empty array for later use.
            $unenrolls = [];

            // If we have existing teachers who are no longer being sent over by the sis.
            if (!empty($uenrs)) {

                // Loop through them.
                foreach ($uenrs as $uenr) {

                    // Build the sql to update their records.
                    $sql = 'UPDATE {enrol_wds_teacher_enroll} e
                                SET e.status = "unenroll",
                                    e.prevstatus = "' . $uenr->status . '",
                                    e.role = "' . $uenr->role . '",
                                    e.prevrole = "' . $uenr->role . '"
                            WHERE e.section_listing_id = "' . $sectionid . '"
                                AND e.universal_id = "' . $uenr->universal_id . '"
                                AND (e.status = "enroll" OR e.status = "enrolled")
                                AND (e.role = "teacher" OR e.role = "primary")';

                    // Execute the SQL.
                    $unenrolls[] = $DB->execute($sql);

                    // Log what we did.
                    self::dtrace("  $uenr->universal_id set to unenroll in $sectionid.");
                }

                self::dtrace(" All enrolled teachers in $sectionid set to unenroll.");
            }

            return $unenrolls;
        }

        // We have an instructor and a role.
        if (!is_null($universalid) && !is_null($role)) {

            // Set the parms up.
            $parm = ['section_listing_id' => $sectionid, 'universal_id' => $universalid];

            // Get the enrollment.
            $enr = $DB->get_record($table, $parm);

            // We do not have an enrollment, so insert it.
            if (!$enr) {

                // Build out the data object.
                $data = new stdClass();
                $data->universal_id = $universalid;
                $data->section_listing_id = $sectionid;
                $data->role = $role;
                $data->status = $status;

                // Insert the record.
                $enroll = $DB->insert_record($table, $data, $returnid = true);
                self::dtrace(" - Inserted $universalid in $sectionid with role: " .
                    "$data->role and status: $data->status");

                return $enroll;

            // We returned a matching enrollment.
            } else {

                // Build out the data object.
                $data = unserialize(serialize($enr));

                $data->universal_id = $universalid;
                $data->section_listing_id = $sectionid;
                $data->prevrole = $enr->role;
                $data->role = $role;
                $data->status = $status;

                // Deal with previous statuses.
                if ($enr->status == 'unenroll' && (
                    $enr->prevstatus == 'enroll' || $enr->prevstatus == 'enrolled')) {
                    $data->prevstatus = $data->prevstatus;
                } else if ($enr->status == 'enrolled' && $status == 'enroll') {
                    $data->status = $enr->status;
                    $data->prevstatus = $enr->status;
                } else {
                    $data->prevstatus = $enr->status;
                }

                // Compare the objects.
                if (get_object_vars($data) === get_object_vars($enr)) {
                    self::dtrace(" - Enrollment entry: " .
                        "$data->id matches exactly, skipping.");

                    return $enr;
                } else {

                    // Update the record and log.
                    $enroll = $DB->update_record($table, $data, $returnid = true);
                    self::dtrace(" - Updated: $data->id - $universalid in " .
                        "$sectionid with role: $data->role " .
                        "and status: $data->status");

                    return $enroll;
                }
            }
        }
    }

    public static function get_potential_new_mstudents() {
        global $DB;

        // Users that don't have a Moodle equivalent created or mapped.
        $sql = "SELECT * FROM {enrol_wds_students} stu
                WHERE stu.userid IS NULL";

        $nusers = $DB->get_records_sql($sql);

        return $nusers;
    }

    public static function get_potential_new_mteachers() {
        global $DB;

        // Users that don't have a Moodle equivalent created or mapped.
        $sql = "SELECT * FROM {enrol_wds_teachers} tea
                WHERE tea.userid IS NULL";

        $nusers = $DB->get_records_sql($sql);

        return $nusers;
    }

    public static function mass_mteacher_updates() {
        global $CFG, $DB;

        // Build the auth methods and choose the top one or manual if none are set.
        if (!empty($CFG->auth)) {
            $auth = explode(',', $CFG->auth);
            $auth = reset($auth);
        } else {
            $auth = 'manual';
        }

        $sql = "UPDATE {enrol_wds_teachers} tea
            INNER JOIN {user} u on u.id = tea.userid
            SET u.idnumber = tea.universal_id,
                u.email = tea.email,
                u.username = tea.username,
                u.firstname = COALESCE(tea.preferred_firstname, tea.firstname),
                u.lastname = COALESCE(tea.preferred_lastname, tea.lastname),
                u.middlename = tea.middlename,
                u.timemodified = tea.lastupdate,
                u.auth = '$auth'
            WHERE tea.userid IS NOT NULL
                AND u.deleted = 0
                AND u.suspended = 0
                AND (tea.universal_id != u.idnumber
                    OR tea.email != u.email
                    OR tea.username != u.username
                    OR COALESCE(tea.preferred_firstname, tea.firstname) != u.firstname
                    OR COALESCE(tea.preferred_lastname, tea.lastname) != u.lastname
                    OR tea.middlename != u.middlename
                    OR u.auth != '$auth')";

        try {

            // Update them.
            $mupdates = $DB->execute($sql);
        } catch (Exception $e) {
            $mupdates = false;
            $error = $e->getMessage();
            $cleanerror = trim(strtok($error, '\n'));
            $cleanerror = preg_replace('/\n/', ' ', $error);
            $cleanerror = preg_replace('/UPDATE.*/', '', $cleanerror);
            $cleanerror = trim($cleanerror) . ')';
            mtrace("Error! " . $cleanerror);
        }

        if (!$mupdates) {

            // Build out a list of potential duplicates by email.
            $dupes = self::get_faculty_dupes();

            // Loop through the dupes.
            foreach ($dupes as $dupe) {

                // Log the dupes.
                mtrace("Error! DUPE! $dupe->userids; " .
                    "$dupe->email; $dupe->fullnames");
            }
        }

        return $mupdates;
    }

    public static function get_faculty_dupes() {
        global $DB;

        // Build out a list of potential duplicates by email.
        $dupesql = "SELECT
            u.email,
            COUNT(u.id) AS usercount,
            GROUP_CONCAT(u.id ORDER BY u.id ASC SEPARATOR ', ') AS userids,
            GROUP_CONCAT(u.username ORDER BY u.id ASC SEPARATOR ', ') AS usernames,
            GROUP_CONCAT(
                CONCAT(u.firstname, ' ', u.lastname)
                ORDER BY u.id ASC SEPARATOR ', '
            ) AS fullnames
            FROM {user} u
            WHERE u.deleted = 0
            GROUP BY u.email HAVING usercount > 1";

        // Get the dupes.
        $dupes = $DB->get_records_sql($dupesql);

        return $dupes;
    }

    public static function mass_mstudent_updates() {
        global $CFG, $DB;

        // Build the auth methods and choose the top one or manual if none are set.
        if (!empty($CFG->auth)) {
            $auth = explode(',', $CFG->auth);
            $auth = reset($auth);
        } else {
            $auth = 'manual';
        }

        $sql = "UPDATE {enrol_wds_students} stu
            INNER JOIN {user} u on u.id = stu.userid
            SET u.idnumber = stu.universal_id,
                u.email = stu.email,
                u.username = stu.username,
                u.firstname = stu.preferred_firstname,
                u.lastname = stu.preferred_lastname,
                u.middlename = stu.middlename,
                u.timemodified = stu.lastupdate,
                u.auth = '$auth'
            WHERE stu.userid IS NOT NULL
                AND u.deleted = 0
                AND u.suspended = 0
                AND (stu.universal_id != u.idnumber
                    OR stu.email != u.email
                    OR stu.username != u.username
                    OR stu.preferred_firstname != u.firstname
                    OR stu.preferred_lastname != u.lastname
                    OR stu.middlename != u.middlename
                    OR u.auth != '$auth')";

        $mupdates = $DB->execute($sql);

        return $mupdates;
    }

    public static function get_potential_mteacher_updates() {
        global $CFG, $DB;

        // Build the auth methods and choose the top one or manual if none are set.
        if (!empty($CFG->auth)) {
            $auth = explode(',', $CFG->auth);
            $auth = reset($auth);
        } else {
            $auth = 'manual';
        }

        // Build the sql to get a count of users that need to be updated.
        $sql = "SELECT COUNT(u.id) AS usercount
               FROM {enrol_wds_teachers} tea
                   INNER JOIN {user} u ON u.id = tea.userid
               WHERE tea.userid IS NOT NULL
                   AND u.deleted = 0
                   AND u.suspended = 0
                   AND (tea.universal_id != u.idnumber
                       OR tea.email != u.email
                       OR tea.username != u.username
                       OR COALESCE(tea.preferred_firstname, tea.firstname) != u.firstname
                       OR COALESCE(tea.preferred_lastname, tea.lastname) != u.lastname
                       OR tea.middlename != u.middlename
                       OR u.auth != '$auth')";

        // Get the count.
        $mupdates = $DB->get_record_sql($sql);

        // Return the count as an integer for comparison later.
        return (int) $mupdates->usercount;
    }

    public static function get_potential_mstudent_updates() {
        global $CFG, $DB;

        // Build the auth methods and choose the top one or manual if none are set.
        if (!empty($CFG->auth)) {
            $auth = explode(',', $CFG->auth);
            $auth = reset($auth);
        } else {
            $auth = 'manual';
        }

        // Build the sql to get a count of users that need to be updated.
        $sql = "SELECT COUNT(u.id) AS usercount
               FROM {enrol_wds_students} stu
                   INNER JOIN {user} u ON u.id = stu.userid
               WHERE stu.userid IS NOT NULL
                   AND u.deleted = 0
                   AND u.suspended = 0
                   AND (stu.universal_id != u.idnumber
                       OR stu.email != u.email
                       OR stu.username != u.username
                       OR stu.preferred_firstname != u.firstname
                       OR stu.preferred_lastname != u.lastname
                       OR stu.middlename != u.middlename
                       OR u.auth != '$auth')";

        // Get the count.
        $mupdates = $DB->get_record_sql($sql);

        // Return the count as an integer for comparison later.
        return (int) $mupdates->usercount;
    }

    public static function reconcile_interstitial_users($type = 'student', $keyword = 'username') {
        global $DB;

        // We're building SQL here, so lets enumerate this shit.
        if ($keyword === 'email') {
            $kw1 = 'email';
            $kw2 = 'email';
        } else if ($keyword === 'idnumber') {
            $kw1 = 'universal_id';
            $kw2 = 'idnumber';
        } else {
            $kw1 = 'username';
            $kw2 = 'username';
        }

        // We're building SQL here, so lets enumerate this shit.
        if ($type === 'teacher') {
            $prefix = 'tea';
            $tablesuffix = 'teachers';
        } else {
            $prefix = 'stu';
            $tablesuffix = 'students';
        }

        // Build the SQL.
        $sql = "UPDATE {enrol_wds_$tablesuffix} $prefix
                   INNER JOIN {user} u ON $prefix.$kw1 = u.$kw2
               SET $prefix.userid = u.id
               WHERE $prefix.userid IS NULL
                   AND u.deleted = 0
                   AND u.suspended = 0
                   AND ($prefix.universal_id = u.idnumber
                       OR LOWER($prefix.email) = LOWER(u.email)
                       OR LOWER($prefix.username) = LOWER(u.username))";

         // Do the nasty.
         $updates = $DB->execute($sql);

         // This will return a bool.
         return $updates;
    }

    public static function build_user_object($student) {
        global $CFG;

        // Build the , 2)auth methods and choose the top one or manual if none are set.
        if (!empty($CFG->auth)) {
            $auth = explode(',', $CFG->auth);
            $auth = reset($auth);
        } else {
            $auth = 'manual';
        }

        // Set up the user object.
        $user = new stdClass();

        // Use the auth from above.
        $user->auth = $auth;

        // Make sure we care only setting usernames and emails in lowecase.
        $user->username = \core_text::strtolower($student->username);
        $user->email = \core_text::strtolower($student->email);

        // Idnumber is universal ID. TODO: Deal with school ID as well.
        $user->idnumber = $student->universal_id;

        // Make sure we're using their preferred names.
        if (is_null($student->preferred_firstname)
            || $student->firstname == $student->preferred_firstname) {
            $user->firstname = $student->firstname;
        } else {
            $user->firstname = $student->preferred_firstname;
        }

        // Make sure we're using their preferred names.
        if (is_null($student->preferred_lastname)
            || $student->lastname == $student->preferred_lastname) {
            $user->lastname = $student->lastname;
        } else {
            $user->lastname = $student->preferred_lastname;
        }

        // If they have a middle name, use it.
        $user->middlename = isset($student->middlename) ?
            $student->middlename : null;

        // Use the default language when building the object.
        $user->lang = $CFG->lang;

        // Let's check and set the school_id to the wds school_id if we have it.
        $user->school_id = $student->school_id;

        // Required BS.
        $user->confirmed = "1";
        $user->deleted = "0";
        $user->suspended = "0";
        $user->policyagreed = "1";
        $user->mnethostid = $CFG->mnet_localhost_id;

        // Set this to what is in Workday.
        $user->timemodified = (int) $student->lastupdate;

        return $user;
    }

    /**
     * Retrieve a user by a specific field and value.
     *
     * @param @string $field. The field to search by ('username', 'email', 'idnumber').
     * @param @string $value. The value to match.
     * @return @object $users[0] | @bool false. If we find a single user match, return them.
     */
    static function get_user_by_field($field, $value) {
        global $DB;

        // Set the table.
        $table = 'user';

        // Validate the field to prevent SQL injection.
        $allowedfields = ['username', 'email', 'idnumber'];
        if (!in_array($field, $allowedfields)) {
            throw new coding_exception('Invalid field name');
        }

        // Perform the database query.
        $users = $DB->get_records($table, [$field => $value]);

        // No match! Return false.
        if (!is_array($users) || count($users) == 0) {
            return false;
        }

        $usercount = count($users);

        // We have at least one match. Return the first one.
        if ($usercount == 1) {
          $user = reset($users);
          return $user;
        } else {
            mtrace("ERROR! $usercount matches for $field->$value, " .
                "skipping update.");
            return false;
        }
    }

    public static function find_moodle_user($student) {
        global $CFG;

        require_once($CFG->dirroot . '/user/lib.php');

        // Set up the parms in priority order.
        $parms = [
            'idnumber' => $student->universal_id,
            'username' => $student->username,
            'email' => $student->email
        ];

        foreach ($parms as $field => $value) {

            // Only check fields that have values.
            if (!empty($value)) {

                // Get the user.
                $user = self::get_user_by_field($field, $value);

                // Return the user if we have one.
                if ($user) {
                    self::dtrace("Found $field->$value as " .
                        "$user->id: $field->" . $user->$field . ".");
                    return $user;
                }
            }
        }

        // No matching user found!
        return false;
    }

    static function update_user_if_needed($user, $muser) {

        // Compare each field and track any changes.
        $changes = false;

        // TODO: Deal with 'school_id' somehow.

        // List the fields to compare.
        $fields_to_check = [
            'username',
            'email',
            'idnumber',
            'firstname',
            'lastname',
            'middlename'
        ];

        // Loop through fields and check for differences.
        foreach ($fields_to_check as $field) {
            $userfield = \core_text::strtolower($user->$field);
            $muserfield = \core_text::strtolower($muser->$field);

            // Check if the provided value exists and differs from the existing value.
            if (isset($user->$field)
                && strcasecmp($muserfield, $userfield) != 0) {

                // If there's a difference, update the field.
                $muser->$field = $user->$field;
                $changes = true;
            }
        }

        // If changes were made, update the user.
        if ($changes) {

            // Ensure password field is not updated or cleared.
            if (isset($muser->password)) {

                // Don't include the password field in the update.
                unset($muser->password);
            }

            // Ensure we're not doing anything stupid here either.
            if (isset($muser->lang)) {

                // Don't include language preferences here.
                unset($muser->lang);
            }

            try {

                // Update them.
                $updated = user_update_user($muser);
            } catch (Exception $e) {
                $updated = false;
                mtrace("Error! " . $e->getMessage());
            }

            // If the update was successful, log it and return true.
            if ($updated) {
                self::dtrace("User $muser->id updated successfully.");
                return $updated;
            } else {
                return $updated;
            }
        } else {
            self::dtrace("No changes were found in the " .
                "above user objects (case insensitive).");
        }

        return false;
    }

    public static function create_update_msuser($student, $courseid=null) {
        global $CFG, $DB;

        // Get the required moodle libraries for users.
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->libdir . '/moodlelib.php');

        // Check if there's a Moodle user and fetch them.
        $muser = self::find_moodle_user($student);

        // Build a user object for comparison.
        $user = self::build_user_object($student);

        // We have a user.
        if (isset($muser->id)) {

            // Only do this for potential new users.
            if (is_null($student->userid)) {

                // Set the interstitial userid to the returned moodle user id.
                $student->userid = $muser->id;

                // Update the interstitial DB so we don't mess with this user again.
                $useridmap = $DB->update_record('enrol_wds_students', $student);

                // Compare and update as needed.
                $moodleuser = self::update_user_if_needed($user, $muser);

            // Update old user. We shound never get here.
            } else {

                // Compare and update as needed.
                $moodleuser = self::update_user_if_needed($user, $muser);
            }

        // We don't have an existing user.
        } else {

            // Create them.
            try {

                // Create a new user.
                $moodleuser = user_create_user($user);
            } catch (Exception $e) {
                $moodleuser = false;
                mtrace("Error! " . $e->getMessage());
            }

            if ($moodleuser) {

                // Set the interstitial userid to the returned moodle user id.
                $student->userid = $moodleuser;

                // Update the interstitial DB so we don't mess with this user again.
                $useridmap = $DB->update_record('enrol_wds_students', $student);
            }
        }
    }

    // Delete all test courses!
    public static function delete_wds_test_courses() {
        global $CFG, $DB;

        require_once($CFG->libdir . '/moodlelib.php');

        define('SKIP_BACKUP_ON_DELETE', true);

        // Purge caches because we're not doing this when deleting.
        purge_all_caches();

        // Get the WDS test courses.
        $csql = "SELECT * FROM {course} WHERE fullname LIKE 'WDS - %'";
        $courses = $DB->get_records_sql($csql);

        // Set this to not back them up.
        $options = ['skipbackup' => true];

        // Loop through these.
        foreach ($courses as $course) {

            // Delete the course using Moodle's course deletion API NOT purging caches or backing up.
            delete_course($course, false, $options);
        }

        // Purge caches because we're not doing this when deleting.
        purge_all_caches();
    }

    public static function get_potential_new_mshells($s, $period) {
        global $CFG, $DB;

        // Do we want our shells built with common sections merged per teacher?
        if ($s->course_grouping == 1) {
            $uniquer = "CONCAT(sec.course_definition_id,'_',tea.universal_id) AS coursesection";
            $grouper = "GROUP BY per.id, cou.course_listing_id, tenr.universal_id";
        } else {
            $uniquer = "sec.course_section_definition_id AS coursesection";
            $grouper = "GROUP BY per.id, sec.course_section_definition_id";
        }

        // Build the SQL to retreive the required data to build shells for primary instructors.
        $sql = "SELECT
            $uniquer,
            per.period_year,
            per.period_type,
            per.start_date,
            per.end_date,
            cou.course_subject_abbreviation,
            cou.course_subject,
            cou.course_abbreviated_title,
            cou.course_number,
            cou.academic_level,
            sec.class_type,
            tea.universal_id,
            tea.userid,
            tea.username,
            tea.email,
            tea.preferred_firstname,
            tea.firstname,
            tea.preferred_lastname,
            tea.lastname,
            sec.delivery_mode,
            GROUP_CONCAT(
                sec.id ORDER BY sec.section_listing_id ASC
            ) AS sectionids,
            GROUP_CONCAT(
                sec.section_number ORDER BY sec.section_listing_id ASC
            ) AS sections,
            GROUP_CONCAT(
                tenr.role ORDER BY tenr.section_listing_id ASC
            ) AS roles
            FROM {enrol_wds_periods} per
                INNER JOIN {enrol_wds_sections} sec
                    ON sec.academic_period_id = per.academic_period_id
                INNER JOIN {enrol_wds_courses} cou
                    ON sec.course_listing_id = cou.course_listing_id
                INNER JOIN {enrol_wds_teacher_enroll} tenr
                    ON sec.section_listing_id = tenr.section_listing_id
                INNER JOIN {enrol_wds_teachers} tea
                    ON tenr.universal_id = tea.universal_id
            WHERE sec.controls_grading = 1
                AND (
                    sec.wd_status = 'Open' OR
                    sec.wd_status = 'Closed' OR
                    sec.wd_status = 'Waitlist'
                )
                AND tenr.role = 'primary'
                AND sec.academic_period_id = '$period->academic_period_id'
                AND (
                    sec.idnumber IS NULL OR
                    sec.moodle_status = 'pending'
                )
                $grouper
            ORDER BY cou.course_listing_id ASC";

        // Get the data.
        $mshells = $DB->get_records_sql($sql);

        return $mshells;
    }

    public static function process_shell_name($s, $mshell) {

        // Fetch admin-configured naming format.
        $format = $s->namingformat;

        // Define available placeholders and their corresponding values.
        $placeholders = [
            '{period_year}' => $mshell->period_year,
            '{period_type}' => $mshell->period_type,
            '{course_subject_abbreviation}' => $mshell->course_subject_abbreviation,
            '{course_number}' => $mshell->course_number,
            '{course_type}' => $mshell->class_type,
            '{firstname}' => isset($mshell->preferred_firstname)
                ? $mshell->preferred_firstname
                : $mshell->firstname,
            '{lastname}' => isset($mshell->preferred_lastname)
                ? $mshell->preferred_lastname
                : $mshell->lastname,
            '{delivery_mode}' => $mshell->delivery_mode == 'On Campus'
                ? ''
                : '(' . $mshell->delivery_mode . ')'
        ];

        // Modify the placeholders based on the settings.
        foreach ($placeholders as $placeholder => $value) {

            // Check if the placeholder is specified in the settings.
            if (isset($settings[$placeholder])) {

                // If specified, use the value from settings.
                $placeholders[$placeholder] = $settings[$placeholder] ?: '';
            }
        }

        // Replace only the placeholders that are part of the format string.
        $shellname = $format;
        foreach ($placeholders as $placeholder => $value) {

            // Only replace placeholders that are actually in the format.
            if (strpos($format, $placeholder) !== false) {
                $shellname = str_replace($placeholder, $value, $shellname);
            }
        }

        // Return the formatted shell name.
        return trim($shellname);
    }

    public static function wds_create_moodle_groups($course, $mshell) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/group/lib.php');

        // Get the sections.
        $sections = explode(",", $mshell->sections);

        foreach ($sections as $section) {

            // Build out the groupname.
            $groupname = "$mshell->course_subject_abbreviation $mshell->course_number $section";

            // Build out an array of groupids.
            $groupids = [];

            // Check if the group already exists in the course.
            $existinggroup = $DB->get_record('groups',
                ['courseid' => $course->id, 'name' => $groupname], 'id');

            if (isset($existinggroup->id)) {
                self::dtrace("  Group '$groupname' already exists in $course->fullname. Skipping.");

                // Add the existing groupid to the array.
                $groupids[] = $existinggroup->id;

                continue;
            } else {

                // Create the group and return the groupid.
                $groupid = self::wds_create_course_group($course->id, $groupname);

                // Add the new groupid to the array of groupids.
                $groupids[] = $groupid;
            }
        }
        return $groupids;
    }

    public static function get_numeric_course_value($mshell) {

        // Extract the numeric portion from the start of the string.
        preg_match('/^\d+/', $mshell->course_number, $matches);

        // If we have a match, set it in the $mshell object..
        if (!empty($matches)) {
            $numerical_value = (int)$matches[0];

            // Return the numerical value.
            return $numerical_value;
        }

        // Return nothing.
        return null;
    }

    public static function create_moodle_shell($mshell, $userprefs) {
        global $CFG, $DB;

        // Get some moodle files as needed.
        require_once($CFG->dirroot . '/course/lib.php');

        // Get or create the course category in the specified parent.
        $cat = self::get_create_course_category_id($mshell);

        // Get settings.
        $s = self::get_settings();

        // Get the Moodle course defaults.
        $coursedefaults = get_config('moodlecourse');

        // Define course settings.
        $course = new stdClass();
        $course->fullname = $mshell->fullname;
        $course->shortname = $mshell->fullname;
        $course->idnumber = self::build_mshell_idnumber($mshell);
        $course->category = $cat->id;
        $course->visible = $s->visible ?? 0;
        $course->format = isset($userprefs->format) ?
            $userprefs->format :
            $coursedefaults->format;
        $course->groupmode = $coursedefaults->groupmode;
        $course->groupmodeforce = $coursedefaults->groupmodeforce;
        $course->summary = $mshell->course_abbreviated_title;
        $course->summaryformat = FORMAT_PLAIN;
        $course->startdate = $mshell->start_date;

        $course->enddate = $mshell->end_date + (($s->erange / 3) * 86400);
        $course->enablecompletion = $coursedefaults->enablecompletion;
        $course->maxsections = $coursedefaults->maxsections;
        $course->newsitems = $coursedefaults->newsitems;
        $course->showreports = $coursedefaults->showreports;
        $course->numsections = $coursedefaults->numsections;
        $course->showgrades = 1;
        $course->lang = $CFG->lang;

        $exists = $DB->get_record('course', ['shortname' => $course->shortname]);

        // If it exists create some groups.
        if (isset($exists->id)) {
            self::dtrace("  $course->fullname already exists. Updating idb idnumber.");

            // Create all the groups in the course.
            $groups = self::wds_create_moodle_groups($exists, $mshell);
        }

        // If it exists and the idnumbers match, update the interstitial record.
        if (isset($exists->id) && $exists->idnumber == $course->idnumber) {
            $sectiontable = 'enrol_wds_sections';
            $sectionids = explode(",", $mshell->sectionids);

            // Loop through the section ids.
            foreach ($sectionids as $sectionid) {

                // Build the parms.
                $parms = [
                    'id' => $sectionid,
                    'idnumber' => $course->idnumber,
                    'moodle_status' => $exists->id
                ];

                // Update the record.
                $updated = $DB->update_record($sectiontable, $parms);
                self::dtrace("   Course idumber / moodle_status updated in $sectiontable for id: $sectionid.");
            }

            return $exists;

        // This is not right!
        } else if (isset($exists->id) && $exists->idnumber != $course->idnumber) {
            mtrace(" Error! We should never have a matching " .
                "shortname with a mismatched idnumber!");
            mtrace(" - Error! Course Shell: $exists->idnumber, " .
                "Interstitial record: $course->idnumber.");
            return false;
        }

        // Use the standard Moodle function to create the course.
        $moodlecourse = create_course($course);

        // If the above worked, update idnumber.
        if ($moodlecourse) {

            // Build out the course groups.
            $groups = self::wds_create_moodle_groups($moodlecourse, $mshell);

            self::dtrace("  Created $course->fullname. " .
                "Updating idb idnumber.");

            // Set the table.
            $sectiontable = 'enrol_wds_sections';

            // We've returned a csv of sections, make them into an array.
            $sectionids = explode(",", $mshell->sectionids);

            // Loop through these sections and update the corresponding idnumbers.
            foreach ($sectionids as $sectionid) {
                $parms = [
                    'id' => $sectionid,
                    'idnumber' => $course->idnumber,
                    'moodle_status' => $moodlecourse->id
                ];

                // Do the nasty.
                $updated = $DB->update_record($sectiontable, $parms);
            }
        }

        return $moodlecourse;
    }

    public static function build_mshell_idnumber($mshell) {

        // Build out the idnumber.
        $idnumber = $mshell->period_year .
            $mshell->period_type .
            $mshell->course_subject_abbreviation .
            $mshell->course_number . '-' .
            $mshell->universal_id;

        return $idnumber;
    }

    public static function get_create_course_category_id($mshell) {
        global $DB;

        // Get settings.
        $s = self::get_settings();

        // Set the table.
        $table = 'course_categories';

        // We might be trying to find or create a parent category.
        if ($s->autoparent === 1) {
            $parentcat = 0;

            $parentname = "$mshell->period_type $mshell->period_year";
            $parentpathsql = "AND cc.path = CONCAT('/$parentname/', cc.id)";
            $catnamesql = "AND cc.name = '$parentname'";

        } else {

            // Set this relative to the configured parent.
            $parentcat = $s->parentcat;
            $parentpathsql = "AND cc.path = CONCAT('/$parentcat/', cc.id)";
            $catnamesql = "AND cc.name = '$mshell->course_subject_abbreviation'";
        }

        $catname = isset($parentname) ? $parentname : $mshell->course_subject_abbreviation;
        $catdesc = isset($parentname) ? $parentname : $mshell->course_subject;

        // This sql is annoying.
        $ccsql = "SELECT *
            FROM {course_categories} cc
            WHERE cc.parent = $parentcat
                $parentpathsql
                $catnamesql
            ORDER BY cc.name ASC";

        // Set the category object.
        $category = $DB->get_records_sql($ccsql);

        if (is_array($category) && !empty($category) && count($category) > 1) {
            mtrace("  ERROR! Multiple categories for $catname. Deal with it.");
        } else if (is_array($category) && !empty($category)) {
            $category = reset($category);

            // We have a match, let's see if the subject needs updating.
            if ($category->description != $catdesc) {

                // Mismatch! Update to the course subject.
                $category->description = $catdesc;

                // Actually do it.
                $updated = $DB->update_record($table, $category);

                // Something went wrong!
                if (!$updated) {
                    mtrace("  ERROR! Failed to updated course category: " .
                        "$cagtegory->id.");
                }
            }
        } else {
            self::dtrace("We do not have a matching $catname " .
                "category. Create it.");

            // Moodle wants an array for the new category.
            $categorydata = [
                'name' => $catname,

                // TODO: Use the settings value for parent category.
                'parent' => $parentcat,
                'description' => $catdesc,
                'descriptionformat' => FORMAT_HTML,
                'visible' => 1
            ];

            // Create it!
            $category = \core_course_category::create($categorydata);
        }

        return $category;
    }

    public static function update_interstitial_enrollment_status(
        $enrollment,
        $teacher = false,
        $status = null
    ) {
        global $DB;

        // If we've sent the teacher flag, use the teacher table.
        $table = $teacher ? 'enrol_wds_teacher_enroll' : 'enrol_wds_student_enroll';

        // Build out the object.
        $enrrecord = new stdClass();

        // Get the insterstitial enrollment record from the $enrollment object.
        $enrrecord->id = $enrollment->enrollment_id;

        // Set the statuses appropriately.
        $enrrecord->prevstatus = isset($enrollment->moodle_enrollment_status) ?
            $enrollment->moodle_enrollment_status :
            (is_null($status) ?
            $enrollment->moodle_enrollment_status :
            $status);

        $enrrecord->status = is_null($status) ?
            $enrollment->moodle_enrollment_status . 'ed' :
            $status;

        // Do the nasty.
        $completed = $DB->update_record(
            $table,
            $enrrecord,
            $returnid = true
        );

        return $completed;
    }

    /**
     * Finds similar objects.
     *
     * @param  @object $obj1
     * @param  @object $obj2
     *
     * @return @float $similarity
     */
    public static function wdstu_compareobjects($obj1, $obj2) {

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

    public static function dtrace($message, $indent = null) {
        global $CFG;

        // Set the indenter.
        $indent = !is_null($indent) ? '' : $indent;

        // If debugdisplay is on.
        if ($CFG->debugdisplay == 1) {
            $mtrace = mtrace($message);
            return $mtrace;
        } else {
            self::$dtc++;
            if (self::$dtc % 50 === 0) {
                $mtrace = print(".\n");
            } else {
                $mtrace = print(".");
            }

            if (PHP_SAPI === 'cli') {
                return $mtrace;
            }
        }
    }

    /**
     * Contructs and sends error emails using Moodle functionality.
     *
     * @package   enrol_workdaystudent
     *
     * @param     @object $emaildata
     * @param     @object $s
     *
     * @return    @bool
     */
    public static function send_wdstu_email($emaildata, $s) {
        global $CFG, $DB;

        // Get email subject from email log.
        $emailsubject = $emaildata->subject;

        // Get email content from email log.
        $emailcontent = $emaildata->body;

        // Grab the list of usernames from Moodle.
        $usernames = explode(",", $s->contacts);

        // Set up the users array.
        $users = [];

        // Loop through the usernames and add each user object to the user array.
        foreach ($usernames as $username) {

            // Make sure we have no spaces.
            $username = trim($username);

            // Add the user object to the array.
            $users[] = $DB->get_record('user', ['username' => $username]);
        }

        // Send an email to each of the above users.
        foreach ($users as $user) {

            // Email the message.
            email_to_user($user,
                get_string("workdaystudent_emailname", "enrol_workdaystudent"),
                $emailsubject . " - " . $CFG->wwwroot,
                $emailcontent);
        }
    }

    // I do not know if I will need this and it is sorta wasteful.
    public static function wds_get_insert_missing_students($courseid = null) {
        global $DB;

        // Make sure we ahve something for course id.
        if (!is_null($courseid)) {

            // Check if the $courseid consists of only digits.
            if (ctype_digit((string) $courseid)) {

                // It's a plain integer or numeric string.
                $parms = ['courseid' => $courseid];

                // Finish out the sql.
                $andsql = 'AND sec.moodle_status = :courseid';
            } else {

                // It's a structured course section definition.
                $parms = ['courseid' => $courseid];

                // Finish out the sql.
                $andsql = 'AND sec.course_section_definition_id = :courseid';
            }

            // SQL to get all the students without demographic data for this course.
            $sql = "SELECT stuenr.universal_id
                FROM {enrol_wds_student_enroll} stuenr
                    INNER JOIN {enrol_wds_sections} sec
                        ON sec.section_listing_id = stuenr.section_listing_id
                        " . $andsql . "
                    LEFT JOIN {enrol_wds_students} stu
                        ON stu.universal_id = stuenr.universal_id
                WHERE stuenr.status = 'enroll'
                    AND stu.id IS NULL
                GROUP BY stuenr.universal_id";

            // Fetch the data from the idb.
            $missingstudents = $DB->get_records_sql($sql, $parms);

        } else {

            // SQL to get all the students without demographic data regardless of period.
            $sql = "SELECT stuenr.universal_id
                FROM {enrol_wds_student_enroll} stuenr
                    LEFT JOIN {enrol_wds_students} stu
                        ON stu.universal_id = stuenr.universal_id
                WHERE stuenr.status = 'enroll'
                    AND stu.id IS NULL
                GROUP BY stuenr.universal_id";

            // Fetch the data from the idb.
            $missingstudents = $DB->get_records_sql($sql);
        }

        // Get settings.
        $s = workdaystudent::get_settings();

        // Make sure we're grabbing all current periods below.
        $s->allperiods = true;

        // Get current periods (all of them regardless of enabled status).
        $periods = workdaystudent::get_current_periods($s);

        // Loop through all the missing students and fetch their data.
        foreach ($missingstudents as $missingstudent) {

            // Loop through all the current the periods.

            // We will end up fetching more than once sometimes.
            foreach ($periods as $period) {

                // Fetch the data for the student.
                $foundstudents = workdaystudent::get_students($s,
                    $period->academic_period_id,
                    $missingstudent->universal_id);

                // If any were found, do some stuff.
                if ($foundstudents) {

                    // I cannot believe we ahve to do this trash.
                    if (count($foundstudents) > 1) {

                        // Loop through the duplicate foundstudents and id the reporting record.
                        foreach($foundstudents as $foundstudent) {

                            // Make sure it's the reporting record.
                            if ($foundstudent->Is_Reporting_Record == "1") {

                                // Set the student record.
                                $student = $foundstudent;

                                // Drop out of the foreach.
                                continue;
                            }
                        }

                    // We only have one record in the array.
                    } else {

                        // This will only be one student, so reset the array.
                        $student = reset($foundstudents);
                    }

                    // Get their email.
                    $email = workdaystudent::wds_email_finder($s, $student);

                    // We do not have an email, try the next one.
                    if (is_null($email)) {
                        continue;
                    }

                    // We could probably use create_istudent, but this is safer.
                    $stu = workdaystudent::create_update_istudent($s, $student);

                    // Insert the metadata for the student we just found and inserted.
                    $stumeta = workdaystudent::insert_all_studentmeta($s, $stu, $student, $period);

                    // Get the potential new student user object for later.
                    $nsusers = workdaystudent::get_potential_new_mstudents();

                    // In theory we should only have one user here, but if something got missed, this is safer.
                    foreach($nsusers as $nsuser) {

                        // Create or update the Moodle user and insert the userid into the itable.
                        $msuser = workdaystudent::create_update_msuser($nsuser, null);
                    }
                }
            }
        }
        return true;
    }

    public static function wds_get_faculty_enrollments($period) {
        global $DB;

	$sql = "SELECT tenr.id AS enrollment_id,
            sec.id AS sectionid,
            sec.academic_period_id AS periodid,
            c.id AS courseid,
            u.id AS userid,
            tenr.universal_id,
            cou.course_subject_abbreviation AS department,
            cou.course_number,
            sec.section_number,
            CONCAT(
                cou.course_subject_abbreviation, ' ',
                cou.course_number, ' ',
                sec.section_number
            ) AS groupname,
            tenr.role,
            tenr.prevrole,
            tenr.status AS moodle_enrollment_status,
            tenr.prevstatus AS moodle_prev_status
            FROM {course} c
                INNER JOIN {enrol_wds_sections} sec
                    ON sec.idnumber = c.idnumber
                    AND sec.moodle_status = c.id
                INNER JOIN {enrol_wds_courses} cou
                    ON cou.course_listing_id = sec.course_listing_id
                INNER JOIN {enrol_wds_teacher_enroll} tenr
                    ON sec.section_listing_id = tenr.section_listing_id
                INNER JOIN {enrol_wds_teachers} tea
                    ON tea.universal_id = tenr.universal_id
                INNER JOIN {user} u
                    ON u.id = tea.userid
                    AND u.idnumber = tea.universal_id
            WHERE sec.controls_grading = 1
                AND tenr.status IN ('enroll', 'unenroll')
                AND sec.academic_period_id = '$period->academic_period_id'
            GROUP BY tenr.id
            ORDER BY c.id ASC, tenr.id ASC";

        $enrollments = $DB->get_records_sql($sql);

        return $enrollments;
    }

    public static function wds_get_student_enrollments($period, $courseid = null) {
        global $DB;

        // Build out the parms.
        $parms = [];

        // Add the academic period id parm.
        $parms['apid'] = $period->academic_period_id;

        $reprocesssection = '';

        // If we have a courseid, figure shit out.
        if (!is_null($courseid)) {

            // The courseid parm.
            $parms['courseid'] = $courseid;

            // Check if the $courseid consists of only digits.
            if (ctype_digit((string) $courseid)) {

                // Build out the reprocessectionsql.
                $reprocesssection = ' AND c.id = :courseid';
            } else {

                // Build out the reprocessectionsql.
                $reprocesssection = ' AND sec.course_section_definition_id = :courseid';

            }
        }

        $sql = "SELECT stuenr.id AS enrollment_id,
                stu.userid AS userid,
                stuenr.universal_id AS student_id,
                c.id AS courseid,
                cou.course_subject_abbreviation AS department,
                cou.course_subject AS department_desc,
                cou.course_number,
                sec.section_number,
                sec.idnumber AS section_idnumber,
                stuenr.status AS moodle_enrollment_status,
                stuenr.prevstatus AS moodle_prev_status,
                stuenr.registered_date AS wds_regdate,
                tenr.universal_id AS primary_id
            FROM {enrol_wds_sections} sec
                INNER JOIN {enrol_wds_courses} cou
                    ON cou.course_listing_id = sec.course_listing_id
                INNER JOIN {course} c
                    ON c.idnumber = sec.idnumber
                    AND sec.idnumber IS NOT NULL
                INNER JOIN {enrol_wds_student_enroll} stuenr
                    ON sec.section_listing_id = stuenr.section_listing_id
                INNER JOIN {enrol_wds_teacher_enroll} tenr
                    ON tenr.section_listing_id = sec.section_listing_id
                    AND tenr.role = 'primary'
                LEFT JOIN {enrol_wds_students} stu
                    ON stu.universal_id = stuenr.universal_id
            WHERE sec.academic_period_id = :apid
                AND sec.idnumber IS NOT NULL
                AND sec.controls_grading = 1
                AND stuenr.status IN ('enroll', 'unenroll')
                $reprocesssection
            GROUP BY stuenr.id
            ORDER BY sec.section_listing_id ASC";

            $enrollments = $DB->get_records_sql($sql, $parms);

            return $enrollments;
    }

    public static function get_wds_groups($courseid, $userid, $periodid) {
        global $DB;

        $sql = "SELECT g.id AS groupid,
                g.name AS groupname
            FROM mdl_course c
                INNER JOIN mdl_enrol_wds_sections sec
                    ON sec.idnumber = c.idnumber
                    AND sec.moodle_status = c.id
                INNER JOIN mdl_enrol_wds_courses cou
                    ON cou.course_listing_id = sec.course_listing_id
                INNER JOIN mdl_enrol_wds_teacher_enroll tenr
                    ON sec.section_listing_id = tenr.section_listing_id
                INNER JOIN mdl_enrol_wds_teachers tea
                    ON tea.universal_id = tenr.universal_id
                INNER JOIN mdl_groups g
                    ON g.courseid = c.id
                    AND g.name = CONCAT(
                        cou.course_subject_abbreviation, ' ',
                        cou.course_number, ' ',
                        sec.section_number
                    )
                INNER JOIN mdl_user u
                    ON u.id = tea.userid
                    AND u.idnumber = tea.universal_id
                INNER JOIN mdl_groups_members gm
                    ON g.id = gm.groupid
                    AND gm.userid = u.id
            WHERE sec.controls_grading = 1
                AND c.id = $courseid
                AND u.id = $userid
                AND sec.academic_period_id = '$periodid'
            GROUP BY g.id
            ORDER BY c.id ASC, tenr.id ASC";

        $fgroups = $DB->get_records_sql($sql);

        return $fgroups;
    }

    public static function wds_create_course_group($courseid, $groupname) {
        global $CFG;

        // We need this to mess with groups.
        require_once($CFG->dirroot . '/group/lib.php');

        // We should always have a group, but if we don't, create it.
        self::dtrace("Group: $groupname not found in course: $courseid, creating it,");

        // Build out the group object.
        $group = new stdClass();

        // ID of the course where the group will be created.
        $group->courseid = $courseid;

        // Set the group name.
        $group->name = $groupname;
        $group->description = $groupname;
        $group->timecreated = time();
        $group->timemodified = time();

        // Create the group.
        $groupid = groups_create_group($group);
        self::dtrace(" Course group $groupname with id: $groupid added to course id: $courseid.");

        return $groupid;
    }

    public static function wds_create_enrollment_instance($courseid) {
        global $DB;

        self::dtrace("No enrollment instance for course: " .
            "$courseid. Creating it.");

        // Build out the enrollment instance object.
        $enrol = new stdClass();
        $enrol->enrol = 'workdaystudent';
        $enrol->courseid = $courseid;
        $enrol->status = 0;
        $enrol->sortorder = 0;
        $enrol->timecreated = time();
        $enrol->timemodified = time();

        // Insert new instance into DB.
        $instanceid = $DB->insert_record('enrol', $enrol, true);

        // Fetch the newly created instance.
        $instance = $DB->get_record('enrol', ['id' => $instanceid]);
        self::dtrace("Enrollment instance created for course: $courseid.");

        // Return the instance.
        return $instance;
    }

    public static function wds_course_has_materials($courseid) {
        global $DB;

        // Get the default News Forum / Announcements. We may have more than one!!!
        $newsforums = $DB->get_records('forum', ['course' => $courseid, 'type' => 'news']);

        // Get all course modules, excluding the default forum, if it exists.
        $sql = "SELECT cm.id
            FROM {course_modules} cm
            JOIN {modules} m ON cm.module = m.id
                AND cm.deletioninprogress = 0
            WHERE cm.course = :courseid";

        // Set the parms.
        $params = ['courseid' => $courseid];

        // Make sure we have a at least one News Forum in the course.
        if (!empty($newsforums)) {

            // Break out the data into sql and corresponding parms.
            list($forumidsql, $forumparams) = $DB->get_in_or_equal(
                array_keys($newsforums),
                SQL_PARAMS_NAMED
            );

            // This gets weird if we have one or more than one.
            if (count($newsforums) > 1) {

                // Add this to the SQL to ignore the News Forums from above.
                $sql .= " AND (cm.instance NOT $forumidsql)";
            } else {

                // Add this to the SQL to ignore the News Forum from above.
                $sql .= " AND (cm.instance !$forumidsql)";
            }

            // Augment the parms with the stuff generated above.
            $params = array_merge($params, $forumparams);
        }

        // Check to see if we have anything.
        $extramaterials = $DB->record_exists_sql($sql, $params);

        $gsql = "SELECT COUNT(gg.id)
            FROM {grade_grades} gg
            INNER JOIN {grade_items} gi ON gg.itemid = gi.id
            WHERE gi.courseid = :courseid";

        $gradecount = $DB->count_records_sql($gsql, ['courseid' => $courseid]);

        $ghsql = "SELECT COUNT(gh.id)
            FROM {grade_grades_history} gh
            INNER JOIN {grade_items} gi ON gh.itemid = gi.id
            WHERE gi.courseid = :courseid";

        $historycount = $DB->count_records_sql($ghsql, ['courseid' => $courseid]);

        $hasgrades = $gradecount > 0 || $historycount > 0;

        $emorgrades = $hasgrades ? $hasgrades : $extramaterials;

        return $emorgrades;
    }

    public static function wds_email_finder($s, $student) {

        // Set this to null so we can work with it.
        $email = null;

        // Build out the email address suffix.
        $esuffix = $s->campusname . '_Email';

        // If the default suffix does not exist, look for others.
        if (isset($student->$esuffix)) {

            // We have a default email. Grab it like you want it.
            $email = isset($student->$esuffix) ? $student->$esuffix : null;
        } else {

            // Log that email is borked.
            self::dtrace(
                'We have a non-default or missing email for ' .
                $student->Universal_Id . ' - ' .
                $student->First_Name . ' ' .
                $student->Last_Name . '.'
            );

            // We do not have a default suffix, build one out based on institution.
            $esuffix = self::get_suffix_from_institution($student) . '_Email';

            // If the default suffix does not exist, look for others.
            if (isset($student->$esuffix)) {

                // Set email accordingly.
                $email = isset($student->$esuffix) ? $student->$esuffix : null;
            } else {

                // Log that email is borked.
                mtrace(
                    'We have a missing email for ' .
                    $student->Universal_Id . ' - ' .
                    $student->First_Name . ' ' .
                    $student->Last_Name . '.'
                );
            }
        }

        // This should either be a real email or null.
        return $email;
    }

    /**
     * Handles instructor changes for a section, properly unenrolling old instructors
     * and managing course shells.
     *
     * @param object $section The section object from Workday
     * @param object $existingsection The existing section from the database
     * @return bool Success status
     */
    public static function handle_instructor_change($section, $existingsection) {
        global $DB;

        // Check if PMI has changed.
        if (!isset($section->PMI_Universal_ID) || !isset($existingsection->id)) {
            return false;
        }

        // Get the old primary instructor for this section.
        $sql = "SELECT tea.*, tenr.id AS enrollment_id
                FROM {enrol_wds_teacher_enroll} tenr
                JOIN {enrol_wds_teachers} tea ON tea.universal_id = tenr.universal_id
                WHERE tenr.section_listing_id = :sectionid
                AND tenr.role = 'primary'";

        $oldinstructor = $DB->get_record_sql($sql, ['sectionid' => $section->Section_Listing_ID]);

        // If no old instructor or same instructor, no change needed.
        if (!$oldinstructor || $oldinstructor->universal_id === $section->PMI_Universal_ID) {
            return false;
        }

        // We have a change in PMI. Handle it.
        workdaystudent::dtrace("PMI change detected for section {$section->Section_Listing_ID}
            Old: {$oldinstructor->universal_id}, New: {$section->PMI_Universal_ID}");

        // Get the course ID if already created.
        $courseid = null;
        if (!empty($existingsection->moodle_status) && $existingsection->moodle_status !== 'pending') {
            $courseid = $existingsection->moodle_status;
        }

        // If no course created yet, just update the enrollment records.
        if (empty($courseid)) {

            // Set old instructor to unenroll.
            $oldinstructor->status = 'unenroll';
            $oldinstructor->prevstatus = 'enrolled';
            $oldinstructor->prevrole = 'primary';

            // Update enrollment record.
            workdaystudent::insert_update_teacher_enrollment(
                $section->Section_Listing_ID,
                $oldinstructor->universal_id,
                'teacher', // Demote from primary.
                'unenroll'
            );

            return true;
        }

        // Course exists, need to handle proper transfer.
        $course = $DB->get_record('course', ['id' => $courseid]);
        if (!$course) {
            return false;
        }

        // Check if course has materials or grades.
        $hasmaterials = workdaystudent::wds_course_has_materials($courseid);

        // Get enrollment instance.
        $instance = $DB->get_record('enrol',
            ['courseid' => $courseid, 'enrol' => 'workdaystudent']);

        if (!$instance) {
            $instance = workdaystudent::wds_create_enrollment_instance($courseid);
        }

        // Get the plugin.
        $enrollplugin = enrol_get_plugin('workdaystudent');

        // Handle old instructor's groups.
        $groupname = $DB->get_field_sql(
            "SELECT g.name FROM {groups} g
             JOIN {groups_members} gm ON g.id = gm.groupid
             WHERE g.courseid = :courseid AND gm.userid = :userid
             LIMIT 1",
            ['courseid' => $courseid, 'userid' => $oldinstructor->userid]
        );

        if ($groupname) {
            $group = $DB->get_record('groups', ['courseid' => $courseid, 'name' => $groupname]);
            if ($group) {

                // Handle student unenrollments for this group.
                enrol_workdaystudent::wds_unenroll_group_members($group->id, $oldinstructor);

                // Remove old instructor from group.
                groups_remove_member($group->id, $oldinstructor->userid);

                // Delete the now empty group.
                groups_delete_group($group->id);
            }
        }

        if (!$hasmaterials) {

            // Course has no materials, unenroll instructor and delete course.
            $enrollplugin->unenrol_user($instance, $oldinstructor->userid);

            // Update the section record to disassociate from this course.
            $sectionrecord = new stdClass();
            $sectionrecord->id = $existingsection->id;
            $sectionrecord->idnumber = null;
            $sectionrecord->moodle_status = 'pending';
            $DB->update_record('enrol_wds_sections', $sectionrecord);

            // Delete the course if truly empty.
            delete_course($courseid, false);

            workdaystudent::dtrace("Deleted empty course {$courseid} as part of PMI change.");
        } else {

            // Course has materials or grades, just unenroll the instructor but keep course.
            $enrollplugin->unenrol_user($instance, $oldinstructor->userid);
            workdaystudent::dtrace("Kept course {$courseid} with materials during PMI change.");

            // IMPORTANT: Unset the course's idnumber to prevent conflicts with new shells.
            $courseupdate = new stdClass();
            $courseupdate->id = $courseid;

    // TODO: Do not do this if there are sections still being taught in this shell.
            $courseupdate->idnumber = '';
            $DB->update_record('course', $courseupdate);

            // Update the section record to disassociate from this course.
            $sectionrecord = new stdClass();
            $sectionrecord->id = $existingsection->id;
            $sectionrecord->idnumber = null;
            $sectionrecord->moodle_status = 'pending';
            $DB->update_record('enrol_wds_sections', $sectionrecord);

            workdaystudent::dtrace("Kept course {$courseid} with materials during PMI change. Course idnumber cleared.");
        }

        // Update enrollment record.
        workdaystudent::insert_update_teacher_enrollment(
            $section->Section_Listing_ID,
            $oldinstructor->universal_id,
            'teacher',
            'unenrolled'
        );

        return true;
    }

    /**
     * Reprocesses instructor enrollments for a specific course.
     *
     * @param int $courseid The course ID to reprocess
     * @return bool Success status
     */
    public static function reprocess_instructor_enrollments($courseid) {
        global $DB;

        // Get all sections for this course.
        $sections = $DB->get_records('enrol_wds_sections', ['moodle_status' => $courseid]);

        if (empty($sections)) {
            mtrace("No sections found for course ID: $courseid");
            return false;
        }

        mtrace("Starting instructor enrollment reprocessing for course ID: $courseid");

        foreach ($sections as $section) {

            // Get the section details from Workday.
            $s = workdaystudent::get_settings();
            $parms = ['Course_Section_Definition_ID' => $section->course_section_definition_id];
            $updatedsections = workdaystudent::get_sections($s, $parms);

            if (empty($updatedsections)) {
                mtrace("Could not fetch updated section data for: $section->section_listing_id");
                continue;
            }

            $updatedsection = reset($updatedsections);

            // Check and handle instructor changes.
            workdaystudent::handle_instructor_change($updatedsection, $section);

            // Update the section record.
            workdaystudent::insert_update_section($updatedsection);
        }

        // Refresh faculty enrollments after processing.
        $periods = workdaystudent::get_specified_period($courseid);
        foreach ($periods as $period) {
            $enrollments = workdaystudent::wds_get_faculty_enrollments($period);
            enrol_workdaystudent::wds_bulk_faculty_enrollments($enrollments);
        }

        mtrace("Completed instructor enrollment reprocessing for course ID: $courseid");

        return true;
    }

    /**
     * Identifies and fixes duplicate course idnumbers by clearing idnumbers
     * for courses no longer associated with active teacher enrollments.
     *
     * @return array Statistics about fixed courses
     */
    public static function fix_duplicate_course_idnumbers() {
        global $DB;

        $stats = [
            'duplicates_found' => 0,
            'courses_fixed' => 0,
        ];

        mtrace("Searching for courses with duplicate idnumbers...");

        // Find courses with duplicate idnumbers (excluding empty idnumbers).
        $sql = "SELECT idnumber, COUNT(*) as count
                FROM {course}
                WHERE idnumber != ''
                GROUP BY idnumber
                HAVING COUNT(*) > 1";

        $duplicates = $DB->get_records_sql($sql);
        $stats['duplicates_found'] = count($duplicates);

        if (empty($duplicates)) {
            mtrace("No duplicate course idnumbers found.");
            return $stats;
        }

        mtrace("Found " . $stats['duplicates_found'] . " duplicate course idnumbers.");

        // Process each duplicate idnumber.
        foreach ($duplicates as $duplicate) {
            $idnumber = $duplicate->idnumber;
            mtrace("Processing duplicate idnumber: $idnumber");

            // Get all courses with this idnumber.
            $courses = $DB->get_records('course', ['idnumber' => $idnumber]);

            foreach ($courses as $course) {

                // Check if this course is still linked in the sections table.
                $islinked = $DB->record_exists('enrol_wds_sections', [
                    'idnumber' => $idnumber,
                    'moodle_status' => $course->id
                ]);

                // If not linked in sections table, check active teacher enrollments.
                if (!$islinked) {

                    // Get WDS enrollment instance for this course.
                    $instance = $DB->get_record('enrol', [
                        'courseid' => $course->id,
                        'enrol' => 'workdaystudent'
                    ]);

                    if ($instance) {

                        // Check if there are any active teachers enrolled through this instance.
                        $activeteachers = $DB->get_records_sql(
                            "SELECT ue.*
                             FROM {user_enrolments} ue
                             JOIN {enrol_wds_teacher_enroll} tenr
                                 ON tenr.universal_id = (
                                     SELECT tea.universal_id
                                     FROM {enrol_wds_teachers} tea
                                     WHERE tea.userid = ue.userid
                                 )
                             WHERE ue.enrolid = :enrolid
                             AND tenr.status IN ('enroll', 'enrolled')",
                            ['enrolid' => $instance->id]
                        );

                        if (empty($activeteachers)) {

                            // No active teachers, clear the idnumber.
                            $courseupdate = new stdClass();
                            $courseupdate->id = $course->id;
                            $courseupdate->idnumber = '';
                            $DB->update_record('course', $courseupdate);

                            mtrace("  Cleared idnumber for course ID: {$course->id}, shortname: {$course->shortname}");
                            $stats['courses_fixed']++;
                        }
                    } else {

                        // No WDS enrollment instance, clear the idnumber.
                        $courseupdate = new stdClass();
                        $courseupdate->id = $course->id;
                        $courseupdate->idnumber = '';
                        $DB->update_record('course', $courseupdate);

                        mtrace("  Cleared idnumber for course ID: {$course->id}, shortname: {$course->shortname} (no WDS enrolment)");
                        $stats['courses_fixed']++;
                    }
                } else {
                    mtrace("  Course ID: {$course->id} still linked in sections table, keeping idnumber");
                }
            }
        }

        mtrace("Fixed {$stats['courses_fixed']} courses with duplicate idnumbers.");
        return $stats;
    }
}

class wdscronhelper {

    public static function cronunits() {
        global $CFG;

        // Get settings. We have to do this several times as I overload them.
        $s = workdaystudent::get_settings();

        // Begin processing units.
        mtrace(" Fetching units from webserice.");

        // Set the satart time for the units fetch.
        $unitstart = microtime(true);

        // Fetch units.
        $units = workdaystudent::get_units($s);

        // How many units did we grab.
        $numunits = count($units);

        // Set the end time for the units fetch.
        $unitsend = microtime(true);

        // Calculate the units fetch time.
        $unitselapsed = round($unitsend - $unitstart, 2);

        mtrace(" Fetched $numunits units from webserice " .
            "in $unitselapsed seconds.");

        mtrace(" Processing $numunits units from webserice.");

        // Set the satart time for the units processing.
        $unitsptart = microtime(true);

        // Loop through the units.
        foreach ($units as $unit) {

            // Process the units.
            $academicunitid = workdaystudent::insert_update_unit($s, $unit);
        }

        // Set the end time for the units processing.
        $unitspend = microtime(true);

        if ($CFG->debugdisplay == 1) {
            mtrace(" Processed $numunits units in $unitselapsed seconds.");
        } else {
            mtrace("\n Processed $numunits units in $unitselapsed seconds.");
        }

        return true;
    }

    public static function cronperiods() {
        global $CFG;

        // Reset the counter. I hate this but I hate the weird logs more.
        workdaystudent::resetdtc();

        // Get settings. We have to do this several times as I overload them.
        $s = workdaystudent::get_settings();

        // Set the period processing start time.
        $periodstart = microtime(true);

        // Get the local academic units.
        $lunits = workdaystudent::get_local_units($s);

        // Set up the date parms.
        $parms = workdaystudent::get_dates();

        // Set these up for later.
        $processedunits = count($lunits);
        $numperiods = 0;
        $totalperiods = 0;

        // Loop through all the the  units.
        foreach($lunits as $unit) {

            mtrace("  Begin processing periods for $unit->academic_unit_code - " .
                "$unit->academic_unit_id: $unit->academic_unit.");

            // In case something stupid happens, only process institutional units.
            if ($unit->academic_unit_subtype == "Institution") {

                // Add the relavent options to the date parms.
                $parms['Institution!Academic_Unit_ID'] = $s->campus;
                $parms['format'] = 'json';

                // Build the url into settings.
                $s = workdaystudent::buildout_settings($s, "periods", $parms);

                // Get the academic periods.
                $periods = workdaystudent::get_data($s);

                $numperiods = count($periods);
                $totalperiods = $totalperiods + $numperiods;

                foreach ($periods as $period) {
                    $indent = "   ";
                    workdaystudent::dtrace("$indent" .
                        "Processing $period->Academic_Period_ID: " .
                        "$period->Name for $unit->academic_unit_id: " .
                        "$unit->academic_unit.", $indent);

                    // Get ancillary dates for census and post grades.
                    $pdates = workdaystudent::get_period_dates($s, $period);

                    // Check to see if we have a matching period.
                    $ap = workdaystudent::insert_update_period($s, $period);

                    foreach ($pdates as $pdate) {

                        // Set the academic period id to the pdate.
                        $pdate->academic_period_id = $period->Academic_Period_ID;

                        // Check to see if we have a matching period date entry.
                        $date = workdaystudent::insert_update_period_date($s, $pdate);
                    }
                    workdaystudent::dtrace("$indent" .
                        "Finished processing $period->Academic_Period_ID: " .
                        "$period->Name for $unit->academic_unit_id: " .
                        "$unit->academic_unit.", $indent);
                }
            }

            if ($CFG->debugdisplay == 1) {
                mtrace("  Finished processing $numperiods periods for " .
                    "$unit->academic_unit_id: $unit->academic_unit.");
            } else {
                mtrace("\n  Finished processing $numperiods periods for " .
                    "$unit->academic_unit_id: $unit->academic_unit.");
            }
        }

        $periodsend = microtime(true);
        $periodstime = round($periodsend - $periodstart, 2);
        mtrace(" Finished processing $totalperiods periods across " .
            "$processedunits units in $periodstime seconds.");

        return true;
    }

    public static function cronprograms() {

        // Set the start time.
        $timestarted = microtime(true);

        // Get settings.
        $s = workdaystudent::get_settings();

        mtrace("  Fetching programs of study.");

        // Get the programs from the webservice.
        $programs = workdaystudent::get_programs($s);

        // Count them.
        $numgrabbed = count($programs);

        // Gett the time it took to fetch them and log it.
        $gstime = round(microtime(true) - $timestarted, 3);
        mtrace("    Took $gstime seconds to fetch $numgrabbed " .
            "remote programs of study.");

        // Set the update start time.
        $timeustarted = microtime(true);

        // Insert or update the programs.
        $pgms = workdaystudent::insert_update_programs($programs);

        // Set the time it took to insert and update them.
        $ugstime = round(microtime(true) - $timeustarted, 3);

        // Set the time it took to run the entire process.
        $uttime = round(microtime(true) - $timestarted, 3);

        if (is_array($pgms)) {

            // Get a program count.
            $pgmcount = count($pgms);

            mtrace("\n    Took $ugstime seconds to insert or " .
                "update $pgmcount programs of study.");
        } else {
            mtrace("\n    Updating $numgrabbed programs " .
                "of study failed.");
        }

        mtrace("  Took $uttime seconds to complete the fetch " .
            "and update $pgmcount programs.");

        return true;
    }

    public static function croncourses() {

        // Set the start time.
        $timestarted = microtime(true);

        mtrace("  Fetching courses.");

        // Get settings.
        $s = workdaystudent::get_settings();

        // Get our all campus setting if it's there.
        $all = isset($s->allcampuses) ? $s->allcampuses : false;

        if ($all) {

            // If we want to grab all campuses.

            // unset($s->campus);
        }

        // Gete the courses.
        $courses = workdaystudent::get_wd_courses($s);

        // Sort the courses.
        $courses = workdaystudent::sort_courses($courses);

        // Count them.
        $numgrabbed = count($courses);

        // Get the time it took to fetch them and log it.
        $gstime = round(microtime(true) - $timestarted, 3);

        mtrace("    Took $gstime seconds to fetch " .
            "$numgrabbed remote courses.");

        // Set the update start time.
        $timeustarted = microtime(true);

        // Build the icourses storage array.
        $icourses = [];

        // Loop through the courses.
        foreach ($courses as $course) {

            // Identify fake courses.
            $faker = workdaystudent::id_fake_courses($course);

            // Remove the fakes.
            if (isset($faker[0])) {
                unset($course);
                continue;
            }

            // Insert or update course data as needed.
            $icourse = workdaystudent::insert_update_course($s, $course);

            $icourses[] = $icourse;
        }

        // Set the time it took to insert and update them.
        $ugstime = round(microtime(true) - $timeustarted, 3);

        // Set the time it took to run the entire process.
        $uttime = round(microtime(true) - $timestarted, 3);

        mtrace("\n    Took $ugstime seconds to insert " .
            "or update $numgrabbed courses.");
        mtrace("  Took $uttime seconds to complete the " .
            "fetch and update $numgrabbed courses.");

        return $icourses;
    }

    public static function cronsections() {

        // Get settings.
        $s = workdaystudent::get_settings();

        $periods = workdaystudent::get_current_periods($s);

        $numgrabbed = 0;

        foreach($periods as $period) {

            // Set upo the parameter array.
            $parms = [];

            // Add the academic period id.
            $parms['Academic_Period!Academic_Period_ID'] = $period->academic_period_id;

            // Set up some timing.
            $grabstart = microtime(true);

            // Get the sections.
            $sections = workdaystudent::get_sections($s, $parms);

            // Count how many sections we grabbed for this period.
            $numgrabbedperiod = count($sections);

            // Add them up in a self referential variable.
            $numgrabbed = $numgrabbedperiod + $numgrabbed;

            // Time how long grabbing the data from the WS took.
            $grabend = microtime(true);
            $grabtime = round($grabend - $grabstart, 2);
            mtrace("\n  Fetched $numgrabbedperiod sections from " .
                "$period->academic_period_id in $grabtime seconds. Processing.");

            // Set up some timing.
            $processstart = microtime(true);

            // Loop through the sections.
            foreach ($sections as $section) {

                // Insert or update this section.
                $sec = workdaystudent::insert_update_section($section);

                // If we have section components, add / update the schedule data.
                if (isset($section->Meeting_Patterns) || isset($section->Section_Components)) {

                    // Because some people cannot consistently set shit up.
                    if (isset($section->Meeting_Patterns)) {

                        // Set this for easier use.
                        $mps = $section->Meeting_Patterns;
                    } else {

                        // Set this for easier use.
                        $mps = $section->Section_Components;
                    }

                    // Check to see if we have more than one meeting patterns.
                    if (str_contains($mps, ';')) {

                        // Split into two (or more) meeting patterns.
                        $mpsa = array_map('trim', explode(';', $mps));

                    // We do not have more than one meeting pattern.
                    } else {

                        // Return the original string as a single-item array.
                        $mpsa = [trim($mps)];
                    }

                    // Set up an empty array for this.
                    $schedules = [];

                    // Loop through the meeting patterns array.
                    foreach ($mpsa as $mp) {

                        // Process the section schedule for this meeting pattern.
                        $schedule = workdaystudent::process_section_schedule($section, $mp);

                        // Merge this shit together.
                        $schedules = array_merge($schedules, $schedule);
                    }

                    // Add these meeting patterns to the DB.
                    $sectionschedule = workdaystudent::wds_store_schedules($section, $schedules);
                }

                // If we do not have an instructor, let us know.
                if (!isset($section->Instructor_Info)) {
                    workdaystudent::dtrace("    - No instructors in " .
                        "$section->Section_Listing_ID.");

                    // We don't have any instructors. Unenroll accordingly.
                    $enrollment = workdaystudent::insert_update_teacher_enrollment(
                        $section->Section_Listing_ID,
                        $tid = null,
                        $role = null, 'unenroll');

                // If we have multiple instructors listed, deal with it.
                } else if (count($section->Instructor_Info) > 1) {
                    workdaystudent::dtrace("    - More than 1 instructor " .
                        "in $section->Section_Listing_ID.");

                    // Loop through the instructors.
                    foreach ($section->Instructor_Info as $teacher) {

                        // Set some variables for later use.
                        $secid = $section->Section_Listing_ID;
                        $tid = $teacher->Instructor_ID;
                        $pmi = isset($section->PMI_Universal_ID) ?
                            $section->PMI_Universal_ID :
                            null;
                        $status = 'enroll';

                        // If we have a primary instructor.
                        if (!is_null($pmi)) {
                            workdaystudent::dtrace("    Primary instructor $pmi found!");

                            // Set the role to primary if the teacher matches the pmi.
                            $role = $tid == $pmi ? 'primary' : 'teacher';

                            // Set the teacher id appropriately.
                            $tid = $tid == $pmi ? $pmi : $tid;

                        // We don't have a primary, only multiple non-primaries.
                        } else {
                            workdaystudent::dtrace("    More than one instructor " .
                                " in $secid and $tid is non-primary.");

                            // Set the role to non-primary.
                            $role = 'teacher';
                        }

                        // Update the teacher user info.
                        $iteacher = workdaystudent::create_update_iteacher(
                            $s,
                            $teacher
                        );

                        // Update the teacher enrollment db.
                        $enrollment = workdaystudent::insert_update_teacher_enrollment(
                            $secid,
                            $tid,
                            $role,
                            $status
                        );
                    }

                // We only have one instructor.
                } else {

                    // Set some variables for later use.
                    $teacher = $section->Instructor_Info[0];
                    $secid = $section->Section_Listing_ID;
                    $tid = $teacher->Instructor_ID;
                    $pmi = isset($section->PMI_Universal_ID) ? $section->PMI_Universal_ID : null;
                    $status = 'enroll';

                    // If we have a primary instructor.
                    if (!is_null($pmi)) {
                        workdaystudent::dtrace("    Primary instructor $pmi found!");

                        // Set the role to primary if the teacher matches the pmi.
                        $role = $tid == $pmi ? 'primary' : 'teacher';

                        // Set the teacher id to either the teacher id or primary id depending on what we have.
                        $tid = $tid == $pmi ? $pmi : $tid;

                    // We don't have a primary.
                    } else {
                        workdaystudent::dtrace("    Sole instructor in $secid and " .
                            "$tid is non-primary.");

                        // Set the role to non-primary.
                        $role = 'teacher';
                    }

                    // Update the teacher user info.
                    $iteacher = workdaystudent::create_update_iteacher($s, $teacher);

                    // Update the teacher enrollment db.
                    $enrollment = workdaystudent::insert_update_teacher_enrollment(
                        $secid,
                        $tid,
                        $role,
                        $status
                    );
                }
            }
        }

        // Get the current time.
        $processend = microtime(true);

        // Calculate how long this crap took and log it.
        $processtime = round($processend - $processstart, 2);
        mtrace("\n  Processing $numgrabbed sections took $processtime seconds.");
    }

    public static function crongradeschemes() {

        // Log it.
        mtrace("  Fetching and updating grading schemes.");

        // Set the time started.
        $timestarted = microtime(true);

        // Get settings.
        $s = workdaystudent::get_settings();

        // Fetch the grade schemes from the endpoint.
        $gradingschemes = workdaystudent::get_grading_schemes($s);

        // Count them.
        $numgrabbed = count($gradingschemes);

        // Caclulate how long that took.
        $gstime = round(microtime(true) - $timestarted, 3);

        // Log it.
        mtrace("    It took $gstime seconds to fetch " .
            "$numgrabbed remote grading schemes.");

        // Set the update time start.
        $timeustarted = microtime(true);

        // Update all the grading schemes.
        $gs = workdaystudent::clear_insert_grading_schemes($gradingschemes);

        // Calculate how long that took.
        $ugstime = round(microtime(true) - $timeustarted, 3);

        if (!$gs) {

            // Log it.
            mtrace("    ERROR: Something went wrong with " .
                "the updating of grading schemes.");
            mtrace("  Processing of grading schemes: complete.");

            return false;
        } else {
            $gscount = count($gs);

            // Log it.
            mtrace("\n    It took $ugstime seconds to fetch and insert $gscount " .
                "records in $numgrabbed updated grading schemes.");
            mtrace("  Processing of grading schemes: complete.");

            return true;
        }
    }

    public static function cronstudents() {

        $timestart = microtime(true);

        // Include the main Moodle config.
        require_once(__DIR__ . '/../../../config.php');

        // Get settings.
        $s = workdaystudent::get_settings();

        // Define this for later.
        $sportfield = $s->sportfield;

        // Gete the academic units.
        $periods = workdaystudent::get_current_periods($s);

        // Get and set some counts.
        $studentcounter = 0;

        // Truncate metadata because it's WAY faster.
        $truncated = workdaystudent::truncate_studentmeta();

        // Loop through the periods.
        foreach ($periods as $period) {

            mtrace("Fetching students in $period->academic_period_id.");

            // Set some things up for future.
            $athletecounter = 0;
            $numathletes = 0;

            // Set the webservice start time.
            $wsstime = microtime(true);

            // Get students.
            $students = workdaystudent::get_students($s,
                $periodid = $period->academic_period_id,
                $studentid = '');

            // Set the webservice end time.
            $wsetime = microtime(true);

            // Calculate how long the webservice took to connect and return data.
            $wselapsed = round($wsetime - $wsstime, 2);

            mtrace("Beginning the process of populating the " .
                "interstitial student db for $period->academic_period_id.");

            // IF we get some data, do all the things.
            if (is_array($students)) {

                // How many students did we get?
                $records = count($students);

                // Set up the sports array.
                $sports = [];
                mtrace("It took $wselapsed seconds to pull $records " .
                    "students in $period->academic_period_id from the webservice.");

                // Loop through the students and insert / update their data.
                foreach ($students as $student) {

                    $email = workdaystudent::wds_email_finder($s, $student);

                    // GTFO if we don't have a UID or email.
                    if (!isset($student->Universal_Id) || is_null($email)) {

                        // Set these for logging.
                        $uid = isset($student->Universal_Id) ?
                            $student->Universal_Id :
                            "Missing UID";

                        $email = isset($email) && !is_null($email) ?
                            $email :
                            "Missing Email";

                        // Log that something vital was missing.
                        mtrace("\nERROR: Missing either UID: $uid or email: " .
                            "$email - $student->First_Name $student->Last_Name.");

                        continue;
                    }

                    // Increment the student counter.
                    $studentcounter++;

                    // Populate the interstitial DB.
                    $stu = workdaystudent::create_update_istudent($s, $student);

                    // Populate the student metadata.
                    $meta = workdaystudent::insert_all_studentmeta($s,
                        $stu,
                        $student,
                        $period);

                    // Add the above response to the number of athletes.
                    $numathletes = $numathletes + $meta;
                }

                // Count how many sports we have.
                $sportcount = count(array_unique($sports));

                // Get the end time.
                $timeend = microtime(true);

                // Calculate the elapsed time.
                $elapsed = round($timeend - $timestart, 2);

                mtrace("\nFinished populating the interstitial student db for " .
                    "$period->academic_period_id.");
                mtrace("It took $elapsed seconds to find and process $numathletes " .
                    "athletes across $studentcounter students in $s->campusname for " .
                    "$period->academic_period_id.");
            }
        }
    }

    public static function cronstuenroll($courseid = null) {

        // Include the main Moodle config.
        require_once(__DIR__ . '/../../../config.php');

        // Set up some timing.
        $processstart = microtime(true);

        // Get settings.
        $s = workdaystudent::get_settings();

        // If we are reprocessing, make sure we don't reprocess everything.
        if (!is_null($courseid)) {

            // Get the period specific to this course.
            $periods = workdaystudent::get_specified_period($courseid);
        } else {

            // Get the current periods.
            $periods = workdaystudent::get_current_periods($s);
        }

        // Get a count for later.
        $numgrabbed = count($periods);

        mtrace("Fetched $numgrabbed periods to enroll.");

        // Build the unenroll array.
        $unenrolls = [];
        $unenrolls[] = 'Dropped';
        $unenrolls[] = 'Enrollment Cancelled';
        $unenrolls[] = 'Enrollment Rescinded';
        $unenrolls[] = 'Not Approved';
        $unenrolls[] = 'Unregistered';
        $unenrolls[] = 'Withdrawn';

        // Build the enroll array.
        $enrolls = [];
        $enrolls[] = 'Enrolled';
        $enrolls[] = 'Registered';

        // Build the do nothing array.
        $donothings = [];
        $donothings[] = 'Completed';
        $donothings[] = 'Auto Drop from Waitlist on Enroll';
        $donothings[] = 'Enrolled - Pending Approval';
        $donothings[] = 'Enrolled - Pending Prerequisites';
        $donothings[] = 'Promoted';
        $donothings[] = 'Waitlist - Closed';
        $donothings[] = 'Waitlisted';
        $donothings[] = 'Waitlisted - Pending Approval';

        foreach ($periods as $period) {

            // Log that we're starting.
            mtrace("\nProcessing enrollments for $period->academic_period_id.");

            if (!is_null($courseid)) {
                $period->courseid = $courseid;
            }

            // Set some times.
            $periodstart = microtime(true);
            $enrollmentstart = $periodstart;

            // Fetch the actual enrollments for the period.
            $enrollments = workdaystudent::get_period_enrollments($s, $period, null);

            // Set some times.
            $enrollmentend = microtime(true);

            $enrollmentelapsed = round($enrollmentend - $enrollmentstart, 2);

            // Count the number of enrollments.
            $enrollmentcount = count($enrollments);

            // Log how long it took to fetch enrollments.l
            mtrace("The webservice took $enrollmentelapsed seconds to fetch " .
            "$enrollmentcount enrollments in $period->academic_period_id.");

            // Loop through the enrollments.
            foreach ($enrollments as $enrollment) {

                // Process the enrollment in question.
                $as = workdaystudent::insert_update_student_enrollment($s,
                    $enrollment,
                    $unenrolls,
                    $enrolls,
                    $donothings);
            }

            // Set some times.
            $periodend = microtime(true);
            $periodelapsed = round($periodend - $periodstart, 2);

            // Log how long it took to process and how many enrollments were processed.
            mtrace("We took $periodelapsed seconds to process " .
                "$enrollmentcount enrollments in $period->academic_period_id.");
        }

        $processend = microtime(true);
        $processtime = round($processend - $processstart, 2);

        // TODO: DEAL WITH TIMES.
        mtrace("Processing $numgrabbed periods took $processtime seconds.");
    }

    public static function cronmusers() {

        // Set the start time for monitoring how long each step takes.
        $starttime = microtime(true);

        // Build this to loop through matchers later.
        $matchers = [];
        $matchers = ['email', 'idnumber', 'username'];

        mtrace("Beginning user reconciliation.");

        // Loop through the above and insert userids for unmatched Moodle users.
        foreach($matchers as $matcher) {

            // Fetch userids from matching teachers.
            $reconciles = workdaystudent::reconcile_interstitial_users('teacher', $matcher);

            // Fetch userids from matching students.
            $reconciles = workdaystudent::reconcile_interstitial_users('student', $matcher);
        }

        // Get the time it took to complete user reconciliation.
        $urectime = round(microtime(true) - $starttime, 2);
        mtrace("User reconciliation took $urectime seconds to complete.");

        // Set the time for mass update timing.
        $mupdatestart = microtime(true);

        // Get the count of students we need to update.
        $msupdates = workdaystudent::get_potential_mstudent_updates();

        // Get the count of students we need to update.
        $mtupdates = workdaystudent::get_potential_mteacher_updates();

        // If we have updates, do them.
        if ($msupdates > 0) {

            // Update everybody. It's faster this way.
            $updated = workdaystudent::mass_mstudent_updates();

            // How long did the mass user updates take?
            $mupdatetime = round(microtime(true) - $mupdatestart, 2);

            mtrace("Mass student updates took $mupdatetime " .
                "seconds to update $msupdates users.");
        } else {
            mtrace("No mass student updates to do.");
        }

        // If we have updates, do them.
        if ($mtupdates > 0) {

            // Update everybody. It's faster this way.
            $updated = workdaystudent::mass_mteacher_updates();

            // How long did the mass user updates take?
            $mupdatetime = round(microtime(true) - $mupdatestart, 2);

            if ($updated) {
                mtrace("Mass teacher updates took $mupdatetime " .
                    "seconds to update $mtupdates users.");
            } else {
                mtrace("Mass teacher updates failed due to the reasons above.");
            }
        } else {
            mtrace("No mass teacher updates to do.");
        }

        // Set the time for new users and unmapped updates.
        $nusertime = microtime(true);

        // Get all the workday students who do not have user idnumbers.
        $nsusers = workdaystudent::get_potential_new_mstudents();

        // If we have data, do the nasty.
        if (is_array($nsusers) && count($nsusers) > 0) {

            // Here we loop through our interstitial users.
            foreach ($nsusers as $nsuser) {

                // Create and / or update users and set the userid to thier matching Moodle user id.
                $msuser = workdaystudent::create_update_msuser($nsuser, null);
            }

            // Set the time this took to run.
            $nuelapsed = ROUND(microtime(true) - $nusertime, 2);

            // Give me a count.
            $nusercount = count($nsusers);

            mtrace("New user evaluation took $nuelapsed seconds " .
                "for $nusercount users.");
        } else {
            mtrace("No new users to create or update.");
        }

        // Get all the workday students who do not have user idnumbers.
        $ntusers = workdaystudent::get_potential_new_mteachers();

        // If we have data, do the nasty.
        if (is_array($ntusers) && count($ntusers) > 0) {

            // Here we loop through our interstitial users.
            foreach ($ntusers as $ntuser) {

                // Create and / or update users and set the userid to thier matching Moodle user id.
                $mtuser = workdaystudent::create_update_msuser($ntuser, null);
            }

            // Set the time this took to run.
            $nuelapsed = ROUND(microtime(true) - $nusertime, 2);

            // Give me a count.
            $nusercount = count($ntusers);

            mtrace("New user evaluation took $nuelapsed seconds for $nusercount users.");
        } else {
            mtrace("No new users to create or update.");
        }
        $endtime = ROUND(microtime(true) - $starttime, 2);
        mtrace("User updates took $endtime seconds.");

    }

    public static function cronmcourses() {

        // Get settings.
        $s = workdaystudent::get_settings();

        $starttime = microtime(true);

        // Get the current periods.
        $periods = workdaystudent::get_current_periods($s);
        $periodcount = count($periods);
        mtrace("Creating courses for $periodcount periods.");

        // Loop through them.
        foreach ($periods as $period) {

            // Get the list of potential Moodle shells, and their sections.
            $mshells = workdaystudent::get_potential_new_mshells($s, $period);
            $mshellcount = count($mshells);
            mtrace(" Creating $mshellcount courses for $period->academic_period_id.");

            // Get some counts.
            $createdcount = 0;
            $skippedcount = 0;

            if (!empty($mshells)) {
                foreach ($mshells as $mshell) {

                    // Generate the course name.
                    $mshell->fullname = workdaystudent::process_shell_name($s, $mshell);

                    // Generate the numerical course number for threshold checks.
                    $mshell->numerical_value = workdaystudent::get_numeric_course_value($mshell);

                    // Get the faculty preferences.
                    $userprefs = workdaystudent::wds_get_faculty_preferences($mshell);

                    // Convert comma-separated sectionids string to an array of strings.
                    $sectionids = array_map('trim', explode(',', $mshell->sectionids));

                    // Cast preference arrays to sets of strings for safe comparisons.
                    $wants = array_map('strval', $userprefs->wants);
                    $unwants = array_map('strval', $userprefs->unwants);

                    // Flag to determine whether to create the shell.
                    $shouldcreate = false;

                    // Set the course number threshold.
                    $cnthreshold = $userprefs->courselimit;
                    $sdthreshold = $userprefs->createprior;

                    if ($mshell->numerical_value >= $cnthreshold) {
                        $reason = "$mshell->numerical_value > $cnthreshold.";
                    }

                    // Check each section ID.
                    foreach ($sectionids as $sectionid) {

                        // If we are NOT unwanted AND the numerical threshold is under the limit.
                        if ((!in_array($sectionid, $unwants) &&
                            $mshell->numerical_value < $cnthreshold) ||

                            // OR the section is SPECIFICALLY WANTED.
                            in_array($sectionid, $wants)) {

                            // We found a wanted section.
                            $shouldcreate = true;

                            // Once we find one section in the shell, we want to create the shell.
                            break;
                        }
                    }

                    if ($mshell->numerical_value >= $cnthreshold) {
                        $reason = "$mshell->numerical_value > $cnthreshold.";
                    }

                    if (in_array($sectionid, $unwants)) {
                        $reason = "user: $mshell->userid unwant rules.";
                    }

                    // Use the user (if they have) or site course number threshold.
                    if (!$shouldcreate) {
                        $skippedcount++;
                        workdaystudent::dtrace(
                            "$mshell->fullname not created due to $reason"
                        );
                        continue;

                    // Use the user (if they have) or site create prior threshold.
                    } else if (((int) $mshell->start_date - (86400 * $sdthreshold)) > time()) {
                            $skippedcount++;
                            workdaystudent::dtrace(                                                                                             "$mshell->fullname not created due to start date " .
                                "being farther than $sdthreshold days from now."
                            );
                        continue;

                    // The course is within the number and creation date thresholds, create it.
                    } else {
                            $createdcount++;
                            workdaystudent::dtrace("  Creating $mshell->fullname.");

                            // Create the shell.
                            $courseshell = workdaystudent::create_moodle_shell($mshell, $userprefs);
                    }
                }
            }

            $endtime = ROUND(microtime(true) - $starttime, 2);
            mtrace(" Created $createdcount / $mshellcount courses for " .
                "$period->academic_period_id in $endtime seconds.");
        }
    }

    public static function cronmenrolls($courseid = null) {
        $s = workdaystudent::get_settings();

        if (!is_null($courseid)) {

            // Get the period for this courseid.
            $periods = workdaystudent::get_specified_period($courseid);
        } else {

            // Get the current periods.
            $periods = workdaystudent::get_current_periods($s);
        }

        // Get the period count.
        $periodcount = count($periods);
        mtrace("Enrolling students for $periodcount periods.");
        foreach ($periods as $period) {

            // Get all the enrollment for this period.
            $enrollments = workdaystudent::wds_get_student_enrollments($period, $courseid);

            // Bulk enrollment.
            $wdsbulk = enrol_workdaystudent::wds_bulk_student_enrollments($enrollments);
        }
    }

    public static function cronmfenrolls() {
        $s = workdaystudent::get_settings();

        // Get the current periods.
        $periods = workdaystudent::get_current_periods($s);

        // Get the period count.
        $periodcount = count($periods);
        mtrace("Enrolling students for $periodcount periods.");
        foreach ($periods as $period) {

            // Get all the enrollment for this period.
            $enrollments = workdaystudent::wds_get_faculty_enrollments($period);

            // Bulk enrollment.
            $wdsbulk = enrol_workdaystudent::wds_bulk_faculty_enrollments($enrollments);
        }
    }

// Class end.
}

class enrol_workdaystudent extends enrol_plugin {

    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array $fields instance fields
     * @return int id of new instance, null if can not be created
     */

    public static function add_enroll_instance($course) {
        return $instance;
    }

    public static function wds_unenroll_group_members($groupid, $enrollment) {
        global $DB;

        // Get the group details.
        $group = $DB->get_record('groups', ['id' => $groupid], '*', MUST_EXIST);
        $courseid = $group->courseid;

        // Set this for later.
        $stunenrollmentcounts = [];
        $stunenrollmentcounts[$courseid] = 0;

        // Get all members of the WDS group.
        $groupmembers = $DB->get_records('groups_members', ['groupid' => $groupid]);

        // There are no students in the WDS group/section left.
        if (!$groupmembers) {
            mtrace("No students found in $group->name.");
            return $stunenrollmentcounts;
        }

        // Get enrolment instance for the course.
        $enrollmethod = $DB->get_record('enrol',
            ['courseid' => $courseid, 'enrol' => 'workdaystudent']);

        $wds = enrol_get_plugin($enrollmethod->enrol);

        // Something failed. Log and move on.
        if (!$wds) {
            mtrace("ERROR: No WDS enrollment instance for course ID: $courseid.");
            return $stunenrollmentcounts;
        }

        foreach ($groupmembers as $groupmember) {

            // Unenroll user from the course.
            $wds->unenrol_user($enrollmethod, $groupmember->userid);
            $stunenrollmentcounts[$courseid]++;

            $seupdated = workdaystudent::update_interstitial_enrollment_status(
                $enrollment,
                false,
                'unenrolled'
            );

            workdaystudent::dtrace("Unenrolled user ID: $groupmember->userid from course ID: $courseid.");
        }

        return $stunenrollmentcounts;
    }

    public static function wds_bulk_faculty_enrollments($enrollments) {
        global $CFG, $DB;

        // We need this to mess with groups.
        require_once($CFG->dirroot . '/group/lib.php');

        // Set this up for timing.
        $starttime = microtime(true);

        mtrace("Faculty enrollment into Moodle courses starting.");

        // Array to store instances already checked per course.
        $checkedcourses = [];

        // Instantiate the enrollment plugin.
        $enrollplugin = enrol_get_plugin('workdaystudent');

        // Set up our array counts.
        $enrollmentcounts = [];
        $unenrollmentcounts = [];
        $skippedcounts = [];

        // Get settings.
        $s = workdaystudent::get_settings();

        // Grab the student role specified in settings.
        $pr = $s->primaryrole;
        $npr = $s->nonprimaryrole;

        // Loop through the enrollments.
        foreach ($enrollments as $enrollment) {

            // Set these for later.
            $periodid = $enrollment->periodid;
            $courseid = $enrollment->courseid;
            $userid = $enrollment->userid;
            $roleid = $enrollment->role === 'primary' ? $pr : $npr;
            $prevrole = $enrollment->prevrole;
            $status = $enrollment->moodle_enrollment_status;
            $prevstatus = $enrollment->moodle_prev_status;

            // Check if we've already retrieved/created the instance for this course.
            if (!isset($checkedcourses[$courseid])) {

                // Try to get existing instance.
                $instance = $DB->get_record('enrol',
                    ['courseid' => $courseid, 'enrol' => 'workdaystudent']);

                // If no instance exists, create a new one.
                if (!$instance) {
                    $instance = workdaystudent::wds_create_enrollment_instance($courseid);
                }

                // Store instance in array to avoid checking again.
                $checkedcourses[$courseid] = $instance;
            } else {

                // Re-use previously retrieved instance.
                $instance = $checkedcourses[$courseid];
            }

            // Build out the expected groupname for this course enrollment.
            $groupname = $enrollment->groupname;

            // Find the group by name in the course.
            $group = $DB->get_record('groups', ['courseid' => $courseid, 'name' => $groupname]);

            // We found a matching group.
            if (isset($group->id)) {
                $groupid = $group->id;

            // We do not have a matching group in the course.
            } else {

                // We should always have a group, but if we don't, create it.
                $groupid = workdaystudent::wds_create_course_group($courseid, $groupname);
            }

            // Enrollment follows.
            if ($status == 'enroll') {

                // If we don't have any enrollments for this course, set it to 0.
                if (!isset($enrollmentcounts[$courseid])) {
                    $enrollmentcounts[$courseid] = 0;
                }

                // We're trying to enroll a skipped user.
                if (is_null($enrollment->userid)) {

                    // If we have no skipped for this course, set it to 0.
                    if (!isset($skippedcounts[$courseid])) {
                        $skippedcounts[$courseid] = 0;
                    }

                    // We have a skipped enrollment.
                    $skippedcounts[$courseid]++;

                    mtrace("\n" . 'Error! ' .
                        $enrollment->universal_id .
                        ' is missing their email, they were not enrolled in ' .
                        $periodid . ' ' .
                        $enrollment->department . ' ' .
                        $enrollment->course_number . ' ' .
                        $enrollment->section_number .
                        ' - courseid: ' .
                        $enrollment->courseid . '.');

                    continue;
                }

                // Increment enrollments for this courseid.
                $enrollmentcounts[$courseid]++;

                // Check if the user is already enrolled.
                $enrolled = $DB->get_record('user_enrolments',
                    ['enrolid' => $instance->id, 'userid' => $userid]);

                // If they are enrolled, we should have had that stored in the idb.
                if ($enrolled) {

                    // Update the interstitial enrollment record.
                    $updated = workdaystudent::update_interstitial_enrollment_status($enrollment, true);

                    // Add the user to the group in case they're not in the group.
                    groups_add_member($groupid, $userid);

                    workdaystudent::dtrace(" User id: $userid added to group id: $groupid.");

                    // Always log if a failure happens.
                    if ($updated) {
                        continue;
                    } else {
                        mtrace(' Interstitial update failed for user: ' .
                            $userid . ' in course: ' . $courseid .
                            ' with role: ' .  $roleid . '.');
                        continue;
                    }
                }

                // Do the nasty.
                $enrollplugin->enrol_user($instance, $userid, $roleid);
                workdaystudent::dtrace(" User id: $userid enrolled into course id: $instance->courseid.");

                // Add the user to the group.
                groups_add_member($groupid, $userid);

                workdaystudent::dtrace(" User id: $userid added to group id: $groupid.");

                // Update the insterstitial status.
                $updated = workdaystudent::update_interstitial_enrollment_status($enrollment, true);

                // If something goes wrong, always log it.
                if (!$updated) {
                    mtrace(' Interstitial update failed for user: ' .
                        $userid . ' in course: ' . $courseid .
                        ' with role: ' . $roleid . '.');
                }

            // Let's deal with unenrollments.
            } else if ($status == 'unenroll') {

                // Set the section to not link to the course shell.
                $section = new stdClass();
                $section->id =  $enrollment->sectionid;
                $section->idnumber = null;
                $section->moodle_status = 'pending';

                // If we don't have any unenrollments for this course, set it to 0.
                if (!isset($unenrollmentcounts[$courseid])) {
                    $unenrollmentcounts[$courseid] = 0;
                }

                // We're trying to unenroll a skipped user, which should never happen.
                if (is_null($enrollment->userid)) {

                    // If we have no skipped for this course, set it to 0.
                    if (!isset($skippedcounts[$courseid])) {
                        $skippedcounts[$courseid] = 0;
                    }

                    // We have a skipped unenrollment.
                    $skippedcounts[$courseid]++;

                    mtrace("\n" . 'Error! ' .
                        $enrollment->student_id .
                        ' is missing their email, they were not unenrolled from ' .
                        $enrollment->department . ' ' .
                        $enrollment->course_number . ' ' .
                        $enrollment->section_number .
                        ' - courseid: ' .
                        $enrollment->courseid . '.');

                    continue;
                }

                // Increment unenrollments for this courseid.
                $unenrollmentcounts[$courseid]++;

                // Get the group this instructor enrollment belongs to.
                $fgroup = $DB->get_record('groups', ['courseid' => $enrollment->courseid,
                    'name' => $enrollment->groupname], 'id');

                // Get the all groups for the instructor for this course.
                $fgroups = workdaystudent::get_wds_groups($courseid, $userid, $periodid);

                // Move them into a single dimensional array.
                $flatgroups = array_map(
                    fn($fgroup) => property_exists($fgroup, 'groupid') ?
                    (int) $fgroup->groupid :
                    null, $fgroups
                );

                // Filter out any nulls.
                $flatgroups = array_filter($flatgroups);

                // We have more than one WDS group that the instructor is a member of.
                if (count($flatgroups) > 1 &&
                    in_array((int) $fgroup->id, $flatgroups, true)) {

                    // Remove the instructor from the WDS group.
                    groups_remove_member($groupid, $userid);

                    // Unenroll the students from this WDS group.
                    $unenrollstucount = self::wds_unenroll_group_members($groupid, $enrollment);

                    // Delete the now empty group.
                    groups_delete_group($groupid);

                // We are about to unenroll them from the last group.
                } else if (count($flatgroups) == 1 &&
                    in_array((int) $fgroup->id, $flatgroups, true)) {

                    // Check to see if the course has any non-default materials or grades.
                    $mgcheck = workdaystudent::wds_course_has_materials($courseid);

                    // The course has materials!
                    if ($mgcheck) {

                        // We're not unenrolling them, log it.
                        mtrace(" User id: $userid NOT unenrolled " .
                            "from course id: $courseid " .
                            "due to having course materials or grades history.");
                    } else {

                        // Unenroll the students from this WDS group.
                        $unenrollstucount = self::wds_unenroll_group_members($groupid, $enrollment);

                        // Unenroll the instructor from the course.
                        $enrollplugin->unenrol_user($instance, $userid);

                        // Last empty WDS groupshould be deleted.
                        groups_delete_group($groupid);

                        // Delete the abandoned course.
                        delete_course($courseid, true);
                    }
                }

                // Update the insterstitial status.
                $feupdated = workdaystudent::update_interstitial_enrollment_status($enrollment, true);
                $supdated = $DB->update_record('enrol_wds_sections', $section);

                // If something goes wrong, log it.
                if (!$feupdated || !$supdated) {
                    workdaystudent::dtrace(' Interstitial update failed for user: ' .
                        $userid . ' in course: ' . $courseid .
                        ' with role: ' . $roleid . '.');
                }
            }
        }

        // Combine enrollments and unenrollments into a single output loop.
        if (isset($unenrollstucount)) {
            $allcourses = array_unique(array_merge(
                array_keys($enrollmentcounts),
                array_keys($unenrollmentcounts),
                array_keys($unenrollstucount),
                array_keys($skippedcounts)
            ));
        } else {
            $allcourses = array_unique(array_merge(
                array_keys($enrollmentcounts),
                array_keys($unenrollmentcounts),
                array_keys($skippedcounts)
            ));
        }

        mtrace("\nEnrollment Summary Begins");

        // Let us know how it went.
        foreach ($allcourses as $coursed) {
            $enrolls = $enrollmentcounts[$coursed] ?? 0;

            if (isset($unenrollstucount)) {
                $unenrolls = $unenrollmentcounts[$coursed] + $unenrollstucount[$courseid] ?? 0;
            } else {
                $unenrolls = $unenrollmentcounts[$coursed] ?? 0;
            }

            $skipped = $skippedcounts[$coursed] ?? 0;
            mtrace(" Course $coursed had $enrolls enrollments," .
               " $unenrolls unenrollments," .
               " $skipped skipped due to missing emails.");
        }
        mtrace("Enrollment Summary Complete");

        // Get the elapsed time.
        $elapsedtime = round(microtime(true) - $starttime, 2);

        mtrace("Enrollment into Moodle courses took $elapsedtime seconds.");

        return true;
    }

    public static function wds_bulk_student_enrollments($enrollments) {
        global $CFG, $DB;

        // Set this up for timing.
        $starttime = microtime(true);

        // We need this to mess with groups.
        require_once($CFG->dirroot . '/group/lib.php');

        mtrace("Enrollment into Moodle courses starting.");

        // Array to store instances already checked per course.
        $checkedcourses = [];

        // Instantiate the enrollment plugin.
        $enrollplugin = enrol_get_plugin('workdaystudent');

        // Set up our array counts.
        $enrollmentcounts = [];
        $unenrollmentcounts = [];
        $skippedcounts = [];

        // Get settings.
        $s = workdaystudent::get_settings();

        // Grab the student role specified in settings.
        $studentrole = $s->studentrole;

        // Loop through the enrollments.
        foreach ($enrollments as $enrollment) {

            // Set these for later.
            $userid = $enrollment->userid;
            $courseid = $enrollment->courseid;
            $roleid = $studentrole;
            $status = $enrollment->moodle_enrollment_status;

            // Check if we've already retrieved/created the instance for this course.
            if (!isset($checkedcourses[$courseid])) {

                // Try to get existing instance.
                $instance = $DB->get_record('enrol',
                    ['courseid' => $courseid, 'enrol' => 'workdaystudent']);

                // If no instance exists, create a new one.
                if (!$instance) {
                    $instance = workdaystudent::wds_create_enrollment_instance($courseid);
                }

                // Store instance in array to avoid checking again.
                $checkedcourses[$courseid] = $instance;
            } else {

                // Re-use previously retrieved instance.
                $instance = $checkedcourses[$courseid];
            }

            // Build out the expected groupname for this course enrollment.
            $groupname = $enrollment->department . " " .
                $enrollment->course_number . " " . $enrollment->section_number;

            // Find the group by name in the course.
            $group = $DB->get_record('groups', ['courseid' => $courseid, 'name' => $groupname]);

            // We do not have a matching group in the course.
            if (!$group) {

                // We don't have a group, create it.
                $newgroupid = workdaystudent::wds_create_course_group($courseid, $groupname);
            }

            $groupid = isset($group->id) ? $group->id : $newgroupid;

            // Enrollment follows.

            // TODO: Go back to just enroll.

            // if ($enrollment->moodle_enrollment_status == 'enroll') {
            if ($enrollment->moodle_enrollment_status == 'enroll' ||
                $enrollment->moodle_enrollment_status == 'completed') {

                // If we don't have any enrollments for this course, set it to 0.
                if (!isset($enrollmentcounts[$courseid])) {
                    $enrollmentcounts[$courseid] = 0;
                }

                // We're trying to enroll a skipped user.
                if (is_null($enrollment->userid)) {

                    // If we have no skipped for this course, set it to 0.
                    if (!isset($skippedcounts[$courseid])) {
                        $skippedcounts[$courseid] = 0;
                    }

                    // We have a skipped enrollment.
                    $skippedcounts[$courseid]++;

                    mtrace("\n" . 'Error! ' .
                        $enrollment->student_id .
                        ' is missing their email, they were not enrolled in ' .
                        $enrollment->department . ' ' .
                        $enrollment->course_number . ' ' .
                        $enrollment->section_number .
                        ' - courseid: ' .
                        $enrollment->courseid . '.');

                    continue;
                }

                // Increment enrollments for this courseid.
                $enrollmentcounts[$courseid]++;

                // Check if the user is already enrolled.
                $enrolled = $DB->get_record('user_enrolments',
                    ['enrolid' => $instance->id, 'userid' => $userid]);

                // If they are enrolled, we should have had that stored in the idb.
                if ($enrolled) {

                    // Update the interstitial enrollment record.
                    $updated = workdaystudent::update_interstitial_enrollment_status($enrollment, false);

                    // Add the user to the group in case they're not in the group.
                    groups_add_member($groupid, $userid);
                    workdaystudent::dtrace(" User id: $userid added to group id: $groupid.");

                    // Always log if a failure happens.
                    if ($updated) {
                        continue;
                    } else {
                        mtrace(' Interstitial update failed for user: ' .
                            $userid . ' in course: ' . $courseid .
                            ' with role: ' .  $roleid . '.');
                        continue;
                    }
                }

                // Do the nasty.
                $enrollplugin->enrol_user($instance, $userid, $roleid, $enrollment->wds_regdate);
                workdaystudent::dtrace(" User id: $userid enrolled into course id: $instance->courseid.");

                // Update the insterstitial status.
                $updated = workdaystudent::update_interstitial_enrollment_status($enrollment, false);

                // If something goes wrong, always log it.
                if (!$updated) {
                    mtrace(' Interstitial update failed for user: ' .
                        $userid . ' in course: ' . $courseid .
                        ' with role: ' . $roleid . '.');
                }

                // Add the user to the group.
                groups_add_member($groupid, $userid);
                workdaystudent::dtrace(" User id: $userid added to group id: $groupid.");

            // Let's deal with unenrollments.
            } else if ($enrollment->moodle_enrollment_status == 'unenroll') {

                // If we don't have any unenrollments for this course, set it to 0.
                if (!isset($unenrollmentcounts[$courseid])) {
                    $unenrollmentcounts[$courseid] = 0;
                }

                // We're trying to unenroll a skipped user.
                if (is_null($enrollment->userid)) {

                    // If we have no skipped for this course, set it to 0.
                    if (!isset($skippedcounts[$courseid])) {
                        $skippedcounts[$courseid] = 0;
                    }

                    // We have a skipped unenrollment.
                    $skippedcounts[$courseid]++;

                    mtrace("\n" . 'Error! ' .
                        $enrollment->student_id .
                        ' is missing their email, they were not unenrolled from ' .
                        $enrollment->department . ' ' .
                        $enrollment->course_number . ' ' .
                        $enrollment->section_number .
                        ' - courseid: ' .
                        $enrollment->courseid . '.');

                    continue;
                }

                // Increment unenrollments for this courseid.
                $unenrollmentcounts[$courseid]++;

                if (!isset($s->suspend) || $s->suspend == 0) {

                    // Do the nasty.
                    $enrollplugin->unenrol_user($instance, $userid);
                    workdaystudent::dtrace(" User id: $userid unenrolled from course id: $instance->courseid.");
                } else {
                    $enrollplugin->update_user_enrol($instance->id, $userid, ENROL_USER_SUSPENDED);
                    workdaystudent::dtrace(" User id: $userid suspended from course id: $instance->courseid.");
                }

                // Update the insterstitial status.
                $updated = workdaystudent::update_interstitial_enrollment_status($enrollment, false);

                // If something goes wrong, log it.
                if (!$updated) {
                    workdaystudent::dtrace(' Interstitial update failed for user: ' .
                        $userid . ' in course: ' . $courseid .
                        ' with role: ' . $roleid . '.');
                }

            }
        }

        // Combine enrollments and unenrollments into a single output loop.
        $allcourses = array_unique(array_merge(
            array_keys($enrollmentcounts),
            array_keys($unenrollmentcounts),
            array_keys($skippedcounts)
        ));

        mtrace("\nEnrollment Summary Begins");

        // Let us know how it went.
        foreach ($allcourses as $coursed) {
            $enrolls = $enrollmentcounts[$coursed] ?? 0;
            $unenrolls = $unenrollmentcounts[$coursed] ?? 0;
            $skipped = $skippedcounts[$coursed] ?? 0;
            mtrace(" Course $coursed had $enrolls enrollments," .
               " $unenrolls unenrollments," .
               " $skipped skipped due to missing emails.");
        }
        mtrace("Enrollment Summary Complete");

        // Get the elapsed time.
        $elapsedtime = round(microtime(true) - $starttime, 2);

        mtrace("Enrollment into Moodle courses took $elapsedtime seconds.");

        return true;
    }

// Class End.
}
