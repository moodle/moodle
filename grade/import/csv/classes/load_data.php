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
 * A class for loading and preparing grade data from import.
 *
 * @package   gradeimport_csv
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * A class for loading and preparing grade data from import.
 *
 * @package   gradeimport_csv
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradeimport_csv_load_data {

    /** @var string $error csv import error. */
    protected $error;
    /** @var int $iid Unique identifier for these csv records. */
    protected $iid;
    /** @var array $headers Column names for the data. */
    protected $headers;
    /** @var array $previewdata A subsection of the csv imported data. */
    protected $previewdata;

    // The map_user_data_with_value variables.
    /** @var array $newgrades Grades to be inserted into the gradebook. */
    protected $newgrades;
    /** @var array $newfeedbacks Feedback to be inserted into the gradebook. */
    protected $newfeedbacks;
    /** @var int $studentid Student ID*/
    protected $studentid;

    // The prepare_import_grade_data() variables.
    /** @var bool $status The current status of the import. True = okay, False = errors. */
    protected $status;
    /** @var int $importcode The code for this batch insert. */
    protected $importcode;
    /** @var array $gradebookerrors An array of errors from trying to import into the gradebook. */
    protected $gradebookerrors;
    /** @var array $newgradeitems An array of new grade items to be inserted into the gradebook. */
    protected $newgradeitems;

    /**
     * Load CSV content for previewing.
     *
     * @param string $text The grade data being imported.
     * @param string $encoding The type of encoding the file uses.
     * @param string $separator The separator being used to define each field.
     * @param int $previewrows How many rows are being previewed.
     */
    public function load_csv_content($text, $encoding, $separator, $previewrows) {
        $this->raise_limits();

        $this->iid = csv_import_reader::get_new_iid('grade');
        $csvimport = new csv_import_reader($this->iid, 'grade');

        $csvimport->load_csv_content($text, $encoding, $separator);
        $this->error = $csvimport->get_error();

        // If there are no import errors then proceed.
        if (empty($this->error)) {

            // Get header (field names).
            $this->headers = $csvimport->get_columns();
            $this->trim_headers();

            $csvimport->init();
            $this->previewdata = array();

            for ($numlines = 0; $numlines <= $previewrows; $numlines++) {
                $lines = $csvimport->next();
                if ($lines) {
                    $this->previewdata[] = $lines;
                }
            }
        }
    }

    /**
     * Gets all of the grade items in this course.
     *
     * @param int $courseid Course id;
     * @return array An array of grade items for the course.
     */
    public static function fetch_grade_items($courseid) {
        $gradeitems = null;
        if ($allgradeitems = grade_item::fetch_all(array('courseid' => $courseid))) {
            foreach ($allgradeitems as $gradeitem) {
                // Skip course type and category type.
                if ($gradeitem->itemtype == 'course' || $gradeitem->itemtype == 'category') {
                    continue;
                }

                $displaystring = null;
                if (!empty($gradeitem->itemmodule)) {
                    $displaystring = get_string('modulename', $gradeitem->itemmodule).get_string('labelsep', 'langconfig')
                            .$gradeitem->get_name();
                } else {
                    $displaystring = $gradeitem->get_name();
                }
                $gradeitems[$gradeitem->id] = $displaystring;
            }
        }
        return $gradeitems;
    }

    /**
     * Cleans the column headers from the CSV file.
     */
    protected function trim_headers() {
        foreach ($this->headers as $i => $h) {
            $h = trim($h); // Remove whitespace.
            $h = clean_param($h, PARAM_RAW); // Clean the header.
            $this->headers[$i] = $h;
        }
    }

    /**
     * Raises the php execution time and memory limits for importing the CSV file.
     */
    protected function raise_limits() {
        // Large files are likely to take their time and memory. Let PHP know
        // that we'll take longer, and that the process should be recycled soon
        // to free up memory.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_EXTRA);
    }

    /**
     * Inserts a record into the grade_import_values table. This also adds common record information.
     *
     * @param object $record The grade record being inserted into the database.
     * @param int $studentid The student ID.
     * @return bool|int true or insert id on success. Null if the grade value is too high.
     */
    protected function insert_grade_record($record, $studentid) {
        global $DB, $USER, $CFG;
        $record->importcode = $this->importcode;
        $record->userid     = $studentid;
        $record->importer   = $USER->id;
        // By default the maximum grade is 100.
        $gradepointmaximum = 100;
        // If the grade limit has been increased then use the gradepointmax setting.
        if ($CFG->unlimitedgrades) {
            $gradepointmaximum = $CFG->gradepointmax;
        }
        // If the record final grade is set then check that the grade value isn't too high.
        // Final grade will not be set if we are inserting feedback.
        if (!isset($record->finalgrade) || $record->finalgrade <= $gradepointmaximum) {
            return $DB->insert_record('grade_import_values', $record);
        } else {
            $this->cleanup_import(get_string('gradevaluetoobig', 'grades', $gradepointmaximum));
            return null;
        }
    }

    /**
     * Insert the new grade into the grade item buffer table.
     *
     * @param array $header The column headers from the CSV file.
     * @param int $key Current row identifier.
     * @param string $value The value for this row (final grade).
     * @return array new grades that are ready for commiting to the gradebook.
     */
    protected function import_new_grade_item($header, $key, $value) {
        global $DB, $USER;

        // First check if header is already in temp database.
        if (empty($this->newgradeitems[$key])) {

            $newgradeitem = new stdClass();
            $newgradeitem->itemname = $header[$key];
            $newgradeitem->importcode = $this->importcode;
            $newgradeitem->importer = $USER->id;

            // Insert into new grade item buffer.
            $this->newgradeitems[$key] = $DB->insert_record('grade_import_newitem', $newgradeitem);
        }
        $newgrade = new stdClass();
        $newgrade->newgradeitem = $this->newgradeitems[$key];

        // If the user has a grade for this grade item.
        if (trim($value) != '-') {
            // Instead of omitting the grade we could insert one with finalgrade set to 0.
            // We do not have access to grade item min grade.
            $newgrade->finalgrade = $value;
            $newgrades[] = $newgrade;
        }
        return $newgrades;
    }

    /**
     * Check that the user is in the system.
     *
     * @param string $value The value, from the csv file, being mapped to identify the user.
     * @param array $userfields Contains the field and label being mapped from.
     * @return int Returns the user ID if it exists, otherwise null.
     */
    protected function check_user_exists($value, $userfields) {
        global $DB;

        $usercheckproblem = false;
        $user = null;
        // The user may use the incorrect field to match the user. This could result in an exception.
        try {
            $user = $DB->get_record('user', array($userfields['field'] => $value));
        } catch (Exception $e) {
            $usercheckproblem = true;
        }
        // Field may be fine, but no records were returned.
        if (!$user || $usercheckproblem) {
            $usermappingerrorobj = new stdClass();
            $usermappingerrorobj->field = $userfields['label'];
            $usermappingerrorobj->value = $value;
            $this->cleanup_import(get_string('usermappingerror', 'grades', $usermappingerrorobj));
            unset($usermappingerrorobj);
            return null;
        }
        return $user->id;
    }

    /**
     * Check to see if the feedback matches a grade item.
     *
     * @param int $courseid The course ID.
     * @param int $itemid The ID of the grade item that the feedback relates to.
     * @param string $value The actual feedback being imported.
     * @return object Creates a feedback object with the item ID and the feedback value.
     */
    protected function create_feedback($courseid, $itemid, $value) {
        // Case of an id, only maps id of a grade_item.
        // This was idnumber.
        if (!new grade_item(array('id' => $itemid, 'courseid' => $courseid))) {
            // Supplied bad mapping, should not be possible since user
            // had to pick mapping.
            $this->cleanup_import(get_string('importfailed', 'grades'));
            return null;
        }

        // The itemid is the id of the grade item.
        $feedback = new stdClass();
        $feedback->itemid   = $itemid;
        $feedback->feedback = $value;
        return $feedback;
    }

    /**
     * This updates existing grade items.
     *
     * @param int $courseid The course ID.
     * @param array $map Mapping information provided by the user.
     * @param int $key The line that we are currently working on.
     * @param bool $verbosescales Form setting for grading with scales.
     * @param string $value The grade value.
     * @return array grades to be updated.
     */
    protected function update_grade_item($courseid, $map, $key, $verbosescales, $value) {
        // Case of an id, only maps id of a grade_item.
        // This was idnumber.
        if (!$gradeitem = new grade_item(array('id' => $map[$key], 'courseid' => $courseid))) {
            // Supplied bad mapping, should not be possible since user
            // had to pick mapping.
            $this->cleanup_import(get_string('importfailed', 'grades'));
            return null;
        }

        // Check if grade item is locked if so, abort.
        if ($gradeitem->is_locked()) {
            $this->cleanup_import(get_string('gradeitemlocked', 'grades'));
            return null;
        }

        $newgrade = new stdClass();
        $newgrade->itemid = $gradeitem->id;
        if ($gradeitem->gradetype == GRADE_TYPE_SCALE and $verbosescales) {
            if ($value === '' or $value == '-') {
                $value = null; // No grade.
            } else {
                $scale = $gradeitem->load_scale();
                $scales = explode(',', $scale->scale);
                $scales = array_map('trim', $scales); // Hack - trim whitespace around scale options.
                array_unshift($scales, '-'); // Scales start at key 1.
                $key = array_search($value, $scales);
                if ($key === false) {
                    $this->cleanup_import(get_string('badgrade', 'grades'));
                    return null;
                }
                $value = $key;
            }
            $newgrade->finalgrade = $value;
        } else {
            if ($value === '' or $value == '-') {
                $value = null; // No grade.
            } else {
                // If the value has a local decimal or can correctly be unformatted, do it.
                $validvalue = unformat_float($value, true);
                if ($validvalue !== false) {
                    $value = $validvalue;
                } else {
                    // Non numeric grade value supplied, possibly mapped wrong column.
                    $this->cleanup_import(get_string('badgrade', 'grades'));
                    return null;
                }
            }
            $newgrade->finalgrade = $value;
        }
        $this->newgrades[] = $newgrade;
        return $this->newgrades;
    }

    /**
     * Clean up failed CSV grade import. Clears the temp table for inserting grades.
     *
     * @param string $notification The error message to display from the unsuccessful grade import.
     */
    protected function cleanup_import($notification) {
        $this->status = false;
        import_cleanup($this->importcode);
        $this->gradebookerrors[] = $notification;
    }

    /**
     * Check user mapping.
     *
     * @param string $mappingidentifier The user field that we are matching together.
     * @param string $value The value we are checking / importing.
     * @param array $header The column headers of the csv file.
     * @param array $map Mapping information provided by the user.
     * @param int $key Current row identifier.
     * @param int $courseid The course ID.
     * @param int $feedbackgradeid The ID of the grade item that the feedback relates to.
     * @param bool $verbosescales Form setting for grading with scales.
     */
    protected function map_user_data_with_value($mappingidentifier, $value, $header, $map, $key, $courseid, $feedbackgradeid,
            $verbosescales) {

        // Fields that the user can be mapped from.
        $userfields = array(
            'userid' => array(
                'field' => 'id',
                'label' => 'id',
            ),
            'useridnumber' => array(
                'field' => 'idnumber',
                'label' => 'idnumber',
            ),
            'useremail' => array(
                'field' => 'email',
                'label' => 'email address',
            ),
            'username' => array(
                'field' => 'username',
                'label' => 'username',
            ),
        );

        switch ($mappingidentifier) {
            case 'userid':
            case 'useridnumber':
            case 'useremail':
            case 'username':
                // Skip invalid row with blank user field.
                if (!empty($value)) {
                    $this->studentid = $this->check_user_exists($value, $userfields[$mappingidentifier]);
                }
            break;
            case 'new':
                $this->newgrades = $this->import_new_grade_item($header, $key, $value);
            break;
            case 'feedback':
                if ($feedbackgradeid) {
                    $feedback = $this->create_feedback($courseid, $feedbackgradeid, $value);
                    if (isset($feedback)) {
                        $this->newfeedbacks[] = $feedback;
                    }
                }
            break;
            default:
                // Existing grade items.
                if (!empty($map[$key])) {
                    $this->newgrades = $this->update_grade_item($courseid, $map, $key, $verbosescales, $value,
                            $mappingidentifier);
                }
                // Otherwise, we ignore this column altogether because user has chosen
                // to ignore them (e.g. institution, address etc).
            break;
        }
    }

    /**
     * Checks and prepares grade data for inserting into the gradebook.
     *
     * @param array $header Column headers of the CSV file.
     * @param object $formdata Mapping information from the preview page.
     * @param object $csvimport csv import reader object for iterating over the imported CSV file.
     * @param int $courseid The course ID.
     * @param bool $separatemode If we have groups are they separate?
     * @param mixed $currentgroup current group information.
     * @param bool $verbosescales Form setting for grading with scales.
     * @return bool True if the status for importing is okay, false if there are errors.
     */
    public function prepare_import_grade_data($header, $formdata, $csvimport, $courseid, $separatemode, $currentgroup,
            $verbosescales) {
        global $DB, $USER;

        // The import code is used for inserting data into the grade tables.
        $this->importcode = $formdata->importcode;
        $this->status = true;
        $this->headers = $header;
        $this->studentid = null;
        $this->gradebookerrors = null;
        $forceimport = $formdata->forceimport;
        // Temporary array to keep track of what new headers are processed.
        $this->newgradeitems = array();
        $this->trim_headers();
        $timeexportkey = null;
        $map = array();
        // Loops mapping_0, mapping_1 .. mapping_n and construct $map array.
        foreach ($header as $i => $head) {
            if (isset($formdata->{'mapping_'.$i})) {
                $map[$i] = $formdata->{'mapping_'.$i};
            }
            if ($head == get_string('timeexported', 'gradeexport_txt')) {
                $timeexportkey = $i;
            }
        }

        // If mapping information is supplied.
        $map[clean_param($formdata->mapfrom, PARAM_RAW)] = clean_param($formdata->mapto, PARAM_RAW);

        // Check for mapto collisions.
        $maperrors = array();
        foreach ($map as $i => $j) {
            if ($j == 0) {
                // You can have multiple ignores.
                continue;
            } else {
                if (!isset($maperrors[$j])) {
                    $maperrors[$j] = true;
                } else {
                    // Collision.
                    print_error('cannotmapfield', '', '', $j);
                }
            }
        }

        $this->raise_limits();

        $csvimport->init();

        while ($line = $csvimport->next()) {
            if (count($line) <= 1) {
                // There is no data on this line, move on.
                continue;
            }

            // Array to hold all grades to be inserted.
            $this->newgrades = array();
            // Array to hold all feedback.
            $this->newfeedbacks = array();
            // Each line is a student record.
            foreach ($line as $key => $value) {

                $value = clean_param($value, PARAM_RAW);
                $value = trim($value);

                /*
                 * the options are
                 * 1) userid, useridnumber, usermail, username - used to identify user row
                 * 2) new - new grade item
                 * 3) id - id of the old grade item to map onto
                 * 3) feedback_id - feedback for grade item id
                 */

                // Explode the mapping for feedback into a label 'feedback' and the identifying number.
                $mappingbase = explode("_", $map[$key]);
                $mappingidentifier = $mappingbase[0];
                // Set the feedback identifier if it exists.
                if (isset($mappingbase[1])) {
                    $feedbackgradeid = (int)$mappingbase[1];
                } else {
                    $feedbackgradeid = '';
                }

                $this->map_user_data_with_value($mappingidentifier, $value, $header, $map, $key, $courseid, $feedbackgradeid,
                        $verbosescales);
                if ($this->status === false) {
                    return $this->status;
                }
            }

            // No user mapping supplied at all, or user mapping failed.
            if (empty($this->studentid) || !is_numeric($this->studentid)) {
                // User not found, abort whole import.
                $this->cleanup_import(get_string('usermappingerrorusernotfound', 'grades'));
                break;
            }

            if ($separatemode and !groups_is_member($currentgroup, $this->studentid)) {
                // Not allowed to import into this group, abort.
                $this->cleanup_import(get_string('usermappingerrorcurrentgroup', 'grades'));
                break;
            }

            // Insert results of this students into buffer.
            if ($this->status and !empty($this->newgrades)) {

                foreach ($this->newgrades as $newgrade) {

                    // Check if grade_grade is locked and if so, abort.
                    if (!empty($newgrade->itemid) and $gradegrade = new grade_grade(array('itemid' => $newgrade->itemid,
                            'userid' => $this->studentid))) {
                        if ($gradegrade->is_locked()) {
                            // Individual grade locked.
                            $this->cleanup_import(get_string('gradelocked', 'grades'));
                            return $this->status;
                        }
                        // Check if the force import option is disabled and the last exported date column is present.
                        if (!$forceimport && !empty($timeexportkey)) {
                            $exportedtime = $line[$timeexportkey];
                            if (clean_param($exportedtime, PARAM_INT) != $exportedtime || $exportedtime > time() ||
                                    $exportedtime < strtotime("-1 year", time())) {
                                // The date is invalid, or in the future, or more than a year old.
                                $this->cleanup_import(get_string('invalidgradeexporteddate', 'grades'));
                                return $this->status;

                            }
                            $timemodified = $gradegrade->get_dategraded();
                            if (!empty($timemodified) && ($exportedtime < $timemodified)) {
                                // The item was graded after we exported it, we return here not to override it.
                                $user = core_user::get_user($this->studentid);
                                $this->cleanup_import(get_string('gradealreadyupdated', 'grades', fullname($user)));
                                return $this->status;
                            }
                        }
                    }
                    $insertid = self::insert_grade_record($newgrade, $this->studentid);
                    // Check to see if the insert was successful.
                    if (empty($insertid)) {
                        return null;
                    }
                }
            }

            // Updating/inserting all comments here.
            if ($this->status and !empty($this->newfeedbacks)) {
                foreach ($this->newfeedbacks as $newfeedback) {
                    $sql = "SELECT *
                              FROM {grade_import_values}
                             WHERE importcode=? AND userid=? AND itemid=? AND importer=?";
                    if ($feedback = $DB->get_record_sql($sql, array($this->importcode, $this->studentid, $newfeedback->itemid,
                            $USER->id))) {
                        $newfeedback->id = $feedback->id;
                        $DB->update_record('grade_import_values', $newfeedback);

                    } else {
                        // The grade item for this is not updated.
                        $insertid = self::insert_grade_record($newfeedback, $this->studentid);
                        // Check to see if the insert was successful.
                        if (empty($insertid)) {
                            return null;
                        }
                    }
                }
            }
        }
        return $this->status;
    }

    /**
     * Returns the headers parameter for this class.
     *
     * @return array returns headers parameter for this class.
     */
    public function get_headers() {
        return $this->headers;
    }

    /**
     * Returns the error parameter for this class.
     *
     * @return string returns error parameter for this class.
     */
    public function get_error() {
        return $this->error;
    }

    /**
     * Returns the iid parameter for this class.
     *
     * @return int returns iid parameter for this class.
     */
    public function get_iid() {
        return $this->iid;
    }

    /**
     * Returns the preview_data parameter for this class.
     *
     * @return array returns previewdata parameter for this class.
     */
    public function get_previewdata() {
        return $this->previewdata;
    }

    /**
     * Returns the gradebookerrors parameter for this class.
     *
     * @return array returns gradebookerrors parameter for this class.
     */
    public function get_gradebookerrors() {
        return $this->gradebookerrors;
    }
}
